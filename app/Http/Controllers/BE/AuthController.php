<?php

namespace App\Http\Controllers\BE;

use App\Http\Controllers\Controller;
use App\Models\Students;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'no_telp' => 'required|string|max:15',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:siswa', // Ensure only siswa role can register via this endpoint
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create new user
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'no_telp' => $request->no_telp,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            // Generate token for the new user (optional)
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token, // Include token if using sanctum/passport
                ]
            ], 201);
        } catch (\Exception $e) {
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function Login(Request $request)
    {
        $login = $request->input('login'); // Input bisa berupa email atau NISN
        $password = $request->input('password');

        // Cek apakah login dengan NISN (hanya angka)
        $isNisn = is_numeric($login);

        if ($isNisn) {
            // Jika login menggunakan NISN
            // Cari user_id berdasarkan NISN di tabel students
            $student = Students::where('nisn', $login)->first();

            if ($student) {
                // Jika siswa ditemukan, ambil user_id-nya dan coba login
                $user = User::find($student->user_id);

                if ($user && Auth::attempt(['email' => $user->email, 'password' => $password])) {
                    // Pastikan yang login dengan NISN adalah siswa
                    if ($user->role == 'siswa') {
                        // Untuk mencegah redirect loop, jangan redirect ke route yang dilindungi middleware
                        // yang melakukan pengecekan profil yang sama
                        session()->flash('success', 'Login Berhasil! Selamat Datang, Siswa!');

                        // Cek apakah profil siswa sudah diisi
                        if (!$this->isProfileComplete($student)) {
                            session()->flash('warning', 'Mohon lengkapi profil Anda terlebih dahulu.');
                            // Redirect langsung ke view edit profil tanpa melalui middleware
                            return redirect()->route('student.profile.edit')->withoutMiddleware('check.student.profile');
                        }

                        return redirect()->route('dashboard');
                    } else {
                        Auth::logout();
                        session()->flash('error', 'NISN hanya untuk login siswa');
                        return redirect()->back()->withInput($request->only('login'));
                    }
                }
            }
        } else {
            // Login dengan email (untuk semua role)
            if (Auth::attempt(['email' => $login, 'password' => $password])) {
                $user = Auth::user();

                // Pesan berbeda berdasarkan role
                $welcomeMessage = 'Login Berhasil! Selamat Datang';

                if ($user->role == 'siswa') {
                    // Cek profil siswa jika role = siswa
                    $student = Students::where('user_id', $user->id)->first();

                    if (!$student || !$this->isProfileComplete($student)) {
                        session()->flash('warning', 'Mohon lengkapi profil Anda terlebih dahulu.');
                        // Redirect langsung ke view edit profil tanpa melalui middleware
                        return redirect()->route('student.profile.edit')->withoutMiddleware('check.student.profile');
                    }

                    $welcomeMessage = 'Login Berhasil! Selamat Datang, Siswa!';
                    session()->flash('success', $welcomeMessage);
                    return redirect()->route('dashboard');
                } elseif ($user->role == 'guru') {
                    $welcomeMessage = 'Login Berhasil! Selamat Datang, Guru!';
                    session()->flash('success', $welcomeMessage);
                    return redirect()->route('dashboard');
                } elseif ($user->role == 'kepalasekolah') {
                    $welcomeMessage = 'Login Berhasil! Selamat Datang, Kepala Sekolah!';
                    session()->flash('success', $welcomeMessage);
                    return redirect()->route('dashboard');
                } elseif ($user->role == 'operator') {
                    $welcomeMessage = 'Login Berhasil! Selamat Datang, Operator!';
                    session()->flash('success', $welcomeMessage);
                    return redirect()->route('dashboard');
                } else {
                    session()->flash('success', $welcomeMessage);
                    return redirect()->route('dashboard');
                }
            }
        }

        // Jika gagal login dengan NISN atau email
        session()->flash('error', 'NISN/Email atau password tidak valid');
        return redirect()->back()->withInput($request->only('login'));
    }

    /**
     * Memeriksa apakah profil siswa sudah lengkap
     * 
     * @param Students $student
     * @return bool
     */
    private function isProfileComplete(Students $student)
    {
        // Menggunakan kriteria yang sama dengan checkProfile()
        $requiredFields = [
            'nama_lengkap',
            'tempat_lahir',
            'tanggal_lahir',
            'alamat',
            'jenis_kelamin'
        ];

        // Check if all required fields are filled
        foreach ($requiredFields as $field) {
            if (empty($student->$field)) {
                return false;
            }
        }

        return true;
    }
    public function changePassword(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            // Get the authenticated user
            $user = Auth::user();

            // Check if the current password matches
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect',
                ], 422);
            }

            // Update the password
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while changing password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        return redirect()->route('login');
    }
}
