<?php

use App\Helpers\HelperMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InvitesRefUserId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invites', function (Blueprint $table) {

            if ( ! Schema::hasColumn('invites', 'user_id') ) {
                $table->unsignedBigInteger('user_id')->nullable()->after('department_id');
            }

            $foreignKeys = HelperMigration::listTableForeignKeys('invites');
            if ( ! in_array('invites_user_id_foreign', $foreignKeys) ) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::table('invites', function (Blueprint $table) {

            $foreignKeys = HelperMigration::listTableForeignKeys('invites');
            if ( in_array('invites_user_id_foreign', $foreignKeys) ) {
                $table->dropForeign('invites_user_id_foreign');
            }

            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('invites');
            if ( $doctrineTable->hasIndex('invites_user_id_index') ) {
                $table->dropIndex('invites_user_id_index');
            }

            if ( Schema::hasColumn('invites', 'user_id') ) {
                $table->dropColumn('user_id');
            }

        });
    }
}
