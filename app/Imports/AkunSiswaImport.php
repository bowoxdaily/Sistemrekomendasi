<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Students;
use App\Models\Jurusan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\DB;

class AkunSiswaImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $results = [
        'success' => 0,
        'errors' => []
    ];

    public function collection(Collection $collection)
    {
        foreach ($collection as $index => $row) {
            $rowNumber = $index + 2;

            DB::beginTransaction();

            try {
                // Find jurusan by kode or nama
                $jurusan = Jurusan::where('kode', $row['jurusan'])
                    ->orWhere('nama', $row['jurusan'])
                    ->first();
                
                if (!$jurusan) {
                    throw new \Exception("Jurusan '{$row['jurusan']}' tidak ditemukan");
                }

                $user = User::create([
                    'email' => $row['email'],
                    'password' => Hash::make($row['password']),
                    'role' => 'siswa',
                ]);

                Students::create([
                    'user_id' => $user->id,
                    'nisn' => $row['nisn'],
                    'nama_lengkap' => $row['nama_lengkap'] ?? null,
                    'jurusan_id' => $jurusan->id, // Using jurusan_id instead of jurusan
                    'tempat_lahir' => null,
                    'tanggal_lahir' => null,
                    'alamat' => null
                ]);

                DB::commit();
                $this->results['success']++;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->results['errors'][] = "Baris {$rowNumber}: " . $e->getMessage();
            }
        }
    }

    public function rules(): array
    {
        return [
            'nisn' => 'required|unique:students,nisn',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'nama_lengkap' => 'required|string',
            'jurusan' => 'required|string'
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nisn.required' => 'NISN wajib diisi',
            'nisn.unique' => 'NISN sudah terdaftar',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'jurusan.required' => 'Jurusan wajib diisi',
        ];
    }

    public function getResults()
    {
        return $this->results;
    }
}