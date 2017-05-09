<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \App\Utilities\Constants;

class CreateVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create(Constants::TBL_VOTES, function (Blueprint $table) {
            $table->increments(Constants::FLD_VOTES_ID);
            $table->unsignedInteger(Constants::FLD_VOTES_USER_ID);
            $table->unsignedInteger(Constants::FLD_VOTES_RESOURCE_ID);
            $table->string(Constants::FLD_VOTES_RESOURCE_TYPE);
            $table->enum(Constants::FLD_VOTES_TYPE, Constants::RESOURCE_VOTE_TYPES);
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
        Schema::dropIfExists(Constants::TBL_VOTES);
    }
}
