<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20);
            $table->string('email', 50)->unique();
            $table->string('password');
            $table->string('handle', 50)->unique();
            $table->enum('gender', config('constants.USER_GENDER'));
            $table->integer('age');
            $table->string('profile_pic');
            $table->string('country');
            $table->enum('role', config('constants.USER_ROLE'));
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
