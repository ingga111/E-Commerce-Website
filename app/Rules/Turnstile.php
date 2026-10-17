<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Translation\PotentiallyTranslatedString;

class Turnstile implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Bypass in testing environment so tests continue to pass reliably
        if (app()->environment('testing')) {
            return;
        }

        $secret = config('services.turnstile.secret');

        if (blank($secret)) {
            return;
        }

        if (blank($value)) {
            $fail('Silakan selesaikan verifikasi captcha Cloudflare terlebih dahulu.');

            return;
        }

        try {
            $response = Http::asForm()->timeout(6)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secret,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            $result = $response->json();

            if (! ($result['success'] ?? false)) {
                $fail('Verifikasi captcha Cloudflare gagal atau kadaluarsa. Silakan muat ulang dan coba lagi.');
            }
        } catch (\Throwable $e) {
            $fail('Gagal memverifikasi captcha Cloudflare. Silakan periksa koneksi internet Anda.');
        }
    }
}
