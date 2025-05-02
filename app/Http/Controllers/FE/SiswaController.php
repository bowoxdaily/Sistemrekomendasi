<?php

namespace App\Http\Controllers\FE;

use App\Http\Controllers\Controller;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    public function index (){
        return view('dashboard.siswa.dashboard');
    }

    public function profile(){
        $student = Students::where('user_id', Auth::id())->first(); // contoh
        return view('dashboard.siswa.profile',compact('student'));
    }
}
