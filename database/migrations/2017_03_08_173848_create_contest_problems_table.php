<?php

use App\Utilities\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContestProblemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_CONTEST_PROBLEMS, function (Blueprint $table) {
            $table->integer(Constants::FLD_CONTEST_PROBLEMS_CONTEST_ID);
            $table->integer(Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ID);
            $table->primary(array(
                    Constants::FLD_CONTEST_PROBLEMS_CONTEST_ID,
                    Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ID
                )
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Constants::TBL_CONTEST_PROBLEMS);
    }
}