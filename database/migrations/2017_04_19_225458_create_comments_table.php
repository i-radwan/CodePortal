<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //const FLD_COMMENTS_COMMENT_ID = "id";
//        const FLD_COMMENTS_USER_ID = "user_id";
//        const FLD_COMMENTS_POST_ID = "post_id";
//        const FLD_COMMENTS_BODY = "body";
//        const FLD_COMMENTS_UP_VOTES = "upvote";
//        const FLD_COMMENTS_DOWN_VOTES = "downvote";
//        Schema::create(Constants::TBL_POSTS, function (Blueprint $table) {
//            $table->increments(Constants::FLD_POSTS_POST_ID);
//            $table->unsignedInteger(Constants::FLD_POSTS_BLOG_ID);
//            $table->text(Constants::FLD_POSTS_BODY);
//            $table->timestamps();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
