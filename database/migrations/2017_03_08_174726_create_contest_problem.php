<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Utilities\Constants;

class CreateContestProblem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_CONTEST_PROBLEM, function (Blueprint $table) {
            $table->integer(Constants::FLD_CONTEST_PROBLEM_CONTEST_ID);
            $table->integer(Constants::FLD_CONTEST_PROBLEM_PROBLEM_ID);
            $table->primary(array(Constants::FLD_CONTEST_PROBLEM_CONTEST_ID,
                Constants::FLD_CONTEST_PROBLEM_PROBLEM_ID));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Constants::TBL_CONTEST_PROBLEM);
    }
}
