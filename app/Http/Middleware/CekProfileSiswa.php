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
        // Cek apakah user login dan memiliki role 'siswa'
        if (Auth::check() && Auth::user()->role === 'siswa') {
            $student = Students::where('user_id', Auth::id())->first();

            // Izinkan akses ke halaman edit profil meskipun belum lengkap
            if ($request->route()->getName() === 'student.profile.edit') {
                return $next($request);
            }

            // Redirect jika profil belum lengkap
            if (!$student || !$this->isProfileComplete($student)) {
                return redirect()->route('student.profile.edit')
                    ->with('warning', 'Mohon lengkapi profil Anda terlebih dahulu.');
            }

            // Cek status setelah lulus, dan tentukan jika modal perlu ditampilkan
            if ($request->route()->getName() === 'dashboard' && !$student->status_modal_shown) {
                // Jika status setelah lulus belum diisi, set session untuk menampilkan modal
                if (empty($student->status_setelah_lulus)) {
                    session(['show_status_modal' => true]);
                    $student->status_modal_shown = true;
                    $student->save();
                }
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
        ];

        foreach ($requiredFields as $field) {
            if (empty($student->$field)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Cek apakah status setelah lulus sudah diisi dengan benar.
     */
    private function isStatusSetelahLulusCompleted(Students $student): bool
    {
        $validStatus = ['kuliah', 'kerja', 'belum_kerja'];

        return !empty($student->status_setelah_lulus) &&
               in_array($student->status_setelah_lulus, $validStatus);
    }
}
