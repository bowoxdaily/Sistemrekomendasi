<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArchivedFieldToQuestionnaireResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questionnaire_responses', function (Blueprint $table) {
            if (!Schema::hasColumn('questionnaire_responses', 'archived')) {
                $table->boolean('archived')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questionnaire_responses', function (Blueprint $table) {
            if (Schema::hasColumn('questionnaire_responses', 'archived')) {
                $table->dropColumn('archived');
            }
        });
    }
}
