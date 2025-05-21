<?php

namespace App\Http\Controllers\BE;

use App\Http\Controllers\Controller;
use App\Models\DataKuliah;
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
        $percentageChange = $totalSiswa > 0 ? round(($count30DaysAgo / $totalSiswa) * 100, 2) : 0;

        return response()->json([
            'status' => 'success',
            'data' => [
                'count' => $totalSiswa,
                'percentage_change' => $percentageChange,
            ],
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
                'redirect' => route('student.profile.edit'),
                'message' => 'Profil siswa tidak ditemukan.',
            ]);
        }

        // Check basic profile fields
        $requiredFields = ['nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'jenis_kelamin'];

        foreach ($requiredFields as $field) {
            if (empty($student->$field)) {
                return response()->json([
                    'complete' => false,
                    'redirect' => route('student.profile.edit'),
                    'message' => 'Mohon lengkapi profil Anda terlebih dahulu.',
                ]);
            }
        }

        // Check if student has graduated
        if ($student->status_lulus === 'lulus') {
            // Check if status after graduation is filled
            if (empty($student->status_setelah_lulus)) {
                return response()->json([
                    'complete' => false,
                    'show_status_modal' => true,
                    'message' => 'Mohon lengkapi status setelah lulus Anda.',
                ]);
            }
        }

        return response()->json(['complete' => true]);
    }

    public function getProfile()
    {
        if (!Auth::check()) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'User not authenticated',
                ],
                401,
            );
        }

        $user = Auth::user();
        $student = Students::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Student profile not found',
                ],
                404,
            );
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
                'foto' => $user->foto,
                'tangal_lulus' => $student->tanggal_lulus,
            ],
        ]);
    }
    public function updateProfile(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'User not authenticated',
                ],
                401,
            );
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'User not found',
                ],
                404,
            );
        }

        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'nisn' => 'required|string',
            'alamat' => 'required|string',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'no_telp' => 'required|string|max:20',
            'status_lulus' => 'required|in:belum,lulus',
            'tanggal_lulus' => 'nullable|required_if:status_lulus,lulus|date',
            'status_setelah_lulus' => 'nullable|required_if:status_lulus,lulus|in:belum_kerja,kuliah,kerja',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ],
                422,
            );
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
            $student->nama_lengkap = $request->nama_lengkap;
            $student->tempat_lahir = $request->tempat_lahir;
            $student->tanggal_lahir = $request->tanggal_lahir;
            $student->nisn = $request->nisn;
            $student->alamat = $request->alamat;
            $student->jenis_kelamin = $request->jenis_kelamin;
            $student->status_lulus = $request->status_lulus;

            // Handle graduation related fields
            if ($request->status_lulus === 'lulus') {
                $student->tanggal_lulus = $request->tanggal_lulus;
                $student->status_setelah_lulus = $request->status_setelah_lulus;
                $student->status_terakhir_diupdate = now();
            } else {
                $student->tanggal_lulus = null;
                $student->status_setelah_lulus = null;
            }

            // Check if all required fields are completed
            $requiredFields = [$student->nama_lengkap, $student->tempat_lahir, $student->tanggal_lahir, $student->nisn, $student->alamat, $student->jenis_kelamin, $user->no_telp];

            $student->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Profil berhasil diperbarui!',
                'data' => [
                    'student' => $student,
                    'foto' => $user->foto ? asset('storage/user_photos/' . $user->foto) : null,
                    'no_telp' => $user->no_telp,
                    'is_profile_complete' => $student->is_profile_complete,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan saat memperbarui profil: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function storeKuliah(Request $request)
    {
        $request->validate([
            'nama_pt' => 'required|string',
            'jurusan' => 'required|string',
            'jenjang' => 'required|string',
            'tahun_masuk' => 'required|integer|min:1900|max:' . date('Y'),
            'status_beasiswa' => 'required|in:ya,tidak',
            'nama_beasiswa' => 'nullable|string',
            'prestasi_akademik' => 'nullable|string',
        ]);

        DataKuliah::create([
            'student_id' => Auth::user()->student->id,
            'nama_pt' => $request->nama_pt,
            'jurusan' => $request->jurusan,
            'jenjang' => $request->jenjang,
            'tahun_masuk' => $request->tahun_masuk,
            'status_beasiswa' => $request->status_beasiswa,
            'nama_beasiswa' => $request->nama_beasiswa,
            'prestasi_akademik' => $request->prestasi_akademik,
        ]);

        return redirect()->route('dashboard')->with('success', 'Data kuliah berhasil disimpan');
    }

    public function updatesiswaProfile(Request $request)
    {
        $user = Auth::user();
        $student = Students::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json(['status' => 'error', 'message' => 'Student not found'], 404);
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'no_telp' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Update student profile
            $student->tempat_lahir = $request->tempat_lahir;
            $student->tanggal_lahir = $request->tanggal_lahir;
            $student->alamat = $request->alamat;
            $student->jenis_kelamin = $request->jenis_kelamin;
            $student->save();

            // Update user data
            $user->no_telp = $request->no_telp;
            $user->email = $request->email;

            // Handle photo upload
            $foto_url = null;
            if ($request->hasFile('foto')) {
                // Remove old photo if exists
                if ($user->foto) {
                    Storage::delete('public/user_photos/' . $user->foto);
                }

                // Save new photo
                $photoName = time() . '.' . $request->foto->extension();
                $request->foto->storeAs('public/user_photos', $photoName);
                $user->foto = $photoName;
                $foto_url = asset('storage/user_photos/' . $photoName);
            }

            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Profil berhasil diperbarui!',
                'data' => [
                    'nama_lengkap' => $student->nama_lengkap,
                    'tempat_lahir' => $student->tempat_lahir,
                    'tanggal_lahir' => $student->tanggal_lahir,
                    'jenis_kelamin' => $student->jenis_kelamin,
                    'alamat' => $student->alamat,
                    'no_telp' => $user->no_telp,
                    'email' => $user->email,
                    'foto_url' => $foto_url,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
