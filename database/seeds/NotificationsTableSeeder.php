<?php

use Illuminate\Database\Seeder;
use App\Utilities\Constants;
use App\Models\Contest;
use App\Models\User;

class NotificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete previous records
        DB::table(Constants::TBL_NOTIFICATIONS)->delete();

        $faker = Faker\Factory::create();

        $limit = 300;

        // ToDo generalize to groups, teams
        $ContestIDs = Contest::all()->pluck('id')->toArray();
        $userIDs = User::all()->pluck('id')->toArray();

        for ($i = 0; $i < $limit; $i++) {

            // Get different sender ID and receiver ID
            $senderID = $faker->randomElement($userIDs);
            do {
                $receiverID = $faker->randomElement($userIDs);
            } while ($senderID == $receiverID);


            DB::table(Constants::TBL_NOTIFICATIONS)->insert([
                Constants::FLD_NOTIFICATIONS_SENDER_ID => $senderID,
                Constants::FLD_NOTIFICATIONS_RECEIVER_ID => $receiverID,
                Constants::FLD_NOTIFICATIONS_RESOURCE_ID => $faker->randomElement($ContestIDs),
                Constants::FLD_NOTIFICATIONS_STATUS => $faker->randomElement(Constants::NOTIFICATION_STATUS),
                Constants::FLD_NOTIFICATIONS_TYPE => $faker->randomElement(Constants::NOTIFICATION_TYPE),
            ]);
        }
    }
}
