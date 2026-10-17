<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of payment methods.
     */
    public function index(): View
    {
        $paymentMethods = PaymentMethod::orderBy('sort_order')->get();

        return view('admin.payment-methods.index', compact('paymentMethods'));
    }

    /**
     * Show form to create new payment method.
     */
    public function create(): View
    {
        return view('admin.payment-methods.create');
    }

    /**
     * Store newly created payment method.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:payment_methods,code'],
            'name' => ['required', 'string', 'max:255'],
            'group' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:100'],
            'account_holder' => ['required', 'string', 'max:255'],
            'fee_flat' => ['nullable', 'numeric', 'min:0'],
            'fee_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'instructions' => ['nullable', 'string'],
            'qr_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer'],
        ], [
            'code.required' => 'Kode channel/bank wajib diisi (Contoh: BCA, MANDIRI, DANA, QRIS).',
            'code.unique' => 'Kode metode pembayaran ini sudah terdaftar.',
            'name.required' => 'Nama bank/metode pembayaran wajib diisi.',
            'account_number.required' => 'Nomor rekening atau nomor e-wallet wajib diisi.',
            'account_holder.required' => 'Nama pemilik rekening (atas nama) wajib diisi.',
        ]);

        $qrPath = null;
        if ($request->hasFile('qr_image')) {
            $qrPath = $request->file('qr_image')->store('payment_methods', 'public');
        }

        PaymentMethod::create([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'group' => $validated['group'],
            'account_number' => $validated['account_number'],
            'account_holder' => $validated['account_holder'],
            'qr_image' => $qrPath,
            'fee_flat' => $validated['fee_flat'] ?? 0,
            'fee_percent' => $validated['fee_percent'] ?? 0,
            'instructions' => $validated['instructions'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Rekening pembayaran manual baru berhasil ditambahkan!');
    }

    /**
     * Show form to edit payment method.
     */
    public function edit(PaymentMethod $paymentMethod): View
    {
        return view('admin.payment-methods.edit', compact('paymentMethod'));
    }

    /**
     * Update payment method.
     */
    public function update(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'group' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:100'],
            'account_holder' => ['required', 'string', 'max:255'],
            'fee_flat' => ['nullable', 'numeric', 'min:0'],
            'fee_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'instructions' => ['nullable', 'string'],
            'qr_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ]);

        $qrPath = $paymentMethod->qr_image;
        if ($request->hasFile('qr_image')) {
            $qrPath = $request->file('qr_image')->store('payment_methods', 'public');
        }

        $paymentMethod->update([
            'name' => $validated['name'],
            'group' => $validated['group'],
            'account_number' => $validated['account_number'],
            'account_holder' => $validated['account_holder'],
            'qr_image' => $qrPath,
            'fee_flat' => $validated['fee_flat'] ?? 0,
            'fee_percent' => $validated['fee_percent'] ?? 0,
            'instructions' => $validated['instructions'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Rekening pembayaran berhasil diperbarui!');
    }

    /**
     * Toggle active status.
     */
    public function toggle(PaymentMethod $paymentMethod): RedirectResponse
    {
        $paymentMethod->update([
            'is_active' => ! $paymentMethod->is_active,
        ]);

        $status = $paymentMethod->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Rekening {$paymentMethod->name} berhasil {$status}!");
    }

    /**
     * Delete payment method.
     */
    public function destroy(PaymentMethod $paymentMethod): RedirectResponse
    {
        $paymentMethod->delete();

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Rekening pembayaran berhasil dihapus!');
    }
}
