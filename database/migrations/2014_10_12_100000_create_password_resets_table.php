<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasswordResetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('db_constants.TABLES.TBL_PASSWORD_RESETS'), function (Blueprint $table) {
            $table->string(config('db_constants.FIELDS.FLD_PASSWORD_RESETS_EMAIL'), 100)->index();
            $table->string(config('db_constants.FIELDS.FLD_PASSWORD_RESETS_TOKEN'), 100)->index();
            $table->timestamp(config('db_constants.FIELDS.FLD_PASSWORD_RESETS_CREATED_AT'))->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('db_constants.TABLES.TBL_PASSWORD_RESETS'));
    }
}
