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
            $userID = $faker->randomElement($userIDs);
            $contestID = $faker->randomElement($contestIDs);
            if (!DB::table(Constants::TBL_CONTEST_ADMINS)
                ->where(Constants::FLD_CONTEST_ADMINS_ADMIN_ID, $userID)
                ->where(Constants::FLD_CONTEST_ADMINS_CONTEST_ID, $contestID)->get()->count()
            )
                DB::table(Constants::TBL_CONTEST_ADMINS)->insert([
                    Constants::FLD_CONTEST_ADMINS_ADMIN_ID => $userID,
                    Constants::FLD_CONTEST_ADMINS_CONTEST_ID => $contestID,
                ]);
        }
        // Contests Participants
        for ($i = 0; $i < count($contestIDs) * 10; $i++) {
            $userID = $faker->randomElement($userIDs);
            $contestID = $faker->randomElement($contestIDs);
            if (!DB::table(Constants::TBL_CONTEST_PARTICIPANTS)
                ->where(Constants::FLD_CONTEST_PARTICIPANTS_USER_ID, $userID)
                ->where(Constants::FLD_CONTEST_PARTICIPANTS_CONTEST_ID, $contestID)->get()->count()
            )
                DB::table(Constants::TBL_CONTEST_PARTICIPANTS)->insert([
                    Constants::FLD_CONTEST_PARTICIPANTS_USER_ID => $userID,
                    Constants::FLD_CONTEST_PARTICIPANTS_CONTEST_ID => $contestID,
                ]);
        }
        // Contests Problems
        $problemIDs = Problem::all()->pluck('id')->toArray();
        for ($i = 0; $i < count($contestIDs) * 5; $i++) {
            $contestID = $faker->randomElement($contestIDs);
            $problemID = $faker->randomElement($problemIDs);
            if (!DB::table(Constants::TBL_CONTEST_PROBLEMS)
                ->where(Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ID, $problemID)
                ->where(Constants::FLD_CONTEST_PROBLEMS_CONTEST_ID, $contestID)->get()->count()
            )
                DB::table(Constants::TBL_CONTEST_PROBLEMS)->insert([
                    Constants::FLD_CONTEST_PROBLEMS_CONTEST_ID => $contestID,
                    Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ID => $problemID,
                ]);
        }


    }
}
