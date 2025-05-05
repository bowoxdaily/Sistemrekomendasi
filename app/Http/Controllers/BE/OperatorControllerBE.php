<?php

namespace App\Http\Controllers\BE;

use App\Http\Controllers\Controller;
use App\Models\Operators;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
}
