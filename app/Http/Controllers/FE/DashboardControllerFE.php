<?php

namespace App\Http\Controllers\FE;

use App\Http\Controllers\Controller;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardControllerFE extends Controller
{
    // app/Http/Controllers/DashboardController.php
    public function index()
    {
        $user = Auth::user();

        // Log the user role to help debugging
        $logData = [
            'user_id' => $user->id,
            'role' => $user->role
        ];

        // Add NISN for students
        if ($user->role == 'siswa') {
            $student = Students::where('user_id', $user->id)->first();
            if ($student) {
                $logData['nisn'] = $student->nisn;
            }
        }

        Log::info('Dashboard access attempt', $logData);

        // Redirect ke view yang sesuai berdasarkan role
        if ($user->role == 'siswa') {
            return view('dashboard.siswa.dashboard', compact('user'));
        } elseif ($user->role == 'guru') {
            return view('dashboard.guru.index', compact('user'));
        } elseif ($user->role == 'superadmin') {
            return view('dashboard.superadmin.index', compact('user'));
        } elseif ($user->role == 'operator') {
            // Ensure operator gets the correct dashboard
            return view('dashboard.operator.index', compact('user'));
        } elseif ($user->role == 'kepalasekolah') {
            // Kepala Sekolah dashboard
            return view('dashboard.kepalasekolah.index', compact('user'));
        } else {
            // Default view jika role tidak dikenali
            Log::warning('Unknown user role', ['user_id' => $user->id, 'role' => $user->role]);
            return view('dashboard.default', compact('user'));
        }
    }

    // Add specific dashboard methods for each role
    public function operatorDashboard()
    {
        if (Auth::user()->role !== 'operator') {
            return redirect()->route('dashboard');
        }

        return view('dashboard.operator.index', ['user' => Auth::user()]);
    }

    public function studentDashboard()
    {
        if (Auth::user()->role !== 'siswa') {
            return redirect()->route('dashboard');
        }

        return view('dashboard.siswa.dashboard', ['user' => Auth::user()]);
    }

    public function teacherDashboard()
    {
        if (Auth::user()->role !== 'guru') {
            return redirect()->route('dashboard');
        }

        return view('dashboard.guru.index', ['user' => Auth::user()]);
    }
    public function KepalaSekolahDashboard()
    {
        if (Auth::user()->role !== 'kepalasekolah') {
            return redirect()->route('dashboard');
        }

        return view('dashboard.kepalasekolah.index', ['user' => Auth::user()]);
    }
}
