<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContestAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('db_constants.TABLES.TBL_CONTEST_ADMIN'), function (Blueprint $table) {
            $table->integer(config('db_constants.FIELDS.FLD_CONTEST_ADMIN_CONTEST_ID'));
            $table->integer(config('db_constants.FIELDS.FLD_CONTEST_ADMIN_ADMIN_ID'));
            $table->primary(array(config('db_constants.FIELDS.FLD_CONTEST_ADMIN_CONTEST_ID'),
                config('db_constants.FIELDS.FLD_CONTEST_ADMIN_ADMIN_ID')));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('db_constants.TABLES.TBL_CONTEST_ADMIN'));
    }
}
