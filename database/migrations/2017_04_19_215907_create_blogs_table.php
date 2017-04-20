<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_BLOGS, function (Blueprint $table) {
            $table->increments(Constants::FLD_BLOGS_BLOG_ID);
            $table->unsignedInteger(Constants::FLD_BLOGS_OWNER_ID);
            $table->timestamps();
            $table->unique(Constants::FLD_BLOGS_OWNER_ID); //ToDo: check Later
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
        Schema::dropIfExists(Constants::TBL_BLOGS);
    }
}
