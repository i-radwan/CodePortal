<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Utilities\Constants;

class CreateGroupMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_GROUP_MEMBERS, function (Blueprint $table) {
            $table->unsignedInteger(Constants::FLD_GROUP_MEMBERS_GROUP_ID);
            $table->unsignedInteger(Constants::FLD_GROUP_MEMBERS_USER_ID);
            $table->primary(array(
                    Constants::FLD_GROUP_MEMBERS_GROUP_ID,
                    Constants::FLD_GROUP_MEMBERS_USER_ID
                )
            );
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
        Schema::dropIfExists(Constants::TBL_GROUP_MEMBERS);
    }
}
