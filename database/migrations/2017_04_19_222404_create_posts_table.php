<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Add POSTS table
        Schema::create(\App\Utilities\Constants::TBL_POSTS, function (Blueprint $table) {
            $table->increments(\App\Utilities\Constants::FLD_POSTS_ID);
            $table->unsignedInteger(\App\Utilities\Constants::FLD_POSTS_OWNER_ID);
            $table->char(\App\Utilities\Constants::FLD_POSTS_TITLE, 50);
            $table->text(\App\Utilities\Constants::FLD_POSTS_BODY);
            $table->integer(\App\Utilities\Constants::FLD_POSTS_UP_VOTES)->default(0);
            $table->integer(\App\Utilities\Constants::FLD_POSTS_DOWN_VOTES)->default(0);
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
        //Remove posts Table
        Schema::dropIfExists(\App\Utilities\Constants::TBL_POSTS);
    }
}
