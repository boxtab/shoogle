<?php

use App\Helpers\HelperMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserHasRewardDropIndexUserId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_has_reward', function (Blueprint $table) {

            if ( HelperMigration::hasUniqueKeyInTable('user_has_reward', 'user_has_reward_unique') ) {
                $table->dropUnique('user_has_reward_unique');
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
        Schema::table('user_has_reward', function (Blueprint $table) {

            if ( ! HelperMigration::hasUniqueKeyInTable('user_has_reward', 'user_has_reward_user_id_reward_id_unique') ) {
                $table->unique(['user_id', 'reward_id']);
            }

        });
    }
}
