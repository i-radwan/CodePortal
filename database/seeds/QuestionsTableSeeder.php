<?php

use App\Models\Contest;
use App\Utilities\Constants;
use Illuminate\Database\Seeder;

class QuestionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete previous records
        DB::table(Constants::TBL_QUESTIONS)->delete();

        $faker = Faker\Factory::create();


        // Get all contest IDs
        $contestIDs = Contest::all()->pluck(Constants::FLD_CONTESTS_ID)->toArray();

        $contestsCount = count($contestIDs);

        for ($i = 0; $i < $contestsCount * 10; ++$i) {
            $contest = Contest::find($contestIDs[$i % $contestsCount]);

            $contestParticipantsIDs = $contest->participants()->pluck(Constants::FLD_USERS_ID)->toArray();
            $contestOrganizersIDs = $contest->organizers()->pluck(Constants::FLD_USERS_ID)->toArray();
            $contestProblemsIDs = $contest->problems()->pluck(Constants::FLD_PROBLEMS_ID)->toArray();

            $userID = $faker->randomElement($contestParticipantsIDs);
            $organizerID = $faker->randomElement($contestOrganizersIDs);
            $problemID = $faker->randomElement($contestProblemsIDs);

            if (!$problemID || !$userID || !$organizerID) continue;

            if ($i % 3 == 0) {
                // Insert question with answer
                DB::table(Constants::TBL_QUESTIONS)->insert([
                    Constants::FLD_QUESTIONS_USER_ID => $userID,
                    Constants::FLD_QUESTIONS_PROBLEM_ID => $problemID,
                    Constants::FLD_QUESTIONS_CONTEST_ID => $contest->id,
                    Constants::FLD_QUESTIONS_TITLE => $faker->title,
                    Constants::FLD_QUESTIONS_CONTENT => $faker->text,
                    Constants::FLD_QUESTIONS_STATUS => $faker->randomElement(Constants::QUESTION_STATUS),
                    Constants::FLD_QUESTIONS_ANSWER => $faker->text,
                    Constants::FLD_QUESTIONS_ADMIN_ID => $organizerID,

                ]);
            }
            else {
                // Insert question without answer
                DB::table(Constants::TBL_QUESTIONS)->insert([
                    Constants::FLD_QUESTIONS_USER_ID => $userID,
                    Constants::FLD_QUESTIONS_PROBLEM_ID => $problemID,
                    Constants::FLD_QUESTIONS_CONTEST_ID => $contest->id,
                    Constants::FLD_QUESTIONS_TITLE => $faker->title,
                    Constants::FLD_QUESTIONS_CONTENT => $faker->text,
                    Constants::FLD_QUESTIONS_STATUS => $faker->randomElement(Constants::QUESTION_STATUS),
                ]);
            }
        }
    }
}
