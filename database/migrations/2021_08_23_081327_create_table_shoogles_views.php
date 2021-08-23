<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableShooglesViews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shoogles_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shoogle_id');
            $table->unsignedBigInteger('user_id');
            $table->dateTime('last_view');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('shoogle_id')->references('id')->on('shoogles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shoogles_views');
    }
}
