<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FlashSaleController extends Controller
{
    /**
     * Display a listing of flash sale events and editor.
     */
    public function index(): View
    {
        $flashSales = FlashSale::latest()->get();
        $currentFlashSale = FlashSale::running()->latest()->first();

        return view('admin.flash-sales.index', compact('flashSales', 'currentFlashSale'));
    }

    /**
     * Store a newly created flash sale event.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'badge_text' => ['required', 'string', 'max:50'],
            'discount_percentage' => ['nullable', 'integer', 'min:1', 'max:100'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date'],
            'target_url' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        FlashSale::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? 'Dapatkan harga diamond & pass termurah sebelum periode berakhir.',
            'badge_text' => $validated['badge_text'],
            'discount_percentage' => $validated['discount_percentage'] ?? 30,
            'start_time' => ! empty($validated['start_time']) ? $validated['start_time'] : now(),
            'end_time' => ! empty($validated['end_time']) ? $validated['end_time'] : now()->addHours(12),
            'target_url' => $validated['target_url'] ?? '#group-game',
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.flash-sales.index')
            ->with('success', 'Event Flash Sale baru berhasil dibuat!');
    }

    /**
     * Update the specified flash sale event.
     */
    public function update(Request $request, FlashSale $flashSale): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'badge_text' => ['required', 'string', 'max:50'],
            'discount_percentage' => ['nullable', 'integer', 'min:1', 'max:100'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date'],
            'target_url' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $flashSale->update([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'],
            'badge_text' => $validated['badge_text'],
            'discount_percentage' => $validated['discount_percentage'] ?? $flashSale->discount_percentage,
            'start_time' => $validated['start_time'] ?: null,
            'end_time' => $validated['end_time'] ?: null,
            'target_url' => $validated['target_url'] ?? '#group-game',
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.flash-sales.index')
            ->with('success', 'Pengaturan Flash Sale berhasil diperbarui!');
    }

    /**
     * Toggle the active status of the flash sale.
     */
    public function toggle(FlashSale $flashSale): RedirectResponse
    {
        $flashSale->update([
            'is_active' => ! $flashSale->is_active,
        ]);

        $status = $flashSale->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Event Flash Sale berhasil {$status}.");
    }

    /**
     * Remove the specified flash sale event.
     */
    public function destroy(FlashSale $flashSale): RedirectResponse
    {
        $flashSale->delete();

        return redirect()->route('admin.flash-sales.index')
            ->with('success', 'Event Flash Sale berhasil dihapus.');
    }
}
