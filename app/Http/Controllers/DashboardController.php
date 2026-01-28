<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Arahkan ke View yang berbeda sesuai Role
        if ($user->role === 'admin') {
            return view('dashboard.admin', compact('user'));
        } 
        elseif ($user->role === 'bk') {
            // Contoh: Ambil data pelanggaran terbaru untuk BK
            // $pelanggaran = LogPelanggaran::latest()->get();
            return view('dashboard.bk', compact('user'));
        } 
        elseif ($user->role === 'kepsek') {
            return view('dashboard.kepsek', compact('user'));
        }
        
        // Default jika guru iseng login web
        return view('dashboard.guru_denied'); 
    }
}