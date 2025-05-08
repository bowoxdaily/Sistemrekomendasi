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
        Schema::create('data_kuliahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('nama_pt');
            $table->string('jurusan');
            $table->string('jenjang');
            $table->year('tahun_masuk');
            $table->enum('status_beasiswa', ['ya', 'tidak']);
            $table->string('nama_beasiswa')->nullable();
            $table->text('prestasi_akademik')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_kuliahs');
    }
};
