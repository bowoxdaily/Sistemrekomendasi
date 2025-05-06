<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Students;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\DB;

class AkunSiswaImport implements ToCollection, WithHeadingRow, WithValidation
{
    // Store results for reporting back to the controller
    protected $results = [
        'success' => 0,
        'errors' => []
    ];

    /**
     * Process the imported collection of rows
     * 
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $index => $row) {
            // Calculate the actual row number in the Excel file for error reporting
            $rowNumber = $index + 2; // +1 for header, +1 for 0-indexing

            // Use a database transaction to ensure data integrity
            DB::beginTransaction();

            try {
                // Create user account first
                $user = User::create([
                    'email' => $row['email'],
                    'password' => Hash::make($row['password']),
                    'role' => 'siswa', // Set default role
                ]);

                // Create student record linked to the user
                Students::create([
                    'user_id' => $user->id, // Link to user
                    'nisn' => $row['nisn'],
                    // Set other fields to null - they can be filled later
                    'nama_lengkap' => null,
                    'tempat_lahir' => null,
                    'tanggal_lahir' => null,
                    'alamat' => null
                ]);

                // If we got here, both user and student were created successfully
                DB::commit();
                $this->results['success']++;
            } catch (\Exception $e) {
                // Something went wrong, rollback the transaction
                DB::rollBack();
                $this->results['errors'][] = "Baris {$rowNumber}: " . $e->getMessage();
            }
        }
    }

    /**
     * Define validation rules for each row
     * 
     * @return array
     */
    public function rules(): array
    {
        return [
            'nisn' => 'required|unique:students,nisn',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ];
    }

    /**
     * Custom validation messages
     * 
     * @return array
     */
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
        ];
    }

    /**
     * Return the results to the controller
     * 
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }
}
