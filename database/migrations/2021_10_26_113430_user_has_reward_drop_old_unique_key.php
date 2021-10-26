<?php

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

            $table->dropForeign('user_has_reward_user_id_foreign');

//            $table->dropForeign('user_has_reward_user_id_foreign');
//            $table->dropIndex('user_has_reward_user_id_foreign');

        });

//        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
//        Schema::table('user_has_reward', function (Blueprint $table) {
//            $table->dropForeign('user_has_reward_user_id_foreign');
//            $table->dropIndex('user_has_reward_user_id_foreign');

//            $table->dropForeign('user_has_reward_reward_id_foreign');
//            $table->dropIndex('user_has_reward_reward_id_foreign');

//            $table->dropUnique('user_has_reward_unique');
//        });
//        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

//        DB::unprepared("SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES';");

//        DB::unprepared("SET FOREIGN_KEY_CHECKS=0;");
//        DB::unprepared("ALTER TABLE user_has_reward DROP INDEX user_has_reward_unique;");
//        DB::unprepared("DROP INDEX user_has_reward_unique ON user_has_reward;");
//        DB::unprepared("SET FOREIGN_KEY_CHECKS=1;");

//        Schema::table('user_has_reward', function (Blueprint $table) {

//            Schema::disableForeignKeyConstraints();



//            $table->dropUnique('user_has_reward_unique');


//            Schema::enableForeignKeyConstraints();

//            $table->dropUnique(['reward_id', 'user_id', ]);
//            $table->dropUnique('user_has_reward_unique');
//            $table->dropUnique('user_has_reward_user_id_reward_id_unique');
//        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_has_reward', function (Blueprint $table) {

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('no action')
                ->onDelete('no action');

//            $table->foreign('reward_id')
//                ->references('id')
//                ->on('rewards')
//                ->onUpdate('no action')
//                ->onDelete('no action');
        });

//        DB::unprepared('ALTER TABLE user_has_reward
//                              ADD CONSTRAINT user_has_reward_unique
//                              UNIQUE (user_id,reward_id);');

//        DB::unprepared("SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES';");
//
//        Schema::table('user_has_reward', function (Blueprint $table) {
//            Schema::disableForeignKeyConstraints();
//            $table->unique(['user_id', 'reward_id'], 'user_has_reward_unique');
//            Schema::enableForeignKeyConstraints();
//        });
    }
}
