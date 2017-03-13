<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Utilities\Constants;

class CreateParticipants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_PARTICIPANTS, function (Blueprint $table) {
            $table->integer(Constants::FLD_PARTICIPANTS_USER_ID);
            $table->integer(Constants::FLD_PARTICIPANTS_CONTEST_ID);
            $table->primary(Constants::FLD_PARTICIPANTS_USER_ID,
                Constants::FLD_PARTICIPANTS_CONTEST_ID);
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
        Schema::dropIfExists(Constants::TBL_PARTICIPANTS);
    }
}
