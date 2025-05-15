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
        Schema::create('job_recommendations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->json('requirements')->nullable();
            $table->json('skills_needed')->nullable();
            $table->string('average_salary')->nullable();
            $table->string('industry_type')->nullable();
            $table->json('criteria_values')->nullable(); // Nilai kriteria untuk perhitungan SAW
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_recommendations');
    }
};
