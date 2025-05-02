<?php

namespace App\Http\Controllers\FE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function viewlogin(){
        return view ('auth.login');
    }
    public function viewregister(){
        return view('auth.register');
    }
    
}
