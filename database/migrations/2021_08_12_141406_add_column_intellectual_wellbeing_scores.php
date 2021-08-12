<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIntellectualWellbeingScores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wellbeing_scores', function (Blueprint $table) {
            $table->unsignedTinyInteger('intellectual')->nullable()->after('emotional');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wellbeing_scores', function (Blueprint $table) {
            $table->dropColumn('intellectual');
        });
    }
}
