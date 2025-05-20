<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'password' => Hash::make('123'), // Menggunakan Hash::make() untuk password
            'no_telp' => '081234567890',
            'email' => 'operator1@example.com', // Menambahkan email
            'role' => 'operator',
            'foto' => 'Default',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add our new seeders to the seeding process
        $this->call([
            JobRecommendationSeeder::class,
            QuestionnaireQuestionSeeder::class,
            JurusanSeeder::class,
        ]);
    }
}
