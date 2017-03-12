<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserHandles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('db_constants.TABLES.TBL_USER_HANDLES'), function (Blueprint $table) {
            $table->integer(config('db_constants.FIELDS.FLD_USER_HANDLES_USER_ID'));
            $table->integer(config('db_constants.FIELDS.FLD_USER_HANDLES_JUDGE_ID'));
            $table->string(config('db_constants.FIELDS.FLD_USER_HANDLES_HANDLE'), 50);
            $table->primary(array(config('db_constants.FIELDS.FLD_USER_HANDLES_USER_ID'),
                config('db_constants.FIELDS.FLD_USER_HANDLES_JUDGE_ID')));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('db_constants.TABLES.TBL_USER_HANDLES'));
    }
}
