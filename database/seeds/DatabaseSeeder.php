<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();

        // Seed users
        $this->call(UsersTableSeeder::class);

        // Sync problems
        Artisan::call("sync-judge:problems", ["--judge" => "codeforces"]);
        Artisan::call("sync-judge:problems", ["--judge" => "uva"]);
        Artisan::call("sync-judge:problems", ["--judge" => "live-archive"]);

        // Sync submissions
        // Artisan::call("sync-judge:submissions", ["--judge" => "codeforces", "user-id" => "Momentum"]);
        // Artisan::call("sync-judge:submissions", ["--judge" => "uva"]);
        // Artisan::call("sync-judge:submissions", ["--judge" => "live-archive"]);

        // Seed contests
        $this->call(ContestsTableSeeder::class);

        // Seed notifications
        $this->call(NotificationsTableSeeder::class);

        // Seed questions
        $this->call(QuestionsTableSeeder::class);


    }
}
