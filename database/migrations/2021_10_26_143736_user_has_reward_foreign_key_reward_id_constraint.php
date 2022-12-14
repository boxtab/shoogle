<?php

use App\Helpers\HelperMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserHasRewardForeignKeyRewardIdConstraint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_has_reward', function (Blueprint $table) {

            $foreignKeys = HelperMigration::listTableForeignKeys('user_has_reward');
            if ( ! in_array('user_has_reward_reward_id_foreign', $foreignKeys) ) {
                $table->foreign('reward_id')
                    ->references('id')
                    ->on('rewards')
                    ->onUpdate('no action')
                    ->onDelete('no action');
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

            $foreignKeys = HelperMigration::listTableForeignKeys('user_has_reward');
            if ( in_array('user_has_reward_reward_id_foreign', $foreignKeys) ) {
                $table->dropForeign('user_has_reward_reward_id_foreign');
            }

        });
    }
}
