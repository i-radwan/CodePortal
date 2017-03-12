<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('db_constants.TABLES.TBL_QUESTIONS'), function (Blueprint $table) {
            $table->increments(config('db_constants.FIELDS.FLD_QUESTIONS_ID'));
            $table->string(config('db_constants.FIELDS.FLD_QUESTIONS_TITLE'));
            $table->longText(config('db_constants.FIELDS.FLD_QUESTIONS_CONTENT'));
            $table->longText(config('db_constants.FIELDS.FLD_QUESTIONS_ANSWER'))->nullable();
            $table->enum(config('db_constants.FIELDS.FLD_QUESTIONS_STATUS'), config('constants.QUESTION_STATUS'));
            $table->integer(config('db_constants.FIELDS.FLD_QUESTIONS_ADMIN_ID'))->nullable();
            $table->integer(config('db_constants.FIELDS.FLD_QUESTIONS_CONTEST_ID'));
            $table->integer(config('db_constants.FIELDS.FLD_QUESTIONS_USER_ID'));
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
        Schema::dropIfExists(config('db_constants.TABLES.TBL_QUESTIONS'));
    }
}
