<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the user profile page.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $recentOrders = $user->orders()->with('items.product')->latest()->take(5)->get();
        $totalOrders = $user->orders()->count();
        $completedOrders = $user->orders()->where('status', OrderStatus::Completed)->count();

        return view('profile.edit', compact('user', 'recentOrders', 'totalOrders', 'completedOrders'));
    }

    /**
     * Update the user profile information and password.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'current_password.current_password' => 'Password saat ini yang Anda masukkan salah.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }
}
