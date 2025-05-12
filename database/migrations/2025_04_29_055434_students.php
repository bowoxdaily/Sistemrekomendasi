<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_lengkap')->nullable();
            $table->string('nisn')->unique();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->text('alamat')->nullable();
            $table->foreignId('jurusan_id')->nullable()->constrained('jurusans');
            $table->enum('status_lulus', ['belum', 'lulus'])->default('belum');
            $table->date('tanggal_lulus')->nullable();
            $table->enum('status_setelah_lulus', ['belum_kerja', 'kuliah', 'kerja'])->nullable();
            $table->timestamp('status_terakhir_diupdate')->nullable();
            $table->boolean('is_profile_complete')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
