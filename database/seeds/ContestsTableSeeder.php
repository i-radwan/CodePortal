<?php

use Illuminate\Database\Seeder;
use App\Utilities\Constants;
use App\Models\User;
use App\Models\Contest;
use App\Models\Problem;

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
        // All user IDs
        $userIDs = User::all()->pluck('id')->toArray();

        for ($i = 0; $i < $limit; $i++) {
            DB::table(Constants::TBL_CONTESTS)->insert([
                Constants::FLD_CONTESTS_OWNER_ID => $faker->randomElement($userIDs),
                Constants::FLD_CONTESTS_NAME => $faker->sentence,
                Constants::FLD_CONTESTS_VISIBILITY => $faker->randomElement(Constants::CONTEST_VISIBILITY),
                Constants::FLD_CONTESTS_TIME => $faker->dateTimeThisMonth,
                Constants::FLD_CONTESTS_DURATION => $faker->numberBetween(30, 150),
            ]);
        }

        $contestIDs = Contest::all()->pluck('id')->toArray();

        // Contests Organizers
        for ($i = 0; $i < count($contestIDs) * 3; $i++) {
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
        for ($i = 0; $i < count($contestIDs) * 10; $i++) {
            try {
                DB::table(Constants::TBL_CONTEST_PARTICIPANTS)->insert([
                    Constants::FLD_CONTEST_PARTICIPANTS_USER_ID => $faker->randomElement($userIDs),
                    Constants::FLD_CONTEST_PARTICIPANTS_CONTEST_ID => $faker->randomElement($contestIDs),
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
            };
        }
        // Contests Problems
        $problemIDs = Problem::all()->pluck('id')->toArray();
        for ($i = 0; $i < count($contestIDs) * 5; $i++) {
            try {
                DB::table(Constants::TBL_CONTEST_PROBLEMS)->insert([
                    Constants::FLD_CONTEST_PROBLEMS_CONTEST_ID => $faker->randomElement($contestIDs),
                    Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ID => $faker->randomElement($problemIDs),
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
            };
        }


    }
}
