<?php

use App\Utilities\Constants;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete previous records
        DB::table(Constants::TBL_USERS)->delete();

        $faker = Faker\Factory::create();

        $limit = 80;

        for ($i = 0; $i < $limit; ++$i) {
            DB::table(Constants::TBL_USERS)->insert([
                Constants::FLD_USERS_USERNAME => !$i ? "asd" : $faker->unique()->userName,
                Constants::FLD_USERS_EMAIL => $faker->unique()->email,
                Constants::FLD_USERS_PASSWORD => bcrypt('asdasd'),
                Constants::FLD_USERS_COUNTRY => $faker->country,
                Constants::FLD_USERS_GENDER => $faker->randomElement(Constants::USER_GENDER),
                Constants::FLD_USERS_FIRST_NAME => $faker->firstName,
                Constants::FLD_USERS_LAST_NAME => $faker->lastName,
                Constants::FLD_USERS_BIRTHDATE => $faker->dateTimeBetween(),
                Constants::FLD_USERS_PROFILE_PICTURE => $faker->imageUrl(),
                Constants::FLD_USERS_ROLE => $faker->randomElement(Constants::ACCOUNT_ROLE),
            ]);
        }
    }
}
