<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Check and validate voucher code for a selected product variant.
     */
    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $code = strtoupper(trim((string) $request->input('code')));
        $variant = ProductVariant::find($request->input('variant_id'));

        if (! $variant) {
            return response()->json([
                'valid' => false,
                'message' => 'Silakan pilih varian / nominal produk terlebih dahulu.',
            ], 422);
        }

        $quantity = (int) ($request->input('quantity', 1));
        $subtotal = (float) $variant->price * $quantity;

        $voucher = Voucher::where('code', $code)->first();

        if (! $voucher) {
            return response()->json([
                'valid' => false,
                'message' => "Kode voucher '{$code}' tidak ditemukan.",
            ], 404);
        }

        if (! $voucher->isValid()) {
            $reason = 'Kode voucher sudah tidak aktif atau kuota penggunaan telah habis.';
            if ($voucher->expires_at && $voucher->expires_at->isPast()) {
                $reason = 'Masa berlaku voucher ini sudah kadaluarsa.';
            } elseif ($voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit) {
                $reason = 'Kuota penggunaan voucher ini sudah habis.';
            }

            return response()->json([
                'valid' => false,
                'message' => $reason,
            ], 422);
        }

        if (! $voucher->canApplyTo($subtotal)) {
            return response()->json([
                'valid' => false,
                'message' => 'Minimal pembelian untuk voucher ini adalah Rp '.number_format($voucher->min_order_amount, 0, ',', '.').'.',
            ], 422);
        }

        $discountAmount = $voucher->calculateDiscount($subtotal);
        $newSubtotal = max(0, $subtotal - $discountAmount);

        return response()->json([
            'valid' => true,
            'code' => $voucher->code,
            'name' => $voucher->name,
            'type' => $voucher->type,
            'discount_amount' => $discountAmount,
            'formatted_discount' => 'Rp '.number_format($discountAmount, 0, ',', '.'),
            'new_subtotal' => $newSubtotal,
            'formatted_new_subtotal' => 'Rp '.number_format($newSubtotal, 0, ',', '.'),
            'message' => "Voucher '{$voucher->code}' berhasil diterapkan! Anda hemat Rp ".number_format($discountAmount, 0, ',', '.').'.',
        ]);
    }
}
