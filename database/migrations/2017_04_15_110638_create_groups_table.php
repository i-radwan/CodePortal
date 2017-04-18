<?php

use App\Utilities\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_GROUPS, function (Blueprint $table) {
            $table->increments(Constants::FLD_GROUPS_ID);
            $table->unsignedInteger(Constants::FLD_GROUPS_OWNER_ID);
            $table->string(Constants::FLD_GROUPS_NAME);
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
        Schema::dropIfExists(Constants::TBL_GROUPS);
    }
}
