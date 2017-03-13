<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Utilities\Constants;

class CreateContestAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_CONTEST_ADMIN, function (Blueprint $table) {
            $table->integer(Constants::FLD_CONTEST_ADMIN_CONTEST_ID);
            $table->integer(Constants::FLD_CONTEST_ADMIN_ADMIN_ID);
            $table->primary(array(Constants::FLD_CONTEST_ADMIN_CONTEST_ID,
                Constants::FLD_CONTEST_ADMIN_ADMIN_ID));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Constants::TBL_CONTEST_ADMIN);
    }
}
