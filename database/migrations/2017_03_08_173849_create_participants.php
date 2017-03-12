<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParticipants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('db_constants.TABLES.TBL_PARTICIPANTS'), function (Blueprint $table) {
            $table->integer(config('db_constants.FIELDS.FLD_PARTICIPANTS_USER_ID'));
            $table->integer(config('db_constants.FIELDS.FLD_PARTICIPANTS_CONTEST_ID'));
            $table->primary(config('db_constants.FIELDS.FLD_PARTICIPANTS_USER_ID'),
                config('db_constants.FIELDS.FLD_PARTICIPANTS_CONTEST_ID'));
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
        Schema::dropIfExists(config('db_constants.TABLES.TBL_PARTICIPANTS'));
    }
}
