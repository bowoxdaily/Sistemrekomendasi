<?php

namespace App\Http\Controllers\BE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Operators;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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

    // Get all operators as JSON
    public function getOperators()
    {
        $operators = Operators::with('user')->get();
        return response()->json($operators);
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

    // Create a new operator
    public function storeOperator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'nama_lengkap' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'no_hp' => 'nullable|string|max:15',
            'nip' => 'nullable|string|max:20',
            'jabatan' => 'nullable|string|max:100',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create user account first
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'operator',
                'status' => 'aktif'
            ]);

            // Create operator profile
            $operator = Operators::create([
                'user_id' => $user->id,
                'nama_lengkap' => $request->nama_lengkap,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat ?? null,
                'nip' => $request->nip ?? null,
                'jabatan' => $request->jabatan ?? null
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Operator berhasil ditambahkan',
                'data' => $operator
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat menambahkan operator',
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
}
