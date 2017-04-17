<?php

use Illuminate\Database\Seeder;
use App\Utilities\Constants;
use App\Models\Contest;
use App\Models\User;
use App\Models\Group;

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

        $limit = 500;

        $userIDs = User::all()->pluck(Constants::FLD_USERS_ID)->toArray();

        // Seed contest notifications
        $ContestIDs = Contest::all()->pluck(Constants::FLD_CONTESTS_ID)->toArray();
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
                Constants::FLD_NOTIFICATIONS_TYPE => (Constants::NOTIFICATION_TYPE[Constants::NOTIFICATION_TYPE_CONTEST])
            ]);
        }

        // Seed group notifications
        $groupIDs = Group::all()->pluck(Constants::FLD_GROUPS_ID)->toArray();
        for ($i = 0; $i < $limit; $i++) {

            // Get different sender ID and receiver ID
            $senderID = $faker->randomElement($userIDs);
            do {
                $receiverID = $faker->randomElement($userIDs);
            } while ($senderID == $receiverID);
            try {
                DB::table(Constants::TBL_NOTIFICATIONS)->insert([
                    Constants::FLD_NOTIFICATIONS_SENDER_ID => $senderID,
                    Constants::FLD_NOTIFICATIONS_RECEIVER_ID => $receiverID,
                    Constants::FLD_NOTIFICATIONS_RESOURCE_ID => $faker->randomElement($groupIDs),
                    Constants::FLD_NOTIFICATIONS_STATUS => $faker->randomElement(Constants::NOTIFICATION_STATUS),
                    Constants::FLD_NOTIFICATIONS_TYPE => (Constants::NOTIFICATION_TYPE[Constants::NOTIFICATION_TYPE_GROUP])
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
            }
        }
    }
}
