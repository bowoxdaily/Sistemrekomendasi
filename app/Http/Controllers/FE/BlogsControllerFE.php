<?php

namespace App\Http\Controllers\FE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BlogsControllerFE extends Controller
{
    public function index()
    {
        // Logic to retrieve and display blogs
        return view('dashboard.operator.blog.index'); // Assuming you have a view for displaying blogs
    }
}
