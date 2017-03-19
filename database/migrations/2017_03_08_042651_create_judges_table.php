<?php

use App\Utilities\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJudgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_JUDGES, function (Blueprint $table) {
            $table->increments(Constants::FLD_JUDGES_ID);
            $table->string(Constants::FLD_JUDGES_NAME, 100)->unique();
            $table->string(Constants::FLD_JUDGES_LINK, 100)->unique();
//            $table->primary(Constants::FLD_JUDGES_ID);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Constants::TBL_JUDGES);
    }
}
