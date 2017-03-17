<?php

use App\Utilities\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContestAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_CONTEST_ADMINS, function (Blueprint $table) {
            $table->integer(Constants::FLD_CONTEST_ADMINS_CONTEST_ID);
            $table->integer(Constants::FLD_CONTEST_ADMINS_ADMIN_ID);
            $table->primary(array(
                    Constants::FLD_CONTEST_ADMINS_CONTEST_ID,
                    Constants::FLD_CONTEST_ADMINS_ADMIN_ID
                )
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Constants::TBL_CONTEST_ADMINS);
    }
}
