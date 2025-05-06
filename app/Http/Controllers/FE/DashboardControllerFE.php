<?php

namespace App\Http\Controllers\FE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardControllerFE extends Controller
{
    // app/Http/Controllers/DashboardController.php
    public function index()
    {
        $user = Auth::user();

        // Redirect ke view yang sesuai berdasarkan role
        if ($user->role == 'siswa') {
            return view('dashboard.siswa.dashboard', compact('user'));
        } elseif ($user->role == 'guru') {
            return view('dashboard.guru', compact('user'));
        } elseif ($user->role == 'operator') {
            return view('dashboard.operator.index', compact('user'));
        } else {
            // Default view jika role tidak dikenali
            return view('dashboard.default', compact('user'));
        }
    }
}
