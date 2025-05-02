<?php

namespace App\Http\Middleware;

use App\Models\Students;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekProfileSiswa
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated and has the 'siswa' role
        if (Auth::check() && Auth::user()->role === 'siswa') {
            // Find the student profile related to this user
            $student = Students::where('user_id', Auth::id())->first();
            
            // If no student profile exists or it's incomplete, redirect to the profile completion page
            if (!$student || !$this->isProfileComplete($student)) {
                return redirect()->route('student.profile.edit')
                    ->with('warning', 'Mohon lengkapi profil Anda terlebih dahulu.');
            }
        }

        return $next($request);
    }

    /**
     * Check if the student profile is complete.
     *
     * @param  \App\Models\Student  $student
     * @return bool
     */
    private function isProfileComplete(Students $student)
    {
        // Define what fields must be filled for a complete profile
        $requiredFields = [
            'nama_lengkap', 
            'tempat_lahir', 
            'tanggal_lahir', 
            'alamat', 
            
        ];

        // Check if all required fields are filled
        foreach ($requiredFields as $field) {
            if (empty($student->$field)) {
                return false;
            }
        }

        return true;
    }
}
