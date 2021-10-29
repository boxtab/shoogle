<?php

use App\Helpers\HelperMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserDeleteForeignKeyInviteId extends Migration
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
            if ( in_array('users_department_id_foreign', $foreignKeys) ) {
                $table->dropForeign('users_department_id_foreign');
            }

            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('users');

            if ( $doctrineTable->hasIndex('users_invite_id_foreign_custom') ) {
                $table->dropIndex('users_invite_id_foreign_custom');
            }
            if ( $doctrineTable->hasIndex('users_department_id_foreign') ) {
                $table->dropIndex('users_department_id_foreign');
            }

            if ( Schema::hasColumn('users', 'invite_id') ) {
                $table->dropColumn('invite_id');
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
            //
        });
    }
}
