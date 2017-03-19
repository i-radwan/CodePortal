<?php

use App\Utilities\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProblemTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_PROBLEM_TAGS, function (Blueprint $table) {
            $table->unsignedInteger(Constants::FLD_PROBLEM_TAGS_PROBLEM_ID);
            $table->unsignedInteger(Constants::FLD_PROBLEM_TAGS_TAG_ID);
            $table->primary(array(
                    Constants::FLD_PROBLEM_TAGS_PROBLEM_ID,
                    Constants::FLD_PROBLEM_TAGS_TAG_ID
                )
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Constants::TBL_PROBLEM_TAGS);
    }
}
