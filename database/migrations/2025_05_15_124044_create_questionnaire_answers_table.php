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
        Schema::create('questionnaire_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('questionnaire_response_id');
            $table->unsignedBigInteger('questionnaire_question_id');
            $table->float('answer_value')->nullable(); // Nilai untuk perhitungan
            $table->text('answer_text')->nullable(); // Teks jawaban
            $table->timestamps();

            $table->foreign('questionnaire_response_id')->references('id')->on('questionnaire_responses')->onDelete('cascade');
            $table->foreign('questionnaire_question_id')->references('id')->on('questionnaire_questions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaire_answers');
    }
};
