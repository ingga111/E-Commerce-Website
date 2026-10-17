<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BannerController extends Controller
{
    /**
     * Display a listing of the promo banners.
     */
    public function index(): View
    {
        $banners = Banner::orderBy('sort_order')->latest('id')->get();

        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Store a newly created banner.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'badge_text' => ['nullable', 'string', 'max:50'],
            'target_url' => ['nullable', 'string', 'max:255'],
            'gradient' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:4096'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('banners', 'public');
        }

        Banner::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'badge_text' => $validated['badge_text'] ?? 'PROMO SPESIAL',
            'target_url' => $validated['target_url'] ?? '#games',
            'gradient' => $validated['gradient'] ?? 'from-indigo-900 via-blue-900 to-slate-950',
            'image_path' => $imagePath,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner promo baru berhasil ditambahkan!');
    }

    /**
     * Update the specified banner.
     */
    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'badge_text' => ['nullable', 'string', 'max:50'],
            'target_url' => ['nullable', 'string', 'max:255'],
            'gradient' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:4096'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imagePath = $banner->image_path;
        if ($request->hasFile('image')) {
            if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $imagePath = $request->file('image')->store('banners', 'public');
        }

        $banner->update([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'badge_text' => $validated['badge_text'] ?? 'PROMO SPESIAL',
            'target_url' => $validated['target_url'] ?? '#games',
            'gradient' => $validated['gradient'] ?? $banner->gradient,
            'image_path' => $imagePath,
            'sort_order' => $validated['sort_order'] ?? $banner->sort_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner promo berhasil diperbarui!');
    }

    /**
     * Remove the specified banner.
     */
    public function destroy(Banner $banner): RedirectResponse
    {
        if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
            Storage::disk('public')->delete($banner->image_path);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner promo berhasil dihapus!');
    }

    /**
     * Toggle the active status of the banner.
     */
    public function toggle(Banner $banner): RedirectResponse
    {
        $banner->update([
            'is_active' => ! $banner->is_active,
        ]);

        return back()->with('success', 'Status banner berhasil diubah.');
    }
}
