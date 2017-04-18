<?php

use Illuminate\Database\Seeder;
use App\Utilities\Constants;
use App\Models\Group;
use App\Models\User;
use App\Models\Problem;
use App\Models\Contest;
use App\Models\Sheet;

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

        $limit = 300;
        // All user IDs
        $userIDs = User::all()->pluck(Constants::FLD_USERS_ID)->toArray();

        // Seed groups
        for ($i = 0; $i < $limit; $i++) {
            DB::table(Constants::TBL_GROUPS)->insert([
                Constants::FLD_GROUPS_OWNER_ID => $faker->randomElement($userIDs),
                Constants::FLD_GROUPS_NAME => $faker->sentence,
            ]);
        }

        // Seed groups join requests and members
        $groupIDs = Group::all()->pluck(Constants::FLD_GROUPS_ID)->toArray();

        for ($i = 0; $i < $limit; $i++) {
            try {
                DB::table(Constants::TBL_GROUP_JOIN_REQUESTS)->insert([
                    Constants::FLD_GROUPS_JOIN_REQUESTS_GROUP_ID => $faker->randomElement($groupIDs),
                    Constants::FLD_GROUPS_JOIN_REQUESTS_USER_ID => $faker->randomElement($userIDs)
                ]);
                DB::table(Constants::TBL_GROUP_MEMBERS)->insert([
                    Constants::FLD_GROUP_MEMBERS_GROUP_ID => $faker->randomElement($groupIDs),
                    Constants::FLD_GROUP_MEMBERS_USER_ID => $faker->randomElement($userIDs)
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
            };
        }

        // Seed group contests
        $contestIDs = Contest::all()->pluck(Constants::FLD_CONTESTS_ID)->toArray();

        for ($i = 0; $i < $limit; $i++) {
            try {

                DB::table(Constants::TBL_GROUP_CONTESTS)->insert([
                    Constants::FLD_GROUP_CONTESTS_GROUP_ID => $faker->randomElement($groupIDs),
                    Constants::FLD_GROUP_CONTESTS_CONTEST_ID => $faker->randomElement($contestIDs)
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
            };
        }

        // Seed sheets
        for ($i = 0; $i < $limit; $i++) {
            DB::table(Constants::TBL_SHEETS)->insert([
                Constants::FLD_SHEETS_GROUP_ID => $faker->randomElement($groupIDs),
                Constants::FLD_SHEETS_NAME => $faker->name
            ]);
        }

        // Seed sheets problems
        $sheetIDs = Sheet::all()->pluck(Constants::FLD_SHEETS_ID)->toArray();
        $problemIDs = Problem::all()->pluck(Constants::FLD_PROBLEMS_ID)->toArray();
        for ($i = 0; $i < $limit; $i++) {
            try {
                DB::table(Constants::TBL_SHEETS)->insert([
                    Constants::FLD_SHEET_PROBLEMS_SHEET_ID => $faker->randomElement($sheetIDs),
                    Constants::FLD_SHEET_PROBLEMS_PROBLEM_ID => $faker->randomElement($problemIDs),
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
            };
        }
    }
}
