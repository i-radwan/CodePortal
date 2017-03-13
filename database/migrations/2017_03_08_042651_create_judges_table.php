<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Utilities\Constants;

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
            $table->string(Constants::FLD_JUDGES_NAME, 100);
            $table->string(Constants::FLD_JUDGES_LINK, 100)->unique();
            $table->string(Constants::FLD_JUDGES_API_LINK);

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
        Schema::dropIfExists(Constants::TBL_JUDGES);
    }
}
