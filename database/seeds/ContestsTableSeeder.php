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
        DB::table(Constants::TBL_CONTEST_PROBLEMS)->delete();

        $faker = Faker\Factory::create();

        $limit = 200;

        // Get all user IDs
        $userIDs = User::all()->pluck(Constants::FLD_USERS_ID)->toArray();
        // Get all team IDs
        $teamIDs = Team::all()->pluck(Constants::FLD_TEAMS_ID)->toArray();

        for ($i = 0; $i < $limit; ++$i) {
            DB::table(Constants::TBL_CONTESTS)->insert([
                Constants::FLD_CONTESTS_OWNER_ID => $faker->randomElement($userIDs),
                Constants::FLD_CONTESTS_NAME => $faker->sentence,
                Constants::FLD_CONTESTS_VISIBILITY => $faker->randomElement(Constants::CONTEST_VISIBILITIES),
                Constants::FLD_CONTESTS_TIME => $faker->dateTimeThisMonth,
                Constants::FLD_CONTESTS_DURATION => $faker->numberBetween(30, 340),
            ]);
        }

        // Get all contest IDs
        $contestIDs = Contest::all()->pluck(Constants::FLD_CONTESTS_ID)->toArray();
        // Get all problem IDs
        $problemIDs = Problem::all()->pluck(Constants::FLD_PROBLEMS_ID)->toArray();

        // Contests Problems
        for ($i = 0; $i < $limit * 5; ++$i) {
            try {
                DB::table(Constants::TBL_CONTEST_PROBLEMS)->insert([
                    Constants::FLD_CONTEST_PROBLEMS_CONTEST_ID => $faker->randomElement($contestIDs),
                    Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ID => $faker->randomElement($problemIDs),
                ]);
            } catch (\Illuminate\Database\QueryException $e) {

            };
        }


        // Set problems orders
        $firstContestID = Contest::first()->id;
        for ($j = 0; $j < Contest::count(); $j++) {
            $i = 1;
            $contest = Contest::find($firstContestID + $j);
            $problemIDs = $contest->problems()->pluck('id')->toArray();
            foreach ($problemIDs as $problemID) {
                $problemPivot = $contest->problems()->find($problemID)->pivot;
                $problemPivot[Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ORDER] = $i;
                $problemPivot->save();
                $i++;
            }
        }

        // Contests Organizers
        for ($i = 0; $i < $limit * 3; ++$i) {
            // Insert if only not exists
            try {
                DB::table(Constants::TBL_CONTEST_ADMINS)->insert([
                    Constants::FLD_CONTEST_ADMINS_ADMIN_ID => $faker->randomElement($userIDs),
                    Constants::FLD_CONTEST_ADMINS_CONTEST_ID => $faker->randomElement($contestIDs),
                ]);
            } catch (\Illuminate\Database\QueryException $e) {

            };
        }

        // Contests Participants
        for ($i = 0; $i < $limit * 10; ++$i) {
            try {
                DB::table(Constants::TBL_CONTEST_PARTICIPANTS)->insert([
                    Constants::FLD_CONTEST_PARTICIPANTS_CONTEST_ID => $faker->randomElement($contestIDs),
                    Constants::FLD_CONTEST_PARTICIPANTS_USER_ID => $faker->randomElement($userIDs),
                ]);
            } catch (\Illuminate\Database\QueryException $e) {

            };
        }

        // Contests Teams
        for ($i = 0; $i < $limit * 3; ++$i) {
            try {
                DB::table(Constants::TBL_CONTEST_PARTICIPANTS)->insert([
                    Constants::FLD_CONTEST_TEAMS_CONTEST_ID => $faker->randomElement($contestIDs),
                    Constants::FLD_CONTEST_TEAMS_TEAM_ID => $faker->randomElement($teamIDs),
                ]);

            } catch (\Illuminate\Database\QueryException $e) {

            };
        }
    }
}
