<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([

            'password' => Hash::make('123'), 
            'no_telp' => '081234567890',
            'email' => 'superadmin@gmail.com', // Menambahkan email
            'role' => 'superadmin',
            'foto' => 'Default',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
