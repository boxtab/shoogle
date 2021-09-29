<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserHasShoogleAddField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_has_shoogle', function (Blueprint $table) {
            $table->dateTime('reminder')->nullable()->after('solo');
            $table->string('reminder_interval', 1024)->nullable()->after('reminder');
            $table->boolean('is_reminder')->default(false)->after('reminder_interval');
            $table->boolean('is_repetitive')->default(false)->after('is_reminder');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_has_shoogle', function (Blueprint $table) {
            $table->dropColumn('reminder');
            $table->dropColumn('reminder_interval');
            $table->dropColumn('is_reminder');
            $table->dropColumn('is_repetitive');
        });
    }
}
