<?php

use App\Utilities\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupJoinRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_GROUP_JOIN_REQUESTS, function (Blueprint $table) {
            $table->unsignedInteger(Constants::FLD_GROUPS_JOIN_REQUESTS_GROUP_ID);
            $table->unsignedInteger(Constants::FLD_GROUPS_JOIN_REQUESTS_USER_ID);
            $table->primary(array(
                Constants::FLD_GROUPS_JOIN_REQUESTS_GROUP_ID,
                Constants::FLD_GROUPS_JOIN_REQUESTS_USER_ID
            ));
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
        Schema::dropIfExists(Constants::TBL_GROUP_JOIN_REQUESTS);
    }
}
