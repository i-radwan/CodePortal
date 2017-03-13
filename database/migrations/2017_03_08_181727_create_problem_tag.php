<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Utilities\Constants;

class CreateProblemTag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_PROBLEM_TAG, function (Blueprint $table) {
            $table->integer(Constants::FLD_PROBLEM_TAG_PROBLEM_ID);
            $table->integer(Constants::FLD_PROBLEM_TAG_TAG_ID);
            $table->primary(array(Constants::FLD_PROBLEM_TAG_PROBLEM_ID, Constants::FLD_PROBLEM_TAG_TAG_ID));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Constants::TBL_PROBLEM_TAG);
    }
}
