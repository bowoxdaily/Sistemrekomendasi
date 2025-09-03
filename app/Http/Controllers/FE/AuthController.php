<?php

namespace App\Http\Controllers\FE;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function viewlogin()
    {
        return view('auth.login');
    }
    public function viewregister()
    {
        $jurusans = Jurusan::all();
        return view('auth.register', compact('jurusans'));
    }
}
