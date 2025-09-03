<?php

namespace App\Http\Controllers\BE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Operators;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SuperAdminController extends Controller
{
    // Show the operator management page
    public function operatorIndex()
    {
        return view('dashboard.superadmin.operator.index');
    }

    // Get all operators, guru, kepala sekolah as JSON
    public function getOperators()
    {
        $operators = \App\Models\Operators::with('user')->get();
        $guru = \App\Models\Guru::with('user')->get();
        $kepala = \App\Models\KepalaSekolah::with('user')->get();
        $all = collect([])->concat($operators)->concat($guru)->concat($kepala)->values();
        return response()->json($all);
    }

    // Get a single operator as JSON
    public function getOperator($id)
    {
        $operator = Operators::with('user')->find($id);

        if (!$operator) {
            return response()->json(['message' => 'Operator not found'], 404);
        }

        return response()->json($operator);
    }

    // Create a new user (operator/guru/kepala sekolah)
    public function storeOperator(Request $request)
    {
        $jabatan = $request->input('jabatan', 'operator');
        $rules = [
            'email' => 'required|email|unique:users,email',
            'nama_lengkap' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'no_hp' => 'nullable|string|max:15',
            'nip' => 'nullable|string|max:20',
            'jabatan' => 'required|string|max:100',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        try {
            DB::beginTransaction();
            // Set role sesuai jabatan
            $role = strtolower($jabatan) === 'kepala sekolah' ? 'kepalasekolah' : (strtolower($jabatan) === 'guru' ? 'guru' : 'operator');
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $role,
                'status' => 'aktif'
            ]);
            // Create profile sesuai jabatan
            if ($role === 'guru') {
                $profile = \App\Models\Guru::create([
                    'user_id' => $user->id,
                    'nama_lengkap' => $request->nama_lengkap,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'alamat' => $request->alamat ?? null,
                    'nip' => $request->nip ?? null,
                    'jabatan' => $jabatan
                ]);
            } elseif ($role === 'kepalasekolah') {
                $profile = \App\Models\KepalaSekolah::create([
                    'user_id' => $user->id,
                    'nama_lengkap' => $request->nama_lengkap,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'alamat' => $request->alamat ?? null,
                    'nip' => $request->nip ?? null,
                    'jabatan' => $jabatan
                ]);
            } else {
                $profile = Operators::create([
                    'user_id' => $user->id,
                    'nama_lengkap' => $request->nama_lengkap,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'alamat' => $request->alamat ?? null,
                    'nip' => $request->nip ?? null,
                    'jabatan' => $jabatan
                ]);
            }
            DB::commit();
            return response()->json([
                'message' => 'User berhasil ditambahkan',
                'data' => $profile
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat menambahkan user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update an existing operator
    public function updateOperator(Request $request, $id)
    {
        $operator = Operators::find($id);

        if (!$operator) {
            return response()->json(['message' => 'Operator not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($operator->user_id)
            ],
            'nip' => 'nullable|string|max:20',
            'jabatan' => 'nullable|string|max:100',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string',
            'status' => 'nullable|in:aktif,nonaktif'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Update operator data
            $operator->update([
                'nama_lengkap' => $request->nama_lengkap,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat ?? $operator->alamat,
                'nip' => $request->nip ?? $operator->nip,
                'jabatan' => $request->jabatan ?? $operator->jabatan
            ]);

            // Update related user data
            if ($operator->user) {
                $operator->user->update([
                    'name' => $request->nama_lengkap,
                    'email' => $request->email,
                    'status' => $request->status ?? $operator->user->status
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Data operator berhasil diperbarui',
                'data' => $operator->fresh(['user'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui operator',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete an operator
    public function deleteOperator($id)
    {
        $operator = Operators::find($id);

        if (!$operator) {
            return response()->json(['message' => 'Operator not found'], 404);
        }

        try {
            DB::beginTransaction();

            // Store user_id to delete user after operator
            $userId = $operator->user_id;

            // Delete operator first
            $operator->delete();

            // Delete related user account
            if ($userId) {
                User::find($userId)->delete();
            }

            DB::commit();

            return response()->json([
                'message' => 'Operator berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus operator',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get a single guru as JSON
    public function getGuru($id)
    {
        $guru = \App\Models\Guru::with('user')->find($id);
        if (!$guru) {
            return response()->json(['message' => 'Guru not found'], 404);
        }
        return response()->json($guru);
    }

    // Update an existing guru
    public function updateGuru(Request $request, $id)
    {
        $guru = \App\Models\Guru::find($id);
        if (!$guru) {
            return response()->json(['message' => 'Guru not found'], 404);
        }
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($guru->user_id)
            ],
            'nip' => 'nullable|string|max:20',
            'jabatan' => 'nullable|string|max:100',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string',
            'status' => 'nullable|in:aktif,nonaktif'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        try {
            DB::beginTransaction();
            $guru->update([
                'nama_lengkap' => $request->nama_lengkap,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat ?? $guru->alamat,
                'nip' => $request->nip ?? $guru->nip,
                'jabatan' => $request->jabatan ?? $guru->jabatan
            ]);
            if ($guru->user) {
                $guru->user->update([
                    'name' => $request->nama_lengkap,
                    'email' => $request->email,
                    'status' => $request->status ?? $guru->user->status
                ]);
            }
            DB::commit();
            return response()->json([
                'message' => 'Data guru berhasil diperbarui',
                'data' => $guru->fresh(['user'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui guru',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete a guru
    public function deleteGuru($id)
    {
        $guru = \App\Models\Guru::find($id);
        if (!$guru) {
            return response()->json(['message' => 'Guru not found'], 404);
        }
        try {
            DB::beginTransaction();
            $userId = $guru->user_id;
            $guru->delete();
            if ($userId) {
                User::find($userId)->delete();
            }
            DB::commit();
            return response()->json([
                'message' => 'Guru berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus guru',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get a single kepala sekolah as JSON
    public function getKepalasekolah($id)
    {
        $kepala = \App\Models\KepalaSekolah::with('user')->find($id);
        if (!$kepala) {
            return response()->json(['message' => 'Kepala Sekolah not found'], 404);
        }
        return response()->json($kepala);
    }

    // Update an existing kepala sekolah
    public function updateKepalasekolah(Request $request, $id)
    {
        $kepala = \App\Models\KepalaSekolah::find($id);
        if (!$kepala) {
            return response()->json(['message' => 'Kepala Sekolah not found'], 404);
        }
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($kepala->user_id)
            ],
            'nip' => 'nullable|string|max:20',
            'jabatan' => 'nullable|string|max:100',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string',
            'status' => 'nullable|in:aktif,nonaktif'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        try {
            DB::beginTransaction();
            $kepala->update([
                'nama_lengkap' => $request->nama_lengkap,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat ?? $kepala->alamat,
                'nip' => $request->nip ?? $kepala->nip,
                'jabatan' => $request->jabatan ?? $kepala->jabatan
            ]);
            if ($kepala->user) {
                $kepala->user->update([
                    'name' => $request->nama_lengkap,
                    'email' => $request->email,
                    'status' => $request->status ?? $kepala->user->status
                ]);
            }
            DB::commit();
            return response()->json([
                'message' => 'Data kepala sekolah berhasil diperbarui',
                'data' => $kepala->fresh(['user'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui kepala sekolah',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete a kepala sekolah
    public function deleteKepalasekolah($id)
    {
        $kepala = \App\Models\KepalaSekolah::find($id);
        if (!$kepala) {
            return response()->json(['message' => 'Kepala Sekolah not found'], 404);
        }
        try {
            DB::beginTransaction();
            $userId = $kepala->user_id;
            $kepala->delete();
            if ($userId) {
                User::find($userId)->delete();
            }
            DB::commit();
            return response()->json([
                'message' => 'Kepala Sekolah berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus kepala sekolah',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the super admin profile page
     */
    public function profile()
    {
        $user = Auth::user();

        // Get or create super admin profile
        $superAdmin = \App\Models\SuperAdmin::where('user_id', $user->id)->first();

        if (!$superAdmin) {
            // Create a basic super admin profile if it doesn't exist
            $superAdmin = new \App\Models\SuperAdmin();
            $superAdmin->user_id = $user->id;
            $superAdmin->nama_lengkap = $user->name;
            $superAdmin->save();
        }

        return view('dashboard.superadmin.profile', compact('superAdmin'));
    }

    /**
     * Update super admin profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            $superAdmin = \App\Models\SuperAdmin::where('user_id', $user->id)->first();

            if (!$superAdmin) {
                $superAdmin = new \App\Models\SuperAdmin();
                $superAdmin->user_id = $user->id;
            }

            // Validation rules
            $validator = Validator::make($request->all(), [
                'nama_lengkap' => 'required|string|max:255',
                'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
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
            }

            // Update super admin data
            $superAdmin->fill([
                'nama_lengkap' => $request->nama_lengkap,
                'jenis_kelamin' => $request->jenis_kelamin,
            ]);
            $superAdmin->save();

            // Update user data
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'email' => $request->email,
                    'foto' => $fotoPath ? $fotoPath : $user->foto,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui',
                'data' => [
                    'nama_lengkap' => $superAdmin->nama_lengkap,
                    'jenis_kelamin' => $superAdmin->jenis_kelamin,
                    'email' => $request->email,
                    'foto_url' => $fotoPath ? asset('storage/user_photos/' . $fotoPath) : null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change password for super admin
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
            }

            // Update password
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
}
