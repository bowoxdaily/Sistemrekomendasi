<?php

namespace App\Http\Controllers\BE;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JurusanController extends Controller
{
    public function getAllJurusan (){
        try {
            $jurusan = Jurusan::orderBy('nama', 'asc')->get();
            return response()->json($jurusan);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memuat data: ' . $e->getMessage()], 500);
        }
    }

        public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kode' => 'required|string|max:10|unique:jurusans,kode',
                'nama' => 'required|string|max:100',
                'deskripsi' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $jurusan = Jurusan::create([
                'kode' => $request->kode,
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
            ]);

            return response()->json([
                'message' => 'Jurusan berhasil ditambahkan',
                'data' => $jurusan
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menambahkan jurusan: ' . $e->getMessage()], 500);
        }
    }
    public function getJurusanById($id)
    {
        try {
            $jurusan = Jurusan::findOrFail($id);
            return response()->json($jurusan);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Data jurusan tidak ditemukan'], 404);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $jurusan = Jurusan::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'kode' => 'required|string|max:10|unique:jurusans,kode,' . $id,
                'nama' => 'required|string|max:100',
                'deskripsi' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $jurusan->update([
                'kode' => $request->kode,
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
            ]);

            return response()->json([
                'message' => 'Data jurusan berhasil diperbarui',
                'data' => $jurusan
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return response()->json(['message' => 'Data jurusan tidak ditemukan'], 404);
            }
            return response()->json(['message' => 'Gagal memperbarui data jurusan: ' . $e->getMessage()], 500);
        }
    }

     public function destroy($id)
    {
        try {
            $jurusan = Jurusan::findOrFail($id);
            $jurusan->delete();

            return response()->json([
                'message' => 'Data jurusan berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return response()->json(['message' => 'Data jurusan tidak ditemukan'], 404);
            }
            return response()->json(['message' => 'Gagal menghapus data jurusan: ' . $e->getMessage()], 500);
        }
    }
}
