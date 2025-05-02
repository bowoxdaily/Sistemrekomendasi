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
return view('dashboard.dashboard');
}

public function tracer(){
    return view('dashboard.tracerstudi.tracerstudi');
}
}

