<?php

use App\Utilities\Constants;
use Illuminate\Database\Seeder;

class LanguageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete previous records
        DB::table(Constants::TBL_LANGUAGES)->delete();

        $faker = Faker\Factory::create();

        $limit = 25;

        for ($i = 0; $i < $limit; ++$i) {
            DB::table(Constants::TBL_LANGUAGES)->insert([
                Constants::FLD_LANGUAGES_NAME => $faker->unique()->languageCode
            ]);
        }
    }
}
