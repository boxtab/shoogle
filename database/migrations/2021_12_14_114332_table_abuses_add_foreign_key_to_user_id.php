<?php

use App\Helpers\HelperMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableAbusesAddForeignKeyToUserId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('abuses', function (Blueprint $table) {

            $foreignKeys = HelperMigration::listTableForeignKeys('abuses');

            if ( ! in_array('abuses_to_user_id_foreign', $foreignKeys) ) {

                $table->foreign('to_user_id', 'abuses_to_user_id_foreign')
                    ->references('id')
                    ->on('users');
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
        Schema::table('abuses', function (Blueprint $table) {
            $foreignKeys = HelperMigration::listTableForeignKeys('abuses');

            if ( in_array('abuses_to_user_id_foreign', $foreignKeys) ) {
                $table->dropForeign('abuses_to_user_id_foreign');
            }
        });
    }
}
