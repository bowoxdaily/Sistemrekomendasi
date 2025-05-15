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
        Schema::create('questionnaire_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('questionnaire_id');
            $table->text('question_text');
            $table->string('question_type'); // multiple_choice, scale, text
            $table->json('options')->nullable(); // Untuk pertanyaan pilihan ganda
            $table->float('weight')->default(1); // Bobot untuk perhitungan SAW
            $table->string('criteria_type')->default('benefit'); // benefit atau cost untuk SAW
            $table->timestamps();

            $table->foreign('questionnaire_id')->references('id')->on('questionnaires')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaire_questions');
    }
};
