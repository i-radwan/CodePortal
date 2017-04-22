<?php

use App\Models\User;
use App\Models\Contest;
use App\Models\Group;
use App\Models\Team;
use App\Utilities\Constants;
use Illuminate\Database\Seeder;

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

        $limit = 5;

        // Get all user IDs
        $userIDs = User::pluck(Constants::FLD_USERS_ID)->toArray();

        // Seed contest notifications
        foreach (Contest::all() as $contest) {
            $contestOrganizersIDs = $contest->organizers()->pluck(Constants::FLD_USERS_ID)->toArray();
            $contestOrganizersIDs[] = $contest[Constants::FLD_CONTESTS_OWNER_ID];
            $receiverIDs = array_diff($userIDs, $contestOrganizersIDs);

            $faker->unique(true);   // Reset faker unique function

            // Insert contest invitations
            $n = $faker->numberBetween(0, $limit);
            for ($i = 0; $i < $n; ++$i) {
                DB::table(Constants::TBL_NOTIFICATIONS)->insert([
                    Constants::FLD_NOTIFICATIONS_SENDER_ID => $faker->randomElement($contestOrganizersIDs),
                    Constants::FLD_NOTIFICATIONS_RECEIVER_ID => $faker->unique()->randomElement($receiverIDs),
                    Constants::FLD_NOTIFICATIONS_RESOURCE_ID => $contest[Constants::FLD_CONTESTS_ID],
                    Constants::FLD_NOTIFICATIONS_STATUS => $faker->randomElement(Constants::NOTIFICATION_STATUS),
                    Constants::FLD_NOTIFICATIONS_TYPE => Constants::NOTIFICATION_TYPE_CONTEST
                ]);
            }
        }

        // Seed group notifications
        foreach (Group::all() as $group) {
            // TODO: add group admins when added
            $groupOrganizersIDs[] = $group[Constants::FLD_GROUPS_OWNER_ID];
            $receiverIDs = array_diff($userIDs, $groupOrganizersIDs);

            $faker->unique(true);   // Reset faker unique function

            // Insert group invitations
            $n = $faker->numberBetween(0, $limit);
            for ($i = 0; $i < $n; ++$i) {
                DB::table(Constants::TBL_NOTIFICATIONS)->insert([
                    Constants::FLD_NOTIFICATIONS_SENDER_ID => $faker->randomElement($groupOrganizersIDs),
                    Constants::FLD_NOTIFICATIONS_RECEIVER_ID => $faker->unique()->randomElement($receiverIDs),
                    Constants::FLD_NOTIFICATIONS_RESOURCE_ID => $group[Constants::FLD_GROUPS_ID],
                    Constants::FLD_NOTIFICATIONS_STATUS => $faker->randomElement(Constants::NOTIFICATION_STATUS),
                    Constants::FLD_NOTIFICATIONS_TYPE => Constants::NOTIFICATION_TYPE_GROUP
                ]);
            }
        }

        // Seed contest notifications
        foreach (Team::all() as $team) {
            $teamMemberIDs = $team->members()->pluck(Constants::FLD_USERS_ID)->toArray();
            $receiverIDs = array_diff($userIDs, $teamMemberIDs);

            $faker->unique(true);   // Reset faker unique function

            // Insert team invitations
            $n = $faker->numberBetween(0, $limit);
            for ($i = 0; $i < $n; ++$i) {
                DB::table(Constants::TBL_NOTIFICATIONS)->insert([
                    Constants::FLD_NOTIFICATIONS_SENDER_ID => $faker->randomElement($teamMemberIDs),
                    Constants::FLD_NOTIFICATIONS_RECEIVER_ID => $faker->unique()->randomElement($receiverIDs),
                    Constants::FLD_NOTIFICATIONS_RESOURCE_ID => $team[Constants::FLD_TEAMS_ID],
                    Constants::FLD_NOTIFICATIONS_STATUS => $faker->randomElement(Constants::NOTIFICATION_STATUS),
                    Constants::FLD_NOTIFICATIONS_TYPE => Constants::NOTIFICATION_TYPE_TEAM
                ]);
            }
        }
    }
}
