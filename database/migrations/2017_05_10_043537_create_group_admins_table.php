<?php

use App\Utilities\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_GROUP_ADMINS, function (Blueprint $table) {
            $table->unsignedInteger(Constants::FLD_GROUP_ADMINS_GROUP_ID);
            $table->unsignedInteger(Constants::FLD_GROUP_ADMINS_ADMIN_ID);
            $table->primary(array(
                    Constants::FLD_GROUP_ADMINS_GROUP_ID,
                    Constants::FLD_GROUP_ADMINS_ADMIN_ID
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
        Schema::dropIfExists(Constants::TBL_GROUP_ADMINS);
    }
}
