<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UsersPasswordRecoveryCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if ( ! Schema::hasColumn('users', 'password_recovery_code') ) {
                $table->string('password_recovery_code', 255)->nullable()->after('password');
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
            if ( Schema::hasColumn('users', 'password_recovery_code') ) {
                $table->dropColumn('password_recovery_code');
            }
        });
    }
}
