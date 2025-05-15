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
        Schema::create('job_recommendation_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('questionnaire_response_id');
            $table->unsignedBigInteger('job_recommendation_id');
            $table->float('score');
            $table->integer('rank');
            $table->timestamps();

            $table->foreign('questionnaire_response_id')->references('id')->on('questionnaire_responses')->onDelete('cascade');
            $table->foreign('job_recommendation_id')->references('id')->on('job_recommendations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_job_recommendation_results');
    }
};
