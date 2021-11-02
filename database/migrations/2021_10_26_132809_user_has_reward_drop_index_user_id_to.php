<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserHasRewardDropIndexUserIdTo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_has_reward', function (Blueprint $table) {

            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes('user_has_reward_user_id_foreign');

            if ( array_key_exists('user_has_reward_user_id_foreign', $indexesFound) ) {
                $table->dropIndex('user_has_reward_user_id_foreign');
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

            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('user_has_reward');

            if ( ! $doctrineTable->hasIndex('user_has_reward_user_id_foreign') ) {
                $table->index('user_has_reward_user_id_foreign');
            }

        });
    }
}
