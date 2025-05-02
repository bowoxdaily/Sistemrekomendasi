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
        // Get current count
        $currentCount = User::where('role', 'siswa')->count();
        
        // Get count from 30 days ago for comparison
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $previousCount = User::where('role', 'siswa')
                            ->where('created_at', '<', $thirtyDaysAgo)
                            ->count();
        
        // Calculate percentage change
        $percentageChange = 0;
        if ($previousCount > 0) {
            $percentageChange = (($currentCount - $previousCount) / $previousCount) * 100;
        }
        
        return response()->json([
            'current_count' => $currentCount,
            'percentage_change' => round($percentageChange, 2),
            'days' => 30
        ]);
    }
    public function edit()
    {
        $user = Auth::user();
        $student = Students::where('user_id', $user->id)->first();
        
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
        $user = Auth::user();
        $student = Students::where('user_id', $user->id)->first();
        
        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Profil siswa tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $student
        ]);
    }
    public function updateProfile(Request $request)
{
    // Check if user is authenticated
    if (!Auth::check()) {
        return response()->json([
            'status' => 'error',
            'message' => 'User not authenticated'
        ], 401);
    }

    // Get authenticated user
    $user = Auth::user();
    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'User not found'
        ], 404);
    }

    // Validate the input
    $validator = Validator::make($request->all(), [
        'nama_lengkap' => 'required|string|max:255',
        'tempat_lahir' => 'required|string|max:255',
        'tanggal_lahir' => 'required|date',
        'nisn' => 'required|string',
        'alamat' => 'required|string',
        'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        // Get or create student record
        $student = Students::where('user_id', $user->id)->first();

        if (!$student) {
            // Create new student record if it doesn't exist
            $student = new Students();
            $student->user_id = $user->id;
        }

        // Handle file upload if a new photo is provided
        // Handle file upload if a new photo is provided
if ($request->hasFile('foto')) {
    // Delete old photo if exists
    if ($user->foto) {
        Storage::delete('public/user_photos/' . $user->foto);
    }

    // Store the new photo
    $photoName = time() . '.' . $request->foto->extension();
    $request->foto->storeAs('public/user_photos', $photoName);
    $user->foto = $photoName;
    $user->save(); // simpan ke tabel users
}


        // Update student profile
        $student->nama_lengkap = $request->nama_lengkap;
        $student->tempat_lahir = $request->tempat_lahir;
        $student->tanggal_lahir = $request->tanggal_lahir;
        $student->nisn = $request->nisn;
        $student->alamat = $request->alamat;
        $student->jenis_kelamin = $request->jenis_kelamin;
        
       
        $student->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui!',
            'data' => $student
        ]);
    } catch (\Exception $e) {
        // Log the exception
        
        
        return response()->json([
            'status' => 'error',
            'message' => 'Terjadi kesalahan saat memperbarui profil: ' . $e->getMessage()
        ], 500);
    }
}

}
