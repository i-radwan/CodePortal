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
        //
        Schema::create(Constants::TBL_POSTS, function (Blueprint $table) {
            $table->increments(Constants::FLD_POSTS_POST_ID);
            $table->unsignedInteger(Constants::FLD_POSTS_BLOG_ID);
            $table->text(Constants::FLD_POSTS_BODY);
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
        Schema::dropIfExists(Constants::TBL_POSTS);
    }
}
