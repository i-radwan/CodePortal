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

        $limit = 10;

        // Loop through every contest
        foreach (Contest::all() as $contest) {
            $contestProblemsIDs = $contest->problems()->pluck(Constants::FLD_PROBLEMS_ID)->toArray();
            $contestParticipantsIDs = $contest->participants()->pluck(Constants::FLD_USERS_ID)->toArray();
            $contestOrganizersIDs = $contest->organizers()->pluck(Constants::FLD_USERS_ID)->toArray();
            $contestOrganizersIDs[] = $contest[Constants::FLD_CONTESTS_OWNER_ID];

            if (!$contestProblemsIDs || !$contestParticipantsIDs) {
                continue;
            }

            // Insert questions
            $n = $faker->numberBetween(0, $limit);
            for ($i = 0; $i < $n; ++$i) {
                // Basic question attribute
                $attributes = [
                    Constants::FLD_QUESTIONS_USER_ID => $faker->randomElement($contestParticipantsIDs),
                    Constants::FLD_QUESTIONS_PROBLEM_ID => $faker->randomElement($contestProblemsIDs),
                    Constants::FLD_QUESTIONS_CONTEST_ID => $contest->id,
                    Constants::FLD_QUESTIONS_TITLE => $faker->title,
                    Constants::FLD_QUESTIONS_CONTENT => $faker->text,
                    Constants::FLD_QUESTIONS_STATUS => $faker->randomElement(Constants::QUESTION_STATUS),
                ];

                // Add answer
                if ($faker->boolean()) {
                    $attributes[Constants::FLD_QUESTIONS_ANSWER] = $faker->text;
                    $attributes[Constants::FLD_QUESTIONS_ADMIN_ID] = $faker->randomElement($contestOrganizersIDs);
                }

                DB::table(Constants::TBL_QUESTIONS)->insert($attributes);
            }
        }
    }
}
