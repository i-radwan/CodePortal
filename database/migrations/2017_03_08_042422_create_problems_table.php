<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Utilities\Constants;

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
            $table->string(Constants::FLD_PROBLEMS_NAME, 100);
            $table->integer(Constants::FLD_PROBLEMS_DIFFICULTY);
            $table->integer(Constants::FLD_PROBLEMS_ACCEPTED_SUBMISSIONS_COUNT);
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
        Schema::dropIfExists(Constants::TBL_PROBLEMS);
    }
}
