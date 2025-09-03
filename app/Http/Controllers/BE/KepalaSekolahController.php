<?php

namespace App\Http\Controllers\BE;

use App\Http\Controllers\Controller;
use App\Models\KepalaSekolah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class KepalaSekolahController extends Controller
{
    /**
     * Display the profile page
     */
    public function profile()
    {
        $user = Auth::user();
        $kepalaSekolah = KepalaSekolah::where('user_id', $user->id)->first();

        return view('dashboard.kepalasekolah.profile', compact('kepalaSekolah'));
    }

    /**
     * Update the profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'jabatan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:500',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            // Update user email if changed
            if ($request->email !== $user->email) {
                $user->update(['email' => $request->email]);
            }

            // Handle photo upload
            if ($request->hasFile('foto')) {
                // Delete old photo if exists
                if ($user->foto && Storage::disk('public')->exists('user_photos/' . $user->foto)) {
                    Storage::disk('public')->delete('user_photos/' . $user->foto);
                }

                // Store new photo
                $photoName = time() . '_' . $request->file('foto')->getClientOriginalName();
                $request->file('foto')->storeAs('user_photos', $photoName, 'public');
                $user->update(['foto' => $photoName]);
            }

            // Update or create KepalaSekolah profile
            KepalaSekolah::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama_lengkap' => $request->nama_lengkap,
                    'nip' => $request->nip,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'jabatan' => $request->jabatan ?: 'Kepala Sekolah',
                    'alamat' => $request->alamat,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini tidak benar!'
            ], 422);
        }

        try {
            // Update password
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $kepalaSekolah = KepalaSekolah::where('user_id', $user->id)->first();

        return view('dashboard.kepalasekolah.index', compact('kepalaSekolah'));
    }
}
