<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBuddyRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buddy_request', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shoogle_id');
            $table->unsignedBigInteger('user1_id');
            $table->unsignedBigInteger('user2_id');
            $table->enum('type', ['invite', 'confirm', 'reject', 'disconnect'])->default('invite');
            $table->text('message')->nullable();
            $table->timestamps();

            $table->foreign('shoogle_id')->references('id')->on('shoogles');
            $table->foreign('user1_id')->references('id')->on('users');
            $table->foreign('user2_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('buddy_request');
    }
}
