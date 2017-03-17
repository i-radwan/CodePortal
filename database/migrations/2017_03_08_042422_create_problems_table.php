<?php

use App\Utilities\Constants;
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
        Schema::create(Constants::TBL_PROBLEMS, function (Blueprint $table) {
            $table->increments(Constants::FLD_PROBLEMS_ID);
            $table->integer(Constants::FLD_PROBLEMS_JUDGE_ID);
            $table->integer(Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY);
            $table->string(Constants::FLD_PROBLEMS_JUDGE_SECOND_KEY, 10);
            $table->string(Constants::FLD_PROBLEMS_NAME, 100);
            $table->integer(Constants::FLD_PROBLEMS_DIFFICULTY);
            $table->integer(Constants::FLD_PROBLEMS_ACCEPTED_SUBMISSIONS_COUNT);
            $table->timestamps();
            $table->unique(array(
                Constants::FLD_PROBLEMS_JUDGE_ID,
                Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY,
                Constants::FLD_PROBLEMS_JUDGE_SECOND_KEY
            ));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Constants::TBL_PROBLEMS);
    }
}
