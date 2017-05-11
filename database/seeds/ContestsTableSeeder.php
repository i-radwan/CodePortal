<?php

use App\Models\User;
use App\Models\Team;
use App\Models\Problem;
use App\Models\Contest;
use App\Utilities\Constants;
use Illuminate\Database\Seeder;

class ContestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete previous records
        DB::table(Constants::TBL_CONTESTS)->delete();
        DB::table(Constants::TBL_CONTEST_ADMINS)->delete();
        DB::table(Constants::TBL_CONTEST_PARTICIPANTS)->delete();
        DB::table(Constants::TBL_CONTEST_TEAMS)->delete();
        DB::table(Constants::TBL_CONTEST_PROBLEMS)->delete();

        $faker = Faker\Factory::create();

        $limit = 200;

        // Get all user IDs
        $userIDs = User::pluck(Constants::FLD_USERS_ID)->toArray();
        // Get all team IDs
        $teamIDs = Team::pluck(Constants::FLD_TEAMS_ID)->toArray();
        // Get all problem IDs
        $problemIDs = Problem::pluck(Constants::FLD_PROBLEMS_ID)->toArray();

        // Insert contests
        for ($i = 0; $i < $limit; ++$i) {
            DB::table(Constants::TBL_CONTESTS)->insert([
                Constants::FLD_CONTESTS_OWNER_ID => $faker->randomElement($userIDs),
                Constants::FLD_CONTESTS_NAME => $faker->sentence,
                Constants::FLD_CONTESTS_VISIBILITY => $faker->randomElement(Constants::CONTEST_VISIBILITIES),
                Constants::FLD_CONTESTS_TIME => $faker->dateTimeBetween('-2 week', '+1 week'),
                Constants::FLD_CONTESTS_DURATION => $faker->numberBetween(30, 340),
            ]);
        }

        // Insert contests data
        foreach (Contest::all() as $contest) {
            $faker->unique(true);   // Reset faker unique function

            // Problems
            $n = $faker->numberBetween(0, Constants::CONTESTS_PROBLEMS_MAX_COUNT);
            for ($i = 0; $i < $n; ++$i) {
                DB::table(Constants::TBL_CONTEST_PROBLEMS)->insert([
                    Constants::FLD_CONTEST_PROBLEMS_CONTEST_ID => $contest[Constants::FLD_CONTESTS_ID],
                    Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ID => $faker->unique()->randomElement($problemIDs),
                    Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ORDER => $i + 1
                ]);
            }

            $faker->unique(true);   // Reset faker unique function

            // Organizers
            $n = $faker->numberBetween(0, 4);
            for ($i = 0; $i < $n; ++$i) {
                DB::table(Constants::TBL_CONTEST_ADMINS)->insert([
                    Constants::FLD_CONTEST_ADMINS_CONTEST_ID => $contest[Constants::FLD_CONTESTS_ID],
                    Constants::FLD_CONTEST_ADMINS_ADMIN_ID => $faker->unique()->randomElement($userIDs),
                ]);
            }

            // Participants
            $n = $faker->numberBetween(0, 20);
            for ($i = 0; $i < $n; ++$i) {
                DB::table(Constants::TBL_CONTEST_PARTICIPANTS)->insert([
                    Constants::FLD_CONTEST_PARTICIPANTS_CONTEST_ID => $contest[Constants::FLD_CONTESTS_ID],
                    Constants::FLD_CONTEST_PARTICIPANTS_USER_ID => $faker->unique()->randomElement($userIDs),
                ]);
            }

            $faker->unique(true);   // Reset faker unique function

            // Teams
            $n = $faker->numberBetween(0, 5);
            for ($i = 0; $i < $n; ++$i) {
                DB::table(Constants::TBL_CONTEST_TEAMS)->insert([
                    Constants::FLD_CONTEST_TEAMS_CONTEST_ID => $contest[Constants::FLD_CONTESTS_ID],
                    Constants::FLD_CONTEST_TEAMS_TEAM_ID => $faker->unique()->randomElement($teamIDs),
                ]);
            }
        }
    }
}
