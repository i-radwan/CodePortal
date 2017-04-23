<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUpVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create(\App\Utilities\Constants::TBL_UP_VOTES, function (Blueprint $table) {
            $table->increments(\App\Utilities\Constants::FLD_UP_VOTES_ID);
            $table->unsignedInteger(\App\Utilities\Constants::FLD_UP_VOTES_USER_ID);
            $table->unsignedInteger(\App\Utilities\Constants::FLD_UP_VOTES_VOTED_ID);
            $table->string(\App\Utilities\Constants::FLD_UP_VOTES_VOTED_TYPE);
            $table->softDeletes();
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
        //
        Schema::dropIfExists(\App\Utilities\Constants::TBL_UP_VOTES);
    }
}
