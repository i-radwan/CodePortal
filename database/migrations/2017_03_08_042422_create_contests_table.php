<?php

use App\Utilities\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_CONTESTS, function (Blueprint $table) {
            $table->increments(Constants::FLD_CONTESTS_ID);
            $table->unsignedInteger(Constants::FLD_CONTESTS_OWNER_ID);
            $table->string(Constants::FLD_CONTESTS_NAME, 100);
            $table->dateTime(Constants::FLD_CONTESTS_TIME);
            $table->unsignedInteger(Constants::FLD_CONTESTS_DURATION);
            $table->enum(Constants::FLD_CONTESTS_VISIBILITY, Constants::CONTEST_VISIBILITIES);
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
        Schema::dropIfExists(Constants::TBL_CONTESTS);
    }
}
