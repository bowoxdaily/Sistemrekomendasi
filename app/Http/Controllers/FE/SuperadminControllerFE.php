<?php

namespace App\Http\Controllers\FE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperadminControllerFE extends Controller
{
    public function index()
    {
        return view('dashboard.superadmin.index');
    }

    public function operator()
    {
        return view('dashboard.superadmin.operator.index');
    }

    public function siswa()
    {
        return view('dashboard.superadmin.siswa');
    }
}
