<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserHandles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_handles', function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('provider_id');
            $table->string('handle', 50);
            $table->unique(array('user_id', 'provider_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_handles');
    }
}
