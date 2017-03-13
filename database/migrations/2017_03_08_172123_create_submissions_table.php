<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Utilities\Constants;
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
            $table->integer(Constants::FLD_SUBMISSIONS_PROBLEM_ID);
            $table->integer(Constants::FLD_SUBMISSIONS_USER_ID);
            $table->string(Constants::FLD_SUBMISSIONS_SUBMISSION_ID);
            $table->integer(Constants::FLD_SUBMISSIONS_LANGUAGE_ID);
            $table->integer(Constants::FLD_SUBMISSIONS_EXECUTION_TIME);
            $table->integer(Constants::FLD_SUBMISSIONS_CONSUMED_MEMORY);
            $table->enum(Constants::FLD_SUBMISSIONS_VERDICT, Constants::SUBMISSION_VERDICT);
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
