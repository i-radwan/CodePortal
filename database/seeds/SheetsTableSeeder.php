<?php

use App\Models\Problem;
use App\Models\Group;
use App\Models\Sheet;
use App\Utilities\Constants;
use Illuminate\Database\Seeder;

class SheetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete previous records
        DB::table(Constants::TBL_SHEETS)->delete();
        DB::table(Constants::TBL_SHEET_PROBLEMS)->delete();

        $faker = Faker\Factory::create();

        $limit = 400;

        // Get all group IDs
        $groupIDs = Group::pluck(Constants::FLD_GROUPS_ID)->toArray();
        // Get all problem IDs
        $problemIDs = Problem::pluck(Constants::FLD_PROBLEMS_ID)->toArray();

        // Insert group sheets
        for ($i = 0; $i < $limit; ++$i) {
            DB::table(Constants::TBL_SHEETS)->insert([
                Constants::FLD_SHEETS_GROUP_ID => $faker->randomElement($groupIDs),
                Constants::FLD_SHEETS_NAME => $faker->name
            ]);
        }

        // Insert group sheet problems
        foreach (Sheet::all() as $sheet) {
            $faker->unique(true);   // Reset faker unique function

            $n = $faker->numberBetween(0, 10);
            for ($i = 0; $i < $n; ++$i) {
                DB::table(Constants::TBL_SHEET_PROBLEMS)->insert([
                    Constants::FLD_SHEET_PROBLEMS_SHEET_ID => $sheet[Constants::FLD_SHEETS_ID],
                    Constants::FLD_SHEET_PROBLEMS_PROBLEM_ID => $faker->unique()->randomElement($problemIDs)
                ]);
            }
        }
    }
}
