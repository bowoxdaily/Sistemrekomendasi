<?php

namespace App\Http\Controllers\FE;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{


    public function profile()
    {
        $student = Students::where('user_id', Auth::id())->first(); // contoh
        $jurusan = Jurusan::where('id', Auth::id())->first();
        return view('dashboard.siswa.profile', compact('student', 'jurusan'));
    }
}
