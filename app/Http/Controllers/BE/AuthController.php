<?php

namespace App\Http\Controllers\BE;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        if (in_array($user->role, ['siswa', 'guru', 'operator'])) {
            return redirect()->route('dashboard')->with('success', 'Login Berhasil! Selamat Datang');
        } else {
            return redirect()->route('dashboard')->with('success', 'Login Berhasil!');
        }
    }

    return redirect()->back()->with('error', 'Email atau password salah!');
}

    public function logout(Request $request)
        {
            Auth::guard('web')->logout();

            $request->session()->invalidate();

            return redirect()->route('login');
        }
        
}
