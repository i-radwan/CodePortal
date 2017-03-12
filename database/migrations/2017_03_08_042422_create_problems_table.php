<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProblemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('db_constants.TABLES.TBL_PROBLEMS'), function (Blueprint $table) {
            $table->increments(config('db_constants.FIELDS.FLD_PROBLEMS_ID'));
            $table->integer(config('db_constants.FIELDS.FLD_PROBLEMS_JUDGE_ID'));
            $table->string(config('db_constants.FIELDS.FLD_PROBLEMS_NAME'), 100);
            $table->integer(config('db_constants.FIELDS.FLD_PROBLEMS_DIFFICULTY'));
            $table->integer(config('db_constants.FIELDS.FLD_PROBLEMS_ACCEPTED_SUBMISSIONS_COUNT'));
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
        Schema::dropIfExists(config('db_constants.TABLES.TBL_PROBLEMS'));
    }
}
