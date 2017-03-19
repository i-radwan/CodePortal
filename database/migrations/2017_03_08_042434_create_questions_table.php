<?php

use App\Utilities\Constants;
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
        Schema::create(Constants::TBL_QUESTIONS, function (Blueprint $table) {
            $table->increments(Constants::FLD_QUESTIONS_ID);
            $table->string(Constants::FLD_QUESTIONS_TITLE);
            $table->longText(Constants::FLD_QUESTIONS_CONTENT);
            $table->longText(Constants::FLD_QUESTIONS_ANSWER)->nullable();
            $table->enum(Constants::FLD_QUESTIONS_STATUS, Constants::QUESTION_STATUS);
            $table->unsignedInteger(Constants::FLD_QUESTIONS_ADMIN_ID)->nullable();
            $table->unsignedInteger(Constants::FLD_QUESTIONS_CONTEST_ID);
            $table->unsignedInteger(Constants::FLD_QUESTIONS_USER_ID);
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
        Schema::dropIfExists(Constants::TBL_QUESTIONS);
    }
}
