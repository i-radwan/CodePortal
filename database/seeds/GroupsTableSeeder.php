<?php

use App\Models\User;
use App\Models\Contest;
use App\Models\Group;
use App\Utilities\Constants;
use Illuminate\Database\Seeder;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete previous records
        DB::table(Constants::TBL_GROUPS)->delete();
        DB::table(Constants::TBL_GROUP_CONTESTS)->delete();
        DB::table(Constants::TBL_GROUP_JOIN_REQUESTS)->delete();
        DB::table(Constants::TBL_GROUP_MEMBERS)->delete();

        $faker = Faker\Factory::create();

        $limit = 200;

        // Get all user IDs
        $userIDs = User::pluck(Constants::FLD_USERS_ID)->toArray();
        // Get all contest IDs
        $contestIDs = Contest::pluck(Constants::FLD_CONTESTS_ID)->toArray();

        // Insert groups
        for ($i = 0; $i < $limit; $i++) {
            DB::table(Constants::TBL_GROUPS)->insert([
                Constants::FLD_GROUPS_OWNER_ID => $faker->randomElement($userIDs),
                Constants::FLD_GROUPS_NAME => $faker->sentence,
            ]);
        }

        // Seed group data
        foreach (Group::all() as $group) {
            $receiverIDs = array_diff($userIDs, [$group[Constants::FLD_GROUPS_OWNER_ID]]);

            $faker->unique(true);   // Reset faker unique function

            // TODO: insert group admins when added

            // Insert group members
            $n = $faker->numberBetween(0, 15);
            for ($i = 0; $i < $n; ++$i) {
                DB::table(Constants::TBL_GROUP_MEMBERS)->insert([
                    Constants::FLD_GROUP_MEMBERS_GROUP_ID => $group[Constants::FLD_GROUPS_ID],
                    Constants::FLD_GROUP_MEMBERS_USER_ID => $faker->unique()->randomElement($receiverIDs)
                ]);
            }

            // Insert group join requests
            $n = $faker->numberBetween(0, 15);
            for ($i = 0; $i < $n; ++$i) {
                DB::table(Constants::TBL_GROUP_JOIN_REQUESTS)->insert([
                    Constants::FLD_GROUPS_JOIN_REQUESTS_GROUP_ID => $group[Constants::FLD_GROUPS_ID],
                    Constants::FLD_GROUPS_JOIN_REQUESTS_USER_ID => $faker->unique()->randomElement($receiverIDs)
                ]);
            }

            $faker->unique(true);   // Reset faker unique function

            // Insert group contests
            $n = $faker->numberBetween(0, 4);
            for ($i = 0; $i < $n; ++$i) {
                DB::table(Constants::TBL_GROUP_CONTESTS)->insert([
                    Constants::FLD_GROUP_CONTESTS_GROUP_ID => $group[Constants::FLD_GROUPS_ID],
                    Constants::FLD_GROUP_CONTESTS_CONTEST_ID => $faker->unique()->randomElement($contestIDs)
                ]);
            }
        }
    }
}
