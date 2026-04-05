<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Tampilkan Halaman Edit Profil
     */
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Update Informasi Dasar (Nama & Email)
     */
    public function updateAll(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // 1. Validasi
        $rules = [
            'no_hp' => 'required|numeric|digits_between:10,15',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        // Jika user mencoba mengisi salah satu field password
        if ($request->filled('current_password') || $request->filled('password')) {
            $rules['current_password'] = 'required|current_password';
            $rules['password'] = 'required|min:8|confirmed';
        }

        $request->validate($rules);

        // 2. Update No HP
        $user->no_hp = $request->no_hp;

        // 3. Update Foto (Jika ada file)
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika bukan default
            if ($user->foto_profil && !str_contains($user->foto_profil, 'default')) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $path = $request->file('foto_profil')->store('profiles', 'public');
            $user->foto_profil = $path;
        }

        // 4. Update Password (Jika diisi)
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
