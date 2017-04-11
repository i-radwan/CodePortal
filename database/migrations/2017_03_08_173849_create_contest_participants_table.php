<?php

use App\Utilities\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContestParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_CONTEST_PARTICIPANTS, function (Blueprint $table) {
            $table->unsignedInteger(Constants::FLD_CONTEST_PARTICIPANTS_USER_ID);
            $table->unsignedInteger(Constants::FLD_CONTEST_PARTICIPANTS_CONTEST_ID);
            $table->timestamps();
            $table->primary(array(
                Constants::FLD_CONTEST_PARTICIPANTS_USER_ID,
                Constants::FLD_CONTEST_PARTICIPANTS_CONTEST_ID
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
        Schema::dropIfExists(Constants::TBL_CONTEST_PARTICIPANTS);
    }
}
