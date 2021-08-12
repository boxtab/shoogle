<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddConstraintIntellectualWellbeingScores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wellbeing_scores', function (Blueprint $table) {
            DB::statement('ALTER TABLE wellbeing_scores ADD CONSTRAINT check_wellbeing_scores_intellectual CHECK (intellectual >= 1 and intellectual <= 10);');
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
            DB::statement('ALTER TABLE wellbeing_scores DROP CHECK check_wellbeing_scores_intellectual;');
        });
    }
}
