<?php

use App\Models\Contest;
use App\Models\Language;
use App\Utilities\Constants;
use Illuminate\Database\Seeder;

class SubmissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete previous records
        DB::table(Constants::TBL_SUBMISSIONS)->delete();

        $faker = Faker\Factory::create();

        // Get all contest IDs
        $contestIDs = Contest::all()->pluck(Constants::FLD_CONTESTS_ID)->toArray();
        // Get all language IDs
        $languageIDs = Language::all()->pluck(Constants::FLD_LANGUAGES_ID)->toArray();

        $contestsCount = count($contestIDs);

        for ($i = 0; $i < $contestsCount * 100; ++$i) {
            $contest = Contest::find($contestIDs[$i % $contestsCount]);

            $contestParticipantsIDs = $contest->participants()->pluck(Constants::FLD_USERS_ID)->toArray();
            $contestProblemsIDs = $contest->problems()->pluck(Constants::FLD_PROBLEMS_ID)->toArray();

            $userID = $faker->randomElement($contestParticipantsIDs);
            $problemID = $faker->randomElement($contestProblemsIDs);

            $contestStartTime = strtotime($contest->time);
            $contestEndTime = strtotime($contest->time . ' + ' . $contest->duration . ' minute');

            if (!$problemID || !$userID) continue;

            // Insert submission
            DB::table(Constants::TBL_SUBMISSIONS)->insert([
                Constants::FLD_SUBMISSIONS_JUDGE_SUBMISSION_ID => $faker->unique()->numberBetween(0, 10000000),
                Constants::FLD_SUBMISSIONS_USER_ID => $userID,
                Constants::FLD_SUBMISSIONS_PROBLEM_ID => $problemID,
                Constants::FLD_SUBMISSIONS_LANGUAGE_ID => $faker->randomElement($languageIDs),
                Constants::FLD_SUBMISSIONS_SUBMISSION_TIME => $faker->numberBetween($contestStartTime, $contestEndTime),
                Constants::FLD_SUBMISSIONS_EXECUTION_TIME => $faker->numberBetween(0, 5000),
                Constants::FLD_SUBMISSIONS_CONSUMED_MEMORY => $faker->numberBetween(0, 10000000),
                Constants::FLD_SUBMISSIONS_VERDICT => $faker->randomElement(Constants::CODEFORCES_SUBMISSION_VERDICTS),
            ]);
        }
    }
}
