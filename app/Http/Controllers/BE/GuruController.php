<?php

namespace App\Http\Controllers\BE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Guru;
use App\Models\User;

class GuruController extends Controller
{
    /**
     * Display the teacher profile page
     */
    public function profile()
    {
        $user = Auth::user();

        // Get or create teacher profile
        $teacher = Guru::where('user_id', $user->id)->first();

        if (!$teacher) {
            // Create a basic teacher profile if it doesn't exist
            $teacher = new Guru();
            $teacher->user_id = $user->id;
            $teacher->nama_lengkap = $user->name;
            $teacher->save();
        }

        return view('dashboard.guru.profile', compact('teacher'));
    }

    /**
     * Update teacher profile via API
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            $teacher = Guru::where('user_id', $user->id)->first();

            if (!$teacher) {
                $teacher = new Guru();
                $teacher->user_id = $user->id;
            }            // Validation rules
            $validator = Validator::make($request->all(), [
                'nama_lengkap' => 'required|string|max:255',
                'jabatan' => 'nullable|string|max:255',
                'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
                'alamat' => 'nullable|string',
                'no_telp' => 'nullable|string|max:20',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle file upload
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                // Delete old photo if exists
                if ($user->foto && Storage::exists('public/user_photos/' . $user->foto)) {
                    Storage::delete('public/user_photos/' . $user->foto);
                }

                // Upload new photo
                $file = $request->file('foto');
                $fileName = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/user_photos', $fileName);
                $fotoPath = $fileName;
            }            // Update teacher data
            $teacher->fill([
                'nama_lengkap' => $request->nama_lengkap,
                'jabatan' => $request->jabatan,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat,
            ]);
            $teacher->save(); // Update user data
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'email' => $request->email,
                    'no_telp' => $request->no_telp,
                    'foto' => $fotoPath ? $fotoPath : $user->foto,
                    'updated_at' => now()
                ]);            // Prepare response data
            $responseData = [
                'nama_lengkap' => $teacher->nama_lengkap,
                'nip' => $teacher->nip,
                'jabatan' => $teacher->jabatan,
                'jenis_kelamin' => $teacher->jenis_kelamin,
                'alamat' => $teacher->alamat,
                'no_telp' => $user->no_telp,
                'email' => $user->email,
            ];

            if ($fotoPath) {
                $responseData['foto_url'] = asset('storage/user_photos/' . $fotoPath);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui',
                'data' => $responseData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change teacher password via API
     */
    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ], [
                'current_password.required' => 'Password saat ini harus diisi',
                'new_password.required' => 'Password baru harus diisi',
                'new_password.min' => 'Password baru minimal 8 karakter',
                'new_password.confirmed' => 'Konfirmasi password tidak cocok',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();

            // Check current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password saat ini tidak benar'
                ], 422);
            }            // Update password
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'password' => Hash::make($request->new_password),
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get teacher dashboard data
     */
    public function dashboard()
    {
        $user = Auth::user();
        $teacher = Guru::where('user_id', $user->id)->first();

        if (!$teacher) {
            // Create a basic teacher profile if it doesn't exist
            $teacher = new Guru();
            $teacher->user_id = $user->id;
            $teacher->nama_lengkap = $user->name;
            $teacher->save();
        }

        return view('dashboard.guru.index', compact('teacher'));
    }
}
