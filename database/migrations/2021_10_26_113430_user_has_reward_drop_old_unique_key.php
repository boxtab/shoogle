<?php

use App\Helpers\HelperMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserHasRewardDropOldUniqueKey extends Migration
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
            if ( in_array('user_has_reward_user_id_foreign', $foreignKeys) ) {
                $table->dropForeign('user_has_reward_user_id_foreign');
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
            if ( ! in_array('user_has_reward_user_id_foreign', $foreignKeys) ) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('no action')
                    ->onDelete('no action');
            }

        });
    }
}
