<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Utilities\Constants;

class CreateSheetProblemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_SHEETS_PROBLEMS, function (Blueprint $table) {
            $table->unsignedInteger(Constants::FLD_SHEETS_PROBLEMS_SHEET_ID);
            $table->unsignedInteger(Constants::FLD_SHEETS_PROBLEMS_PROBLEM_ID);
            $table->string(Constants::FLD_SHEETS_PROBLEMS_SOLUTION);
            $table->timestamps();

            $table->primary(array(
                Constants::FLD_SHEETS_PROBLEMS_SHEET_ID,
                Constants::FLD_SHEETS_PROBLEMS_PROBLEM_ID
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
        Schema::dropIfExists(Constants::TBL_SHEETS_PROBLEMS);
    }
}
