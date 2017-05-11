<?php

use Illuminate\Database\Seeder;

class ProblemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call("sync-judge:problems", ["--judge" => "codeforces"]);
        Artisan::call("sync-judge:problems", ["--judge" => "uva"]);
        Artisan::call("sync-judge:problems", ["--judge" => "live-archive"]);
    }
}
