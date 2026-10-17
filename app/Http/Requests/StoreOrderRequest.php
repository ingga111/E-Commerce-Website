<?php

namespace App\Http\Requests;

use App\Models\ProductVariant;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:10'],
            'payment_method' => ['required', 'string'],
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['required', 'string', 'min:9', 'max:16'],
            'notes' => ['nullable', 'string', 'max:500'],
            'target_user_id' => ['nullable', 'string', 'max:50'],
            'target_zone_id' => ['nullable', 'string', 'max:50'],
            'target_nickname' => ['nullable', 'string', 'max:100'],
            'voucher_code' => ['nullable', 'string', 'max:50'],
        ];

        // Conditional validation based on product configuration
        if ($this->has('variant_id')) {
            $variant = ProductVariant::with('product')->find($this->input('variant_id'));

            if ($variant && $variant->product) {
                $product = $variant->product;

                if ($product->requires_user_id) {
                    $rules['target_user_id'] = ['required', 'string', 'max:50'];
                }

                if ($product->requires_zone_id) {
                    $rules['target_zone_id'] = ['required', 'string', 'max:50'];
                }
            }
        }

        return $rules;
    }

    /**
     * Custom validation messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'variant_id.required' => 'Silakan pilih paket / nominal produk terlebih dahulu.',
            'variant_id.exists' => 'Paket produk yang dipilih tidak valid.',
            'payment_method.required' => 'Silakan pilih metode pembayaran.',
            'customer_name.required' => 'Nama lengkap wajib diisi.',
            'customer_email.required' => 'Alamat email wajib diisi untuk pengiriman bukti dan kredensial.',
            'customer_email.email' => 'Format email tidak valid.',
            'customer_phone.required' => 'Nomor WhatsApp / HP wajib diisi.',
            'target_user_id.required' => 'ID Pemain / User ID wajib diisi.',
            'target_zone_id.required' => 'Zone ID / Server ID wajib diisi untuk produk ini.',
        ];
    }
}
