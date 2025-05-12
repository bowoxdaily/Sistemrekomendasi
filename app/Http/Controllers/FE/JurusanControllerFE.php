<?php

namespace App\Http\Controllers\FE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JurusanControllerFE extends Controller
{
    public function index (){

        return view('dashboard.operator.management.jurusan.index');
    }
}
