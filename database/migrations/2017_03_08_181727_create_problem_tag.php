<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProblemTag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('db_constants.TABLES.TBL_PROBLEM_TAG'), function (Blueprint $table) {
            $table->integer(config('db_constants.FIELDS.FLD_PROBLEM_TAG_PROBLEM_ID'));
            $table->integer(config('db_constants.FIELDS.FLD_PROBLEM_TAG_TAG_ID'));
            $table->primary(array(config('db_constants.FIELDS.FLD_PROBLEM_TAG_PROBLEM_ID'), config('db_constants.FIELDS.FLD_PROBLEM_TAG_TAG_ID')));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('db_constants.TABLES.TBL_PROBLEM_TAG'));
    }
}
