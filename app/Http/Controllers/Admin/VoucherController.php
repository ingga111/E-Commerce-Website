<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VoucherController extends Controller
{
    /**
     * Display a listing of vouchers and metrics.
     */
    public function index(): View
    {
        $vouchers = Voucher::latest()->get();
        $totalVouchers = $vouchers->count();
        $activeVouchers = $vouchers->where('is_active', true)->count();
        $totalClaims = $vouchers->sum('used_count');

        return view('admin.vouchers.index', compact('vouchers', 'totalVouchers', 'activeVouchers', 'totalClaims'));
    }

    /**
     * Store a newly created voucher.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'alpha_num', 'unique:vouchers,code'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'type' => ['required', 'string', Rule::in(['fixed', 'percentage'])],
            'discount_amount' => ['required', 'numeric', 'min:0.01'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'code.unique' => 'Kode voucher ini sudah digunakan, silakan buat kode lain.',
            'code.alpha_num' => 'Kode voucher hanya boleh berupa huruf dan angka tanpa spasi.',
        ]);

        Voucher::create([
            'code' => strtoupper(trim($validated['code'])),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'discount_amount' => $validated['discount_amount'],
            'min_order_amount' => $validated['min_order_amount'] ?? 0,
            'max_discount' => ($validated['type'] === 'percentage' && ! empty($validated['max_discount'])) ? $validated['max_discount'] : null,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'used_count' => 0,
            'starts_at' => $validated['starts_at'] ?: null,
            'expires_at' => $validated['expires_at'] ?: null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.vouchers.index')
            ->with('success', "Voucher '{$validated['code']}' berhasil dibuat!");
    }

    /**
     * Update the specified voucher.
     */
    public function update(Request $request, Voucher $voucher): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'alpha_num', Rule::unique('vouchers', 'code')->ignore($voucher->id)],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'type' => ['required', 'string', Rule::in(['fixed', 'percentage'])],
            'discount_amount' => ['required', 'numeric', 'min:0.01'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $voucher->update([
            'code' => strtoupper(trim($validated['code'])),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'discount_amount' => $validated['discount_amount'],
            'min_order_amount' => $validated['min_order_amount'] ?? 0,
            'max_discount' => ($validated['type'] === 'percentage' && ! empty($validated['max_discount'])) ? $validated['max_discount'] : null,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'starts_at' => $validated['starts_at'] ?: null,
            'expires_at' => $validated['expires_at'] ?: null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.vouchers.index')
            ->with('success', "Voucher '{$voucher->code}' berhasil diperbarui!");
    }

    /**
     * Toggle the active status of the voucher.
     */
    public function toggle(Voucher $voucher): RedirectResponse
    {
        $voucher->update([
            'is_active' => ! $voucher->is_active,
        ]);

        $status = $voucher->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Voucher '{$voucher->code}' berhasil {$status}.");
    }

    /**
     * Remove the specified voucher.
     */
    public function destroy(Voucher $voucher): RedirectResponse
    {
        $code = $voucher->code;
        $voucher->delete();

        return redirect()->route('admin.vouchers.index')
            ->with('success', "Voucher '{$code}' berhasil dihapus.");
    }
}
