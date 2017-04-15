<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Utilities\Constants;

class CreateGroupsJoinRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_GROUPS_JOIN_REQUESTS, function (Blueprint $table) {
            $table->unsignedInteger(Constants::FLD_GROUPS_JOIN_REQUESTS_GROUP_ID);
            $table->unsignedInteger(Constants::FLD_GROUPS_JOIN_REQUESTS_USER_ID);
            $table->timestamps();

            $table->primary(array(
                    Constants::FLD_GROUPS_JOIN_REQUESTS_GROUP_ID,
                    Constants::FLD_GROUPS_JOIN_REQUESTS_USER_ID
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
        Schema::dropIfExists(Constants::TBL_GROUPS_JOIN_REQUESTS);
    }
}
