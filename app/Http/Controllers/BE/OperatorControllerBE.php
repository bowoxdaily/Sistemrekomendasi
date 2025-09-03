<?php

namespace App\Http\Controllers\BE;

use App\Exports\AkunSiswaTemplate;
use App\Http\Controllers\Controller;
use App\Imports\AkunSiswaImport;
use App\Models\Operators;
use App\Models\Students;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class OperatorControllerBE extends Controller
{
    public function updateProfile(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'jabatan' => 'required|string|max:100',
            'jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan',
            'alamat' => 'required|string',
            'foto' => 'nullable|image|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Get current user
        $user = Auth::user();

        // Find or create operator record
        $operator = Operators::firstOrNew(['user_id' => $user->id]);

        // Update operator details
        $operator->nama_lengkap = $request->nama_lengkap;
        $operator->jabatan = $request->jabatan;
        $operator->jenis_kelamin = $request->jenis_kelamin;
        $operator->alamat = $request->alamat;

        // Only set NIP if it's not already set
        if (!$operator->nip && $request->nip) {
            $operator->nip = $request->nip;
        }

        $operator->save();

        // Handle profile photo upload
        $fotoUrl = null;
        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($user->foto) {
                Storage::disk('public')->delete('user_photos/' . $user->foto);
            }

            // Save new photo
            $file = $request->file('foto');
            $fileName = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/user_photos', $fileName);

            // Update user photo field
            $user->foto = $fileName;
            $user->save();

            $fotoUrl = asset('storage/user_photos/' . $fileName);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'updatedData' => $operator,
            'foto_url' => $fotoUrl
        ]);
    }

    public function getProfile()
    {
        try {
            $user = Auth::user();
            $operator = Operators::where('user_id', $user->id)->first();

            return response()->json([
                'success' => true,
                'data' => $operator,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data profil: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSiswaData()
    {
        $data = Students::select('id', 'nama_lengkap', 'nisn', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'jurusan_id', 'alamat', 'created_at', 'tanggal_lulus')->get();

        return response()->json($data);
    }

    public function tambahAkunSiswa(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'nama_lengkap' => 'required',
                'jurusan_id' => 'required|exists:jurusans,id',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'nisn' => 'required|unique:students,nisn',
                'status_lulus' => 'required|in:belum,lulus',
                'tanggal_lulus' => 'nullable|required_if:status_lulus,lulus', // Only required if status is "lulus"
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first()], 422);
            }

            // Mulai transaksi database
            DB::beginTransaction();

            // Buat user baru
            $user = new User();
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role = 'siswa';
            $user->save();

            // Buat data siswa baru
            $student = new Students();
            $student->user_id = $user->id;
            $student->nama_lengkap = $request->nama_lengkap;
            $student->nisn = $request->nisn;
            $student->jurusan_id = $request->jurusan_id;
            $student->status_lulus = $request->status_lulus;

            // Handle tanggal_lulus conditionally
            if ($request->status_lulus === 'lulus') {
                $tanggal_lulus = $request->tanggal_lulus;
                // If it's just a year, convert to YYYY-01-01 format
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_lulus) && is_numeric($tanggal_lulus)) {
                    $tanggal_lulus = $tanggal_lulus . '-01-01';
                }
                $student->tanggal_lulus = $tanggal_lulus;
            } else {
                $student->tanggal_lulus = null; // Set to null if status is "belum"
            }

            $student->save();

            DB::commit();

            return response()->json([
                'message' => 'Akun siswa berhasil dibuat',
                'data' => [
                    'user_id' => $user->id,
                    'student_id' => $student->id
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Gagal membuat akun siswa: ' . $e->getMessage()], 500);
        }
    }

    public function downloadTemplateAkunSiswa()
    {
        return Excel::download(new AkunSiswaTemplate, 'template-akun-siswa.xlsx');
    }
    public function importAkunSiswa(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Import using Laravel Excel
            $import = new AkunSiswaImport;
            Excel::import($import, $request->file('file'));

            // Get results and check for errors
            $results = $import->getResults();

            if (!empty($results['errors'])) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Terdapat kesalahan pada data import',
                    'errors' => $results['errors']
                ], 422);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Import akun siswa berhasil! ' . $results['success'] . ' data berhasil diimport.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimport data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSiswaById($id)
    {
        try {
            // Log the incoming ID for debugging


            // Try to fetch the student
            $siswa = Students::find($id);

            // Log whether student was found
            if (!$siswa) {

                return response()->json(['message' => 'Data siswa tidak ditemukan'], 404);
            }


            return response()->json($siswa, 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Gagal mengambil data siswa: ' . $e->getMessage()], 500);
        }
    }

    // Add this new method to handle student updates
    public function updateSiswa(Request $request, $id)
    {
        try {
            // Find the student
            $siswa = Students::findOrFail($id);

            // Validate input
            $validator = Validator::make($request->all(), [
                'nama_lengkap' => 'required|string|max:255',
                'nisn' => 'required|string',
                'jurusan_id' => 'required|exists:jurusans,id',
                'status_lulus' => 'required|in:belum,lulus',
                'tempat_lahir' => 'required|string|max:255',
                'tanggal_lahir' => 'required|date',
                'alamat' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update the student
            $siswa->nama_lengkap = $request->nama_lengkap;
            $siswa->nisn = $request->nisn;
            $siswa->jurusan_id = $request->jurusan_id;
            $siswa->status_lulus = $request->status_lulus;
            $siswa->tempat_lahir = $request->tempat_lahir;
            $siswa->tanggal_lahir = $request->tanggal_lahir;
            $siswa->alamat = $request->alamat;

            // Handle graduation date
            if ($request->status_lulus === 'lulus') {
                if ($request->has('tanggal_lulus')) {
                    $siswa->tanggal_lulus = $request->tanggal_lulus;
                } elseif ($request->has('tahun_lulus')) {
                    // Convert year to date format
                    $siswa->tanggal_lulus = $request->tahun_lulus . '-01-01';
                }
            } else {
                $siswa->tanggal_lulus = null;
            }

            $siswa->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data siswa berhasil diperbarui',
                'data' => $siswa
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui data siswa: ' . $e->getMessage()
            ], 500);
        }
    }





    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $siswa = Students::findOrFail($id);

            // Delete the associated user account if it exists
            if ($siswa->user_id) {
                User::where('id', $siswa->user_id)->delete();
            }

            // Delete the student record
            $siswa->delete();

            DB::commit();
            return response()->json(['message' => 'Data siswa berhasil dihapus'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menghapus data siswa: ' . $e->getMessage()], 500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();

            // Check if current password is correct
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password saat ini tidak benar'
                ], 422);
            }

            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah password: ' . $e->getMessage()
            ], 500);
        }
    }
}
