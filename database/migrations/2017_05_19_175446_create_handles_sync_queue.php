<?php

use App\Utilities\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHandlesSyncQueue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_HANDLES_SYNC_QUEUE, function (Blueprint $table) {
            $table->unsignedInteger(Constants::FLD_HANDLES_SYNC_QUEUE_USER_ID);
            $table->unsignedInteger(Constants::FLD_HANDLES_SYNC_QUEUE_JUDGE_ID);
            $table->primary(array(
                Constants::FLD_HANDLES_SYNC_QUEUE_USER_ID,
                Constants::FLD_HANDLES_SYNC_QUEUE_JUDGE_ID
            ));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Constants::TBL_HANDLES_SYNC_QUEUE);
    }
}
