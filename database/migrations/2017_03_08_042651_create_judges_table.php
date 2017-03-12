<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJudgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('db_constants.TABLES.TBL_JUDGES'), function (Blueprint $table) {
            $table->increments(config('db_constants.FIELDS.FLD_JUDGES_ID'));
            $table->string(config('db_constants.FIELDS.FLD_JUDGES_NAME'), 100);
            $table->string(config('db_constants.FIELDS.FLD_JUDGES_LINK'), 100)->unique();
            $table->string(config('db_constants.FIELDS.FLD_JUDGES_API_LINK'));

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
        Schema::dropIfExists(config('db_constants.TABLES.TBL_JUDGES'));
    }
}
