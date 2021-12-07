<?php

use App\Helpers\HelperMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableUsersDropUniqueKeyEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if ( HelperMigration::hasUniqueKeyInTable('users', 'users_email_unique') ) {
                $table->dropUnique(['email']);
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
            if ( ! HelperMigration::hasUniqueKeyInTable('users', 'users_email_unique') ) {
                $table->unique('email');
            }
        });
    }
}
