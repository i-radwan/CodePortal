<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Utilities\Constants;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_USERS, function (Blueprint $table) {
            $table->increments(Constants::FLD_USERS_ID);
            $table->string(Constants::FLD_USERS_NAME, 20);
            $table->string(Constants::FLD_USERS_EMAIL, 50)->unique();
            $table->string(Constants::FLD_USERS_PASSWORD);
            $table->string(Constants::FLD_USERS_USERNAME, 50)->unique();
            $table->enum(Constants::FLD_USERS_GENDER, Constants::USER_GENDER)->nullable();
            $table->integer(Constants::FLD_USERS_AGE)->nullable();
            $table->string(Constants::FLD_USERS_PROFILE_PIC)->nullable();
            $table->string(Constants::FLD_USERS_COUNTRY)->nullable();
            $table->enum(Constants::FLD_USERS_ROLE, Constants::USER_ROLE);
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
        Schema::dropIfExists(Constants::TBL_USERS);
    }
}
