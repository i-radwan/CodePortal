<?php

use Illuminate\Database\Seeder;
use App\Utilities\Constants;
use App\Models\Contest;

class QuestionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(Constants::TBL_QUESTIONS)->delete();

        $faker = Faker\Factory::create();

        $contestIDs = Contest::all()->pluck('id')->toArray();
        $contestsCount = count($contestIDs) - 1;
        // Contests Participants
        for ($i = 0; $i < $contestsCount * 10; $i++) {

            $contestParticipantsIDs = Contest::find($contestIDs[$i % $contestsCount])->participants()->pluck('id')->toArray();
            $contestOrganizersIDs = Contest::find($contestIDs[$i % $contestsCount])->organizers()->pluck('id')->toArray();
            $contestProblemsIDs = Contest::find($contestIDs[$i % $contestsCount])->problems()->pluck('id')->toArray();

            $contestID = $faker->randomElement($contestIDs);
            $userID = $faker->randomElement($contestParticipantsIDs);
            $organizerID = $faker->randomElement($contestOrganizersIDs);
            $problemID = $faker->randomElement($contestProblemsIDs);
            if ($i % 3 == 0) {
                DB::table(Constants::TBL_QUESTIONS)->insert([
                    Constants::FLD_QUESTIONS_USER_ID => $userID,
                    Constants::FLD_QUESTIONS_PROBLEM_ID => $problemID,
                    Constants::FLD_QUESTIONS_CONTEST_ID => $contestID,
                    Constants::FLD_QUESTIONS_TITLE => $faker->title,
                    Constants::FLD_QUESTIONS_CONTENT => $faker->text,
                    Constants::FLD_QUESTIONS_STATUS => $faker->randomElement(Constants::QUESTION_STATUS),
                    Constants::FLD_QUESTIONS_ANSWER => $faker->text,
                    Constants::FLD_QUESTIONS_ADMIN_ID => $organizerID,

                ]);
            } else {
                DB::table(Constants::TBL_QUESTIONS)->insert([
                    Constants::FLD_QUESTIONS_USER_ID => $userID,
                    Constants::FLD_QUESTIONS_PROBLEM_ID => $problemID,
                    Constants::FLD_QUESTIONS_CONTEST_ID => $contestID,
                    Constants::FLD_QUESTIONS_TITLE => $faker->title,
                    Constants::FLD_QUESTIONS_CONTENT => $faker->text,
                    Constants::FLD_QUESTIONS_STATUS => $faker->randomElement(Constants::QUESTION_STATUS),
                ]);
            }
        }
    }
}
