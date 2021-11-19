<?php

use App\Helpers\HelperMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class TableUsersAddForeignRankId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $foreignKeys = HelperMigration::listTableForeignKeys('users');

            if ( ! in_array('users_rank_id_foreign', $foreignKeys) ) {

                $table->foreign('rank_id', 'users_rank_id_foreign')->references('id')->on('ranks');
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
        Schema::table('users', function (Blueprint $table) {

            $foreignKeys = HelperMigration::listTableForeignKeys('users');

            if ( in_array('users_rank_id_foreign', $foreignKeys) ) {
                $table->dropForeign('users_rank_id_foreign');
            }

        });
    }
}
