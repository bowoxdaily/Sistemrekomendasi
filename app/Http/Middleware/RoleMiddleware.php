<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
{
    // Check if user is logged in
    if (!Auth::check()) {
        return redirect('login');
    }

    $user = Auth::user();
    
    // If $roles contains the user's role, allow access
    if (in_array($user->role, $roles)) {
        $response = $next($request);
        
        // Set cache control headers
        $response->headers->set('Cache-Control', 'nocache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
        
        return $response;
    }
    
    // User doesn't have the required role
    return redirect('login')->with('error', 'Anda tidak memiliki akses...');
}
}
