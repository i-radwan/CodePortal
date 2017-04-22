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

        $limit = 80;

        // Get all users IDs
        $userIDs = User::pluck(Constants::FLD_USERS_ID)->toArray();

        // Insert teams
        for ($i = 0; $i < $limit; ++$i) {
            DB::table(Constants::TBL_TEAMS)->insert([
                Constants::FLD_TEAMS_NAME => $faker->name
            ]);
        }

        // Insert members to teams
        foreach (Team::all() as $team) {
            $faker->unique(true);   // Reset faker unique function

            $n = $faker->numberBetween(1, Constants::TEAM_MEMBERS_MAX_COUNT);
            for ($i = 0; $i < $n; ++$i) {
                DB::table(Constants::TBL_TEAM_MEMBERS)->insert([
                    Constants::FLD_TEAM_MEMBERS_TEAM_ID => $team[Constants::FLD_TEAMS_ID],
                    Constants::FLD_TEAM_MEMBERS_USER_ID => $faker->unique()->randomElement($userIDs)
                ]);
            }
        }
    }
}
