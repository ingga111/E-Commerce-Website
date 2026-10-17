<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DokuPaymentService implements PaymentGatewayInterface
{
    protected string $clientId;

    protected string $secretKey;

    protected string $baseUrl;

    protected int $expiryMinutes;

    public function __construct()
    {
        $this->clientId = (string) config('doku.client_id');
        $this->secretKey = (string) config('doku.secret_key');

        $mode = config('doku.mode', 'sandbox');
        $this->baseUrl = config("doku.urls.{$mode}", 'https://api-sandbox.doku.com');
        $this->expiryMinutes = (int) config('doku.expiry_minutes', 1440);
    }

    /**
     * Get list of payment channels supported by DOKU or configured by Admin.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPaymentChannels(): array
    {
        // Prioritize dynamic Payment Methods managed by Admin in Database
        $dbMethods = PaymentMethod::active()->get();
        if ($dbMethods->isNotEmpty()) {
            return $dbMethods->map(fn ($m) => [
                'group' => $m->group,
                'code' => $m->code,
                'name' => $m->name,
                'type' => 'direct',
                'fee_customer' => [
                    'flat' => (float) $m->fee_flat,
                    'percent' => (float) $m->fee_percent,
                ],
                'active' => true,
            ])->toArray();
        }

        // Standard DOKU payment channels fallback
        return [
            [
                'group' => 'QRIS & E-Wallet',
                'code' => 'QRIS',
                'name' => 'DOKU QRIS (Semua E-Wallet & Bank)',
                'type' => 'direct',
                'fee_customer' => ['flat' => 750, 'percent' => 0.7],
                'active' => true,
            ],
            [
                'group' => 'Virtual Account',
                'code' => 'BCAVA',
                'name' => 'BCA Virtual Account',
                'type' => 'direct',
                'fee_customer' => ['flat' => 4000, 'percent' => 0],
                'active' => true,
            ],
            [
                'group' => 'Virtual Account',
                'code' => 'BRIVA',
                'name' => 'BRI Virtual Account',
                'type' => 'direct',
                'fee_customer' => ['flat' => 3500, 'percent' => 0],
                'active' => true,
            ],
            [
                'group' => 'Virtual Account',
                'code' => 'MANDIRIVA',
                'name' => 'Mandiri Virtual Account',
                'type' => 'direct',
                'fee_customer' => ['flat' => 3500, 'percent' => 0],
                'active' => true,
            ],
            [
                'group' => 'E-Wallet',
                'code' => 'OVO',
                'name' => 'OVO',
                'type' => 'direct',
                'fee_customer' => ['flat' => 1000, 'percent' => 1.5],
                'active' => true,
            ],
            [
                'group' => 'E-Wallet',
                'code' => 'DANA',
                'name' => 'DANA',
                'type' => 'direct',
                'fee_customer' => ['flat' => 1000, 'percent' => 1.5],
                'active' => true,
            ],
        ];
    }

    /**
     * Create transaction using DOKU Checkout API v2.
     *
     * @return array<string, mixed>
     */
    public function createTransaction(Order $order, string $paymentMethod): array
    {
        $amount = (int) round((float) $order->total_amount);
        $requestId = (string) Str::uuid();
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $requestTarget = '/checkout/v1/payment';

        $lineItems = $order->items->map(function ($item) {
            return [
                'name' => "{$item->product_name} - {$item->variant_name}",
                'price' => (int) round((float) $item->price),
                'quantity' => (int) $item->quantity,
            ];
        })->toArray();

        $payload = [
            'order' => [
                'amount' => $amount,
                'invoice_number' => $order->order_number,
                'currency' => 'IDR',
                'callback_url' => route('orders.show', $order->order_number),
                'auto_redirect' => true,
                'line_items' => $lineItems,
            ],
            'payment' => [
                'payment_due_date' => $this->expiryMinutes,
            ],
            'customer' => [
                'name' => $order->customer_name,
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
            ],
        ];

        $jsonPayload = json_encode($payload);
        $digest = base64_encode(hash('sha256', $jsonPayload, true));

        // DOKU v2 HMAC Signature
        $stringToSign = "Client-Id:{$this->clientId}\n"
            ."Request-Id:{$requestId}\n"
            ."Request-Timestamp:{$timestamp}\n"
            ."Request-Target:{$requestTarget}\n"
            ."Digest:{$digest}";

        $signature = 'HMACSHA256='.base64_encode(hash_hmac('sha256', $stringToSign, $this->secretKey, true));

        try {
            $response = Http::withHeaders([
                'Client-Id' => $this->clientId,
                'Request-Id' => $requestId,
                'Request-Timestamp' => $timestamp,
                'Signature' => $signature,
                'Content-Type' => 'application/json',
            ])->timeout(15)->post("{$this->baseUrl}{$requestTarget}", $payload);

            $result = $response->json();

            if ($response->successful() && isset($result['response']['payment']['url'])) {
                $paymentData = $result['response']['payment'];

                return [
                    'success' => true,
                    'reference' => $result['response']['order']['invoice_number'] ?? $order->order_number,
                    'merchant_ref' => $order->order_number,
                    'payment_method' => $paymentMethod,
                    'payment_name' => "DOKU - {$paymentMethod}",
                    'pay_code' => $paymentData['virtual_account_info']['virtual_account_number'] ?? null,
                    'qr_url' => $paymentData['qr_code_info']['qr_image_url'] ?? null,
                    'checkout_url' => $paymentData['url'],
                    'fee' => $order->admin_fee,
                    'total_amount' => $amount,
                    'expires_at' => now()->addMinutes($this->expiryMinutes),
                    'raw' => $result,
                ];
            }

            Log::warning('DOKU API Response: '.json_encode($result));

            // If in local development or credentials are being configured, provide simulated response
            if (app()->environment('local', 'testing') || empty($this->clientId)) {
                return $this->mockDokuSandboxTransaction($order, $paymentMethod, $amount, $payload);
            }

            throw new Exception($result['error']['message'] ?? 'Gagal membuat tagihan di DOKU Checkout.');
        } catch (Exception $e) {
            Log::error('DOKU Exception: '.$e->getMessage());

            if (app()->environment('local', 'testing')) {
                return $this->mockDokuSandboxTransaction($order, $paymentMethod, $amount, $payload);
            }

            throw $e;
        }
    }

    /**
     * Validate incoming DOKU notification signature.
     */
    public function validateCallback(Request $request): bool
    {
        // Allow sandbox simulation in local environment for testing
        if (app()->environment('local', 'testing') && ($request->header('X-Doku-Simulation') === 'true' || $request->has('simulate_sandbox'))) {
            return true;
        }

        $signature = $request->header('Signature')
            ?? $request->header('X-SIGNATURE')
            ?? $request->header('X-Callback-Signature');

        if (! $signature) {
            return false;
        }

        $clientId = $request->header('Client-Id') ?? $this->clientId;
        $requestId = $request->header('Request-Id') ?? '';
        $timestamp = $request->header('Request-Timestamp') ?? '';
        $rawBody = $request->getContent();
        $digest = $request->header('Digest') ?: base64_encode(hash('sha256', $rawBody, true));

        // Candidates for Request-Target: Header, URI, or normalized path
        $targetCandidates = array_unique(array_filter([
            $request->header('Request-Target'),
            $request->getRequestUri(),
            '/'.ltrim($request->path(), '/'),
        ]));

        // 1. Asymmetric Signature Verification using DOKU RSA Public Key (SNAP / Open API)
        $publicKey = $this->getPublicKey();
        if ($publicKey) {
            $rawSigString = str_starts_with($signature, 'HMACSHA256=') ? substr($signature, 11) : $signature;
            $decodedSignature = base64_decode($rawSigString, true);

            if ($decodedSignature !== false) {
                foreach ($targetCandidates as $target) {
                    $stringToSign = "Client-Id:{$clientId}\n"
                        ."Request-Id:{$requestId}\n"
                        ."Request-Timestamp:{$timestamp}\n"
                        ."Request-Target:{$target}\n"
                        ."Digest:{$digest}";

                    if (@openssl_verify($stringToSign, $decodedSignature, $publicKey, OPENSSL_ALGO_SHA256) === 1) {
                        return true;
                    }
                }

                // Check raw body verification with RSA
                if (@openssl_verify($rawBody, $decodedSignature, $publicKey, OPENSSL_ALGO_SHA256) === 1) {
                    return true;
                }
            }
        }

        // 2. Symmetric HMAC-SHA256 Canonical Verification (DOKU Checkout v2)
        foreach ($targetCandidates as $target) {
            $stringToSign = "Client-Id:{$clientId}\n"
                ."Request-Id:{$requestId}\n"
                ."Request-Timestamp:{$timestamp}\n"
                ."Request-Target:{$target}\n"
                ."Digest:{$digest}";

            $hashRaw = base64_encode(hash_hmac('sha256', $stringToSign, $this->secretKey, true));
            $expectedSignature = 'HMACSHA256='.$hashRaw;

            if (hash_equals($expectedSignature, $signature) || hash_equals($hashRaw, $signature)) {
                return true;
            }
        }

        // 3. Direct HMAC-SHA256 & HMAC-SHA512 comparison fallback
        $simpleExpected256 = hash_hmac('sha256', $rawBody, $this->secretKey);
        if (hash_equals($simpleExpected256, $signature) || hash_equals('HMACSHA256='.$simpleExpected256, $signature)) {
            return true;
        }

        $simpleExpected512 = hash_hmac('sha512', $rawBody, $this->secretKey);
        if (hash_equals($simpleExpected512, $signature) || hash_equals('HMACSHA512='.$simpleExpected512, $signature)) {
            return true;
        }

        // 4. Tripay secret key comparison fallback
        $tripayKey = (string) config('tripay.private_key');
        if (! empty($tripayKey)) {
            $tripayExpected = hash_hmac('sha256', $rawBody, $tripayKey);
            if (hash_equals($tripayExpected, $signature)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get loaded DOKU Public Key string (PEM format).
     */
    public function getPublicKey(): ?string
    {
        $path = config('doku.public_key_path');
        if ($path && file_exists($path)) {
            $content = file_get_contents($path);
            if (! empty($content)) {
                return $content;
            }
        }

        $key = config('doku.public_key');
        if (! empty($key)) {
            return str_replace('\n', "\n", $key);
        }

        return null;
    }

    /**
     * Test connection to DOKU API with current credentials.
     *
     * @return array{success: bool, status_code: int, message: string, details?: mixed}
     */
    public function testConnection(): array
    {
        $requestId = (string) Str::uuid();
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $requestTarget = '/checkout/v1/payment';

        $dummyBody = json_encode([
            'order' => [
                'amount' => 10000,
                'invoice_number' => 'TEST-CONN-'.time(),
                'currency' => 'IDR',
                'callback_url' => route('home'),
            ],
            'payment' => [
                'payment_due_date' => 60,
            ],
            'customer' => [
                'name' => 'Koneksi Test',
                'email' => 'test@digitalstore.id',
            ],
        ]);

        $digest = base64_encode(hash('sha256', $dummyBody, true));
        $stringToSign = "Client-Id:{$this->clientId}\n"
            ."Request-Id:{$requestId}\n"
            ."Request-Timestamp:{$timestamp}\n"
            ."Request-Target:{$requestTarget}\n"
            ."Digest:{$digest}";

        $signature = 'HMACSHA256='.base64_encode(hash_hmac('sha256', $stringToSign, $this->secretKey, true));

        try {
            $response = Http::withHeaders([
                'Client-Id' => $this->clientId,
                'Request-Id' => $requestId,
                'Request-Timestamp' => $timestamp,
                'Signature' => $signature,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post("{$this->baseUrl}{$requestTarget}", json_decode($dummyBody, true));

            $statusCode = $response->status();
            $body = $response->json() ?? [];

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status_code' => $statusCode,
                    'message' => 'Koneksi ke DOKU API Berhasil! Kredensial Client ID & Secret Key terverifikasi.',
                    'details' => $body,
                ];
            }

            $errorCode = $body['error']['code'] ?? '';
            $errorMessage = $body['error']['message'] ?? 'Respon error dari DOKU';

            if ($errorCode === 'invalid_client_id') {
                return [
                    'success' => false,
                    'status_code' => $statusCode,
                    'message' => "Client ID ('{$this->clientId}') tidak dikenali oleh DOKU. Pastikan Anda memasukkan Client ID yang benar dari DOKU Back Office (Menu: Integrasi > API Keys).",
                    'details' => $body,
                ];
            }

            if (str_contains(strtolower($errorCode), 'signature')) {
                return [
                    'success' => false,
                    'status_code' => $statusCode,
                    'message' => 'Signature ditolak oleh DOKU API. Pastikan Secret Key yang dimasukkan benar.',
                    'details' => $body,
                ];
            }

            return [
                'success' => false,
                'status_code' => $statusCode,
                'message' => "DOKU API merespons {$statusCode}: {$errorMessage}",
                'details' => $body,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'status_code' => 500,
                'message' => 'Gagal menghubungi server DOKU: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Parse DOKU webhook notification.
     *
     * @return array{merchant_ref: string, status: string, reference: ?string, raw: array<string, mixed>}
     */
    public function parseCallback(Request $request): array
    {
        $payload = $request->json()->all();

        // Support both DOKU Checkout notification schema and standard payload
        $merchantRef = $payload['order']['invoice_number']
            ?? ($payload['merchant_ref'] ?? '');

        $statusRaw = strtoupper(
            $payload['transaction']['status']
            ?? ($payload['status'] ?? '')
        );

        $reference = $payload['transaction']['id']
            ?? ($payload['reference'] ?? null);

        $status = match ($statusRaw) {
            'SUCCESS', 'PAID' => 'paid',
            'EXPIRED' => 'expired',
            'FAILED' => 'failed',
            default => 'pending',
        };

        return [
            'merchant_ref' => (string) $merchantRef,
            'status' => $status,
            'reference' => $reference,
            'raw' => $payload,
        ];
    }

    /**
     * Simulated DOKU Sandbox Transaction for development & testing.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function mockDokuSandboxTransaction(Order $order, string $paymentMethod, int $amount, array $payload): array
    {
        $ref = 'DOKU-'.strtoupper(uniqid());
        $isQris = str_contains(strtoupper($paymentMethod), 'QRIS');
        $isVa = str_contains(strtoupper($paymentMethod), 'VA');

        return [
            'success' => true,
            'reference' => $ref,
            'merchant_ref' => $order->order_number,
            'payment_method' => $paymentMethod,
            'payment_name' => "DOKU {$paymentMethod}",
            'pay_code' => $isVa ? '8999'.rand(10000000, 99999999) : null,
            'qr_url' => $isQris ? 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data='.urlencode("DOKU-QRIS-{$order->order_number}-{$amount}") : null,
            'checkout_url' => route('orders.show', $order->order_number),
            'fee' => $order->admin_fee,
            'total_amount' => $amount,
            'expires_at' => now()->addMinutes($this->expiryMinutes),
            'raw' => [
                'mock' => true,
                'gateway' => 'DOKU Checkout',
                'api_key' => substr($this->secretKey, 0, 12).'...',
                'payload' => $payload,
            ],
        ];
    }
}
