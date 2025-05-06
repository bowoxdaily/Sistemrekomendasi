<?php

namespace App\Http\Controllers\FE;

use App\Http\Controllers\Controller;
use App\Models\Operators;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperatorControllerFE extends Controller
{
    public function profile()
    {
        $operator = Operators::where('user_id', Auth::id())->first(); // contoh
        return view('dashboard.operator.profile.index', compact('operator'));
    }
    public function tracer()
    {
        return view('dashboard.operator.tracerstudi.tracerstudi');
    }

    public function viewsiswa()
    {

        return view('dashboard.operator.management.siswa.index');
    }
}
