<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Utilities\Constants;

class CreatePasswordResetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_PASSWORD_RESETS, function (Blueprint $table) {
            $table->string(Constants::FLD_PASSWORD_RESETS_EMAIL, 100)->index();
            $table->string(Constants::FLD_PASSWORD_RESETS_TOKEN, 100)->index();
            $table->timestamp(Constants::FLD_PASSWORD_RESETS_CREATED_AT)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Constants::TBL_PASSWORD_RESETS);
    }
}
