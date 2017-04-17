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

        // Sync problems
        // Artisan::call("sync-judge:problems", ["--judge" => "codeforces"]);
        // Artisan::call("sync-judge:problems", ["--judge" => "uva"]);
        // Artisan::call("sync-judge:problems", ["--judge" => "live-archive"]);

        // Sync submissions
        // Artisan::call("sync-judge:submissions", ["--judge" => "codeforces", "user-id" => "Momentum"]);
        // Artisan::call("sync-judge:submissions", ["--judge" => "uva"]);
        // Artisan::call("sync-judge:submissions", ["--judge" => "live-archive"]);

        // Seed users
        $this->call(UsersTableSeeder::class);

        // Seed contests
        $this->call(ContestsTableSeeder::class);

        // Seed submissions
        $this->call(LanguageTableSeeder::class);

        // Seed submissions
        $this->call(SubmissionsTableSeeder::class);

        // Seed questions
        $this->call(QuestionsTableSeeder::class);

        // Seed groups
        $this->call(GroupsTableSeeder::class);

        // Seed notifications
        $this->call(NotificationsTableSeeder::class);
    }
}
