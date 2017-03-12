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
        Schema::create(config('db_constants.TABLES.TBL_USERS'), function (Blueprint $table) {
            $table->increments(config('db_constants.FIELDS.FLD_USERS_ID'));
            $table->string(config('db_constants.FIELDS.FLD_USERS_NAME'), 20);
            $table->string(config('db_constants.FIELDS.FLD_USERS_EMAIL'), 50)->unique();
            $table->string(config('db_constants.FIELDS.FLD_USERS_PASSWORD'));
            $table->string(config('db_constants.FIELDS.FLD_USERS_USERNAME'), 50)->unique();
            $table->enum(config('db_constants.FIELDS.FLD_USERS_GENDER'), config('constants.USER_GENDER'))->nullable();
            $table->integer(config('db_constants.FIELDS.FLD_USERS_AGE'))->nullable();
            $table->string(config('db_constants.FIELDS.FLD_USERS_PROFILE_PIC'))->nullable();
            $table->string(config('db_constants.FIELDS.FLD_USERS_COUNTRY'))->nullable();
            $table->enum(config('db_constants.FIELDS.FLD_USERS_ROLE'), config('constants.USER_ROLE'));
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
        Schema::dropIfExists(config('db_constants.TABLES.TBL_USERS'));
    }
}
