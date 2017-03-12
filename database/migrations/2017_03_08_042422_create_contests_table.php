<?php

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
        Schema::create(config('db_constants.TABLES.TBL_CONTESTS'), function (Blueprint $table) {
            $table->increments(config('db_constants.FIELDS.FLD_CONTESTS_ID'));
            $table->string(config('db_constants.FIELDS.FLD_CONTESTS_NAME'), 100);
            $table->dateTime(config('db_constants.FIELDS.FLD_CONTESTS_TIME'));
            $table->integer(config('db_constants.FIELDS.FLD_CONTESTS_DURATION'))->unsigned();
            $table->enum(config('db_constants.FIELDS.FLD_CONTESTS_VISIBILITY'), config('constants.CONTEST_VISIBILITY'));
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
        Schema::dropIfExists(config('db_constants.TABLES.TBL_CONTESTS'));
    }
}
