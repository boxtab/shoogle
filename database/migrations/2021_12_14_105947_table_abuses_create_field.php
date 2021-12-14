<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableAbusesCreateField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('abuses', function (Blueprint $table) {
            $table->string('date_abuse', 255)->after('id');
            $table->unsignedBigInteger('from_user_id')->after('date_abuse');
            $table->unsignedBigInteger('to_user_id')->after('from_user_id');
            $table->unsignedBigInteger('company_admin_id')->after('to_user_id');
            $table->string('message_id', 255)->after('company_admin_id')->nullable();
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
            $table->dropColumn('date_abuse');
            $table->dropColumn('from_user_id');
            $table->dropColumn('to_user_id');
            $table->dropColumn('company_admin_id');
            $table->dropColumn('message_id');
        });
    }
}
