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
        //
        Schema::create(\App\Utilities\Constants::TBL_COMMENTS, function (Blueprint $table) {
            $table->increments(\App\Utilities\Constants::FLD_COMMENTS_ID);
            $table->unsignedInteger(\App\Utilities\Constants::FLD_COMMENTS_POST_ID);
            $table->unsignedInteger(\App\Utilities\Constants::FLD_COMMENTS_USER_ID);
            $table->unsignedInteger(\App\Utilities\Constants::FLD_COMMENTS_PARENT_ID)->nullable(); //ToDo: Samir Change that to normalized table
//            $table->unsignedInteger(\App\Utilities\Constants::FLD_COMMENTS_PARENT_ID)->default(0);
            $table->text(\App\Utilities\Constants::FLD_COMMENTS_BODY);
            $table->integer(\App\Utilities\Constants::FLD_COMMENTS_UP_VOTES)->default(0);
            $table->integer(\App\Utilities\Constants::FLD_COMMENTS_DOWN_VOTES)->default(0);
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
        Schema::dropIfExists(\App\Utilities\Constants::TBL_COMMENTS);

    }
}
