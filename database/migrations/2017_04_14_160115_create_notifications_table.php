<?php

use App\Utilities\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Constants::TBL_NOTIFICATIONS, function (Blueprint $table) {
            $table->increments(Constants::FLD_NOTIFICATIONS_ID);
            $table->unsignedInteger(Constants::FLD_NOTIFICATIONS_SENDER_ID);
            $table->unsignedInteger(Constants::FLD_NOTIFICATIONS_RECEIVER_ID);
            $table->unsignedInteger(Constants::FLD_NOTIFICATIONS_RESOURCE_ID);
            $table->enum(Constants::FLD_NOTIFICATIONS_STATUS, Constants::NOTIFICATION_STATUS)->default(Constants::NOTIFICATION_STATUS_UNREAD);
            $table->enum(Constants::FLD_NOTIFICATIONS_TYPE, Constants::NOTIFICATION_TYPES);
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
        Schema::dropIfExists(Constants::TBL_NOTIFICATIONS);
    }
}
