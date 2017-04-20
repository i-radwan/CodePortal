<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create(\App\Utilities\Constants::TBL_COMMENTS_REPLIES, function (Blueprint $table) {
            $table->unsignedInteger(\App\Utilities\Constants::FLD_COMMENTS_REPLIES_COMMENT_ID);
            $table->unsignedInteger(\App\Utilities\Constants::FLD_COMMENTS_REPLIES_REPLY_ID);
            $table->primary([\App\Utilities\Constants::FLD_COMMENTS_REPLIES_COMMENT_ID, \App\Utilities\Constants::FLD_COMMENTS_REPLIES_REPLY_ID]);
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
        Schema::dropIfExists(\App\Utilities\Constants::TBL_COMMENTS_REPLIES);
    }
}
