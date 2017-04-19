<?php

use App\Utilities\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_SHEETS, function (Blueprint $table) {
            $table->increments(Constants::FLD_SHEETS_ID);
            $table->unsignedInteger(Constants::FLD_SHEETS_GROUP_ID);
            $table->string(Constants::FLD_SHEETS_NAME);
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
        Schema::dropIfExists(Constants::TBL_SHEETS);
    }
}
