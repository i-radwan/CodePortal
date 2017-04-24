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
        $this->call(ProblemsSeeder::class);

        // Seed users
        $this->call(UsersTableSeeder::class);

        // Seed teams
        $this->call(TeamsTableSeeder::class);

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

        // Seed sheets
        $this->call(SheetsTableSeeder::class);

        // Seed notifications
        $this->call(NotificationsTableSeeder::class);
    }
}
