<?php

namespace App\Http\Controllers\BE;

use App\Http\Controllers\Controller;
use App\Models\Students;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SiswaControllerBE extends Controller
{
    public function getCount()
    {
        $totalSiswa = Students::count(); // ganti dengan model yang kamu pakai

        // Misal: Perhitungan perubahan persentase (optional)
        $count30DaysAgo = Students::where('created_at', '>=', now()->subDays(30))->count();
        $percentageChange = $totalSiswa > 0
            ? round(($count30DaysAgo / $totalSiswa) * 100, 2)
            : 0;

        return response()->json([
            'status' => 'success',
            'data' => [
                'count' => $totalSiswa,
                'percentage_change' => $percentageChange
            ]
        ]);
    }

    public function edit()
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Ambil data siswa berdasarkan user_id
        $student = Students::where('user_id', $user->id)->first();

        // Kirim data ke view edit
        return view('dashboard.siswa.edit', compact('user', 'student'));
    }

    public function checkProfile()
    {
        $user = Auth::user();

        if ($user->role !== 'siswa') {
            return response()->json(['complete' => true]);
        }

        $student = Students::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json([
                'complete' => false,
                'redirect' => route('student.profile.edit')
            ]);
        }

        $requiredFields = [
            'nama_lengkap',
            'tempat_lahir',
            'tanggal_lahir',
            'alamat',
            'jenis_kelamin',


        ];

        foreach ($requiredFields as $field) {
            if (empty($student->$field)) {
                return response()->json([
                    'complete' => false,
                    'redirect' => route('student.profile.edit'),
                    'message' => 'Mohon lengkapi profil Anda terlebih dahulu.'
                ]);
            }
        }

        return response()->json(['complete' => true]);
    }

    public function getProfile()
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated'
            ], 401);
        }

        $user = Auth::user();
        $student = Students::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student profile not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Profile data retrieved successfully',
            'data' => [
                'nama_lengkap' => $student->nama_lengkap,
                'tempat_lahir' => $student->tempat_lahir,
                'tanggal_lahir' => $student->tanggal_lahir,
                'nisn' => $student->nisn,
                'alamat' => $student->alamat,
                'jenis_kelamin' => $student->jenis_kelamin,
                'foto' => $user->foto
            ]
        ]);
    }
    public function updateProfile(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated'
            ], 401);
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_lengkap'   => 'required|string|max:255',
            'tempat_lahir'   => 'required|string|max:255',
            'tanggal_lahir'  => 'required|date',
            'nisn'           => 'required|string',
            'alamat'         => 'required|string',
            'jenis_kelamin'  => 'required|in:Laki-laki,Perempuan',
            'no_telp'        => 'required|string|max:20',
            'foto'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $student = Students::where('user_id', $user->id)->first();
            if (!$student) {
                $student = new Students();
                $student->user_id = $user->id;
            }

            // Handle photo upload
            if ($request->hasFile('foto')) {
                if ($user->foto) {
                    Storage::delete('public/user_photos/' . $user->foto);
                }

                $photoName = time() . '.' . $request->foto->extension();
                $request->foto->storeAs('public/user_photos', $photoName);
                $user->foto = $photoName;
            }

            // Update user table (no_telp)
            $user->no_telp = $request->no_telp;
            $user->save();

            // Update student profile
            $student->nama_lengkap  = $request->nama_lengkap;
            $student->tempat_lahir  = $request->tempat_lahir;
            $student->tanggal_lahir = $request->tanggal_lahir;
            $student->nisn          = $request->nisn;
            $student->alamat        = $request->alamat;
            $student->jenis_kelamin = $request->jenis_kelamin;
            $student->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Profil berhasil diperbarui!',
                'data'    => [
                    'student' => $student,
                    'foto'    => $user->foto ? asset('storage/user_photos/' . $user->foto) : null,
                    'no_telp' => $user->no_telp
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui profil: ' . $e->getMessage()
            ], 500);
        }
    }
}
