<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHasCompletedQuestionnaireToStudents extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->boolean('has_completed_questionnaire')->default(false);
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('has_completed_questionnaire');
        });
    }
}
