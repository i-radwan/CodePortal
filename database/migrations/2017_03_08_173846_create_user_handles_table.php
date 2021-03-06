<?php

use App\Utilities\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserHandlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_USER_HANDLES, function (Blueprint $table) {
            $table->unsignedInteger(Constants::FLD_USER_HANDLES_USER_ID);
            $table->unsignedInteger(Constants::FLD_USER_HANDLES_JUDGE_ID);
            $table->string(Constants::FLD_USER_HANDLES_HANDLE, 50);
            $table->primary(array(
                    Constants::FLD_USER_HANDLES_USER_ID,
                    Constants::FLD_USER_HANDLES_JUDGE_ID
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
        Schema::dropIfExists(Constants::TBL_USER_HANDLES);
    }
}
