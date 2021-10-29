<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserHasRewardDropIndexewardId extends Migration
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
            $doctrineTable = $sm->listTableDetails('user_has_reward');
            if ( $doctrineTable->hasIndex('user_has_reward_reward_id_foreign') ) {
                $table->dropIndex('user_has_reward_reward_id_foreign');
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

            if ( ! $doctrineTable->hasIndex('user_has_reward_reward_id_foreign') ) {
                $table->index('user_has_reward_reward_id_foreign');
            }

        });
    }
}
