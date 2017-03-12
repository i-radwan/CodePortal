<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('db_constants.TABLES.TBL_SUBMISSIONS'), function (Blueprint $table) {
            $table->increments(config('db_constants.FIELDS.FLD_SUBMISSIONS_ID'));
            $table->integer(config('db_constants.FIELDS.FLD_SUBMISSIONS_PROBLEM_ID'));
            $table->integer(config('db_constants.FIELDS.FLD_SUBMISSIONS_USER_ID'));
            $table->string(config('db_constants.FIELDS.FLD_SUBMISSIONS_SUBMISSION_ID'));
            $table->integer(config('db_constants.FIELDS.FLD_SUBMISSIONS_LANGUAGE_ID'));
            $table->integer(config('db_constants.FIELDS.FLD_SUBMISSIONS_EXECUTION_TIME'));
            $table->integer(config('db_constants.FIELDS.FLD_SUBMISSIONS_CONSUMED_MEMORY'));
            $table->enum(config('db_constants.FIELDS.FLD_SUBMISSIONS_VERDICT'), config('constants.SUBMISSION_VERDICT'));
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
        Schema::dropIfExists(config('db_constants.TABLES.TBL_SUBMISSIONS'));
    }
}
