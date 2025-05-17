<?php

namespace App\Http\Middleware;

use App\Models\Students;
use App\Models\Questionnaire;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        // Cek apakah user login dan memiliki role 'siswa'
        if (Auth::check() && Auth::user()->role === 'siswa') {
            $student = Students::where('user_id', Auth::id())->first();

            // Log untuk debugging
            Log::info('Middleware CekProfileSiswa executing for route: ' . $request->route()->getName());

            // Jika siswa tidak ditemukan, redirect ke edit profil
            if (!$student) {
                Log::info('Student not found, redirecting to profile edit');
                return redirect()->route('student.profile.edit')
                    ->with('warning', 'Mohon lengkapi profil Anda terlebih dahulu.');
            }

            // Log status siswa untuk debugging
            Log::info('Student status: ' . $student->status_setelah_lulus);
            Log::info('Has completed questionnaire: ' . ($student->has_completed_questionnaire ? 'Yes' : 'No'));

            // Route-route yang selalu diizinkan, terlepas dari kelengkapan profil
            $allowedRoutes = [
                'student.profile.edit',
                'student.profile.update',
                'student.kuis',
                'student.questionnaire.submit',
                'student.logout',
                'student.dashboard' // Tambahkan dashboard ke allowed routes
            ];

            // Izinkan akses ke route yang selalu diizinkan
            if (in_array($request->route()->getName(), $allowedRoutes)) {
                return $next($request);
            }

            // Remove questionnaire redirect and let dashboard handle it
            // Only check profile completion
            if (!$this->isProfileComplete($student)) {
                Log::info('Profile incomplete, redirecting to profile edit');
                return redirect()->route('student.profile.edit')
                    ->with('warning', 'Mohon lengkapi profil Anda terlebih dahulu.');
            }
        }

        return $next($request);
    }

    /**
     * Cek apakah profil siswa sudah lengkap.
     */
    private function isProfileComplete(Students $student): bool
    {
        $requiredFields = [
            'nama_lengkap',
            'tempat_lahir',
            'tanggal_lahir',
            'alamat',
            'jenis_kelamin',
            'status_setelah_lulus' // Pastikan status setelah lulus diisi
        ];

        foreach ($requiredFields as $field) {
            if (empty($student->$field)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Cek apakah status setelah lulus adalah "belum kerja" dengan lebih fleksibel.
     */
    private function isStatusBelumKerja($status): bool
    {
        if (empty($status)) {
            return false;
        }

        $status = trim(strtolower($status));
        return $status === 'belum_kerja' ||
            $status === 'belum bekerja' ||
            $status === 'belum kerja' ||
            $status === 'menganggur';
    }
}
