<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Utilities\Constants;
use App\Services\CodeforcesSyncService;
use App\Services\UVaSyncService;
use App\Services\LiveArchiveSyncService;
use Illuminate\Console\Command;

class SyncJudgeSubmissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-judge:submissions
                            {user-id : the id of the user to fetch submissions for, * to fetch all recent submissions for Codeforces}
                            {--judge=codeforces : the name of the online judge to be synced (codeforces, uva, live-archive)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch submissions from online judge and sync them with local database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $userId = $this->argument('user-id');
        $judgeName = $this->option('judge');
        $user = null;

        if ($userId != '*' || $judgeName != 'codeforces') {
            $user = User::find($userId);

            if(!$user) {
                $this->error("No user with such id was found.");
                return;
            }
        }

        switch ($judgeName) {
            case 'codeforces':
                if ($user) {
                    if ((new CodeforcesSyncService())->syncSubmissions($user))
                        $this->info(Constants::CODEFORCES_NAME . " submissions for $user->username were synced successfully.");
                    else
                        $this->error("Failed to sync $user->username's " . Constants::CODEFORCES_NAME . " submissions.");
                    break;
                }
                else {
                    if ((new CodeforcesSyncService())->syncSubmissions())
                        $this->info(Constants::CODEFORCES_NAME . " submissions were synced successfully.");
                    else
                        $this->error("Failed to sync " . Constants::CODEFORCES_NAME . " submissions.");
                    break;
                }

            case 'uva':
                if ((new UVaSyncService())->syncSubmissions($user))
                    $this->info(Constants::UVA_NAME . " submissions for $user->username were synced successfully.");
                else
                    $this->error("Failed to sync $user->username's " . Constants::UVA_NAME . " submissions.");
                break;

            case 'live-archive':
                if ((new LiveArchiveSyncService())->syncSubmissions($user))
                    $this->info(Constants::LIVE_ARCHIVE_NAME . " submissions for $user->username were synced successfully.");
                else
                    $this->error("Failed to sync $user->username's " . Constants::LIVE_ARCHIVE_NAME . " submissions.");
                break;

            default:
                $this->error($judgeName . " is not one of the supported online judges by " . config('app.name') . ".");
                break;
        }
    }
}
