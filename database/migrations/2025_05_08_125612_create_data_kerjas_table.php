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
        Schema::create('data_kerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('nama_perusahaan');
            $table->string('posisi');
            $table->string('jenis_pekerjaan');
            $table->date('tanggal_mulai');
            $table->integer('gaji')->nullable();
            $table->enum('sesuai_jurusan', ['ya', 'tidak']);
            $table->text('kompetensi_dibutuhkan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_kerjas');
    }
};
