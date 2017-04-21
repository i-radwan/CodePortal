<?php

use App\Utilities\Constants;
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
        Schema::create(Constants::TBL_USERS, function (Blueprint $table) {
            $table->increments(Constants::FLD_USERS_ID);
            $table->string(Constants::FLD_USERS_USERNAME, 20)->unique();
            $table->string(Constants::FLD_USERS_EMAIL, 50)->unique();
            $table->string(Constants::FLD_USERS_PASSWORD);
            $table->string(Constants::FLD_USERS_FIRST_NAME, 20)->nullable();
            $table->string(Constants::FLD_USERS_LAST_NAME, 20)->nullable();
            $table->enum(Constants::FLD_USERS_GENDER, Constants::USER_GENDERS)->nullable();
            $table->date(Constants::FLD_USERS_BIRTHDATE)->nullable();
            $table->string(Constants::FLD_USERS_COUNTRY)->nullable();
            $table->string(Constants::FLD_USERS_PROFILE_PICTURE)->nullable();
            $table->enum(Constants::FLD_USERS_ROLE, Constants::ACCOUNT_ROLES)->default(Constants::ACCOUNT_ROLE_USER);
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
