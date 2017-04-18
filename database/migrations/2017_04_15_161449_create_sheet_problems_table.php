<?php

use App\Utilities\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSheetProblemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_SHEET_PROBLEMS, function (Blueprint $table) {
            $table->unsignedInteger(Constants::FLD_SHEET_PROBLEMS_SHEET_ID);
            $table->unsignedInteger(Constants::FLD_SHEET_PROBLEMS_PROBLEM_ID);
            $table->string(Constants::FLD_SHEET_PROBLEMS_SOLUTION);
            $table->string(Constants::FLD_SHEET_PROBLEMS_SOLUTION_LANG)->default("c_cpp"); //TODO: remove or add constant
            $table->timestamps();
            $table->primary(array(
                Constants::FLD_SHEET_PROBLEMS_SHEET_ID,
                Constants::FLD_SHEET_PROBLEMS_PROBLEM_ID
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
        Schema::dropIfExists(Constants::TBL_SHEET_PROBLEMS);
    }
}
