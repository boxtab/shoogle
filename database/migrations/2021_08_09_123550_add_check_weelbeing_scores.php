<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCheckWeelbeingScores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE wellbeing_scores ADD CONSTRAINT check_wellbeing_scores_social CHECK (social >= 1 and social <= 10);');
        DB::statement('ALTER TABLE wellbeing_scores ADD CONSTRAINT check_wellbeing_scores_physical CHECK (physical >= 1 and physical <= 10);');
        DB::statement('ALTER TABLE wellbeing_scores ADD CONSTRAINT check_wellbeing_scores_mental CHECK (mental >= 1 and mental <= 10);');
        DB::statement('ALTER TABLE wellbeing_scores ADD CONSTRAINT check_wellbeing_scores_economical CHECK (economical >= 1 and economical <= 10);');
        DB::statement('ALTER TABLE wellbeing_scores ADD CONSTRAINT check_wellbeing_scores_emotional CHECK (emotional >= 1 and emotional <= 10);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE wellbeing_scores DROP CHECK check_wellbeing_scores_social;');
        DB::statement('ALTER TABLE wellbeing_scores DROP CHECK check_wellbeing_scores_physical;');
        DB::statement('ALTER TABLE wellbeing_scores DROP CHECK check_wellbeing_scores_mental;');
        DB::statement('ALTER TABLE wellbeing_scores DROP CHECK check_wellbeing_scores_economical;');
        DB::statement('ALTER TABLE wellbeing_scores DROP CHECK check_wellbeing_scores_emotional;');
    }
}
