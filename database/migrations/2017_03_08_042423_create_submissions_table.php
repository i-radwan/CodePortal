<?php

use App\Utilities\Constants;
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
        Schema::create(Constants::TBL_SUBMISSIONS, function (Blueprint $table) {
            $table->increments(Constants::FLD_SUBMISSIONS_ID);
            $table->unsignedBigInteger(Constants::FLD_SUBMISSIONS_JUDGE_SUBMISSION_ID);
            $table->unsignedInteger(Constants::FLD_SUBMISSIONS_USER_ID);
            $table->unsignedInteger(Constants::FLD_SUBMISSIONS_PROBLEM_ID);
            $table->unsignedInteger(Constants::FLD_SUBMISSIONS_LANGUAGE_ID);
            $table->unsignedBigInteger(Constants::FLD_SUBMISSIONS_SUBMISSION_TIME);
            $table->unsignedInteger(Constants::FLD_SUBMISSIONS_EXECUTION_TIME);
            $table->unsignedBigInteger(Constants::FLD_SUBMISSIONS_CONSUMED_MEMORY);
            $table->enum(Constants::FLD_SUBMISSIONS_VERDICT, Constants::CODEFORCES_SUBMISSION_VERDICTS);
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
        Schema::dropIfExists(Constants::TBL_SUBMISSIONS);
    }
}
