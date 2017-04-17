<?php

use App\Models\User;
use App\Models\Team;
use App\Utilities\Constants;
use Illuminate\Database\Seeder;

class TeamsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete previous records
        DB::table(Constants::TBL_TEAMS)->delete();
        DB::table(Constants::TBL_TEAM_MEMBERS)->delete();

        $faker = Faker\Factory::create();

        $limit = 15;

        // Insert teams
        for ($i = 0; $i < $limit; ++$i) {
            DB::table(Constants::TBL_TEAMS)->insert([
                Constants::FLD_TEAMS_NAME => $faker->name
            ]);
        }

        // Get all team IDs
        $teamIDs = Team::all()->pluck(Constants::FLD_TEAMS_ID)->toArray();
        // Get all user IDs
        $userIDs = User::all()->pluck(Constants::FLD_USERS_ID)->toArray();

        // Insert members to teams
        for ($i = 0; $i < $limit; ++$i) {
            $n = $faker->numberBetween(1, 3);

            for ($j = 0; $j < $n; ++$j) {
                try {
                    DB::table(Constants::TBL_TEAM_MEMBERS)->insert([
                        Constants::FLD_TEAM_MEMBERS_TEAM_ID => $teamIDs[$i],
                        Constants::FLD_TEAM_MEMBERS_USER_ID => $faker->randomElement($userIDs)
                    ]);
                }
                catch (\Illuminate\Database\QueryException $e) {

                };
            }
        }
    }
}
