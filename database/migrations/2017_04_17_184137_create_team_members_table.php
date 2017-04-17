<?php

use App\Utilities\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_TEAM_MEMBERS, function (Blueprint $table) {
            $table->unsignedInteger(Constants::FLD_TEAM_MEMBERS_TEAM_ID);
            $table->unsignedInteger(Constants::FLD_TEAM_MEMBERS_USER_ID);
            $table->primary(array(
                    Constants::FLD_TEAM_MEMBERS_TEAM_ID,
                    Constants::FLD_TEAM_MEMBERS_USER_ID
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
        Schema::dropIfExists(Constants::TBL_TEAM_MEMBERS);
    }
}
