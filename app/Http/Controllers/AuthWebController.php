<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthWebController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'nip' => 'required',
        'password' => 'required',
    ]);

    // Tambahkan kondisi status aktif di sini
    // Laravel akan otomatis mengecek is_active = 1 saat login
    $credentialsWithStatus = array_merge($credentials, ['is_active' => 1]);

    if (Auth::attempt($credentialsWithStatus)) {
        $request->session()->regenerate();

        return redirect()->intended('dashboard');
    }

    // Jika gagal, cek apakah NIP-nya ada tapi is_active-nya 0
    $user = \App\Models\User::where('nip', $request->nip)->first();
    
    if ($user && !$user->is_active) {
        return back()->withErrors([
            'nip' => 'Akun Anda dinonaktifkan. Silakan hubungi Administrator.',
        ]);
    }

    return back()->withErrors([
        'nip' => 'NIP atau Password salah.',
    ]);
}
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}