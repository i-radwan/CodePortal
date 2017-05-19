<?php

namespace App\Console\Commands;

use App\Models\Judge;
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
    protected $signature = 'sync:judge-submissions
                            {user-id : the id of the user to fetch submissions for, write * to fetch submissions for all users}
                            {--judge=codeforces : the name of the online judge to be synced (codeforces, uva, live-archive)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch submissions from online judge and sync them with local database';

    //
    // Constants
    //
    const ARG_USER_ID = 'user-id';
    const OPT_JUDGE_NAME = 'judge';
    const JUDGE_CODEFORCES = 'codeforces';
    const JUDGE_UVA = 'uva';
    const JUDGE_LIVE_ARCHIVE = 'live-archive';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument(self::ARG_USER_ID);
        $judgeName = $this->option(self::OPT_JUDGE_NAME);
        $user = null;
        $syncService = null;

        switch ($judgeName) {
            case self::JUDGE_CODEFORCES:
                $syncService = new CodeforcesSyncService();
                $judgeId = Constants::JUDGE_CODEFORCES_ID;
                break;
            case self::JUDGE_UVA:
                $syncService = new UVaSyncService();
                $judgeId = Constants::JUDGE_UVA_ID;
                break;
            case self::JUDGE_LIVE_ARCHIVE:
                $syncService = new LiveArchiveSyncService();
                $judgeId = Constants::JUDGE_LIVE_ARCHIVE_ID;
                break;
            default:
                $this->warn("$judgeName is not one of the supported online judges by " . config('app.name') . ".");
                return;
        }

        // Sync for a specific user
        if ($userId != '*') {
            $user = User::find($userId);

            if(!$user)
                $this->warn("No user with such id.");
            else if ($syncService->syncSubmissions($user))
                $this->info("$judgeName submissions for $user->username were synced successfully.");
            else
                $this->error("Failed to sync $user->username's $judgeName submissions.");
            return;
        }

        // Sync last 1000 recent submissions for Codeforces
        if ($judgeName == self::JUDGE_CODEFORCES) {
            if ($syncService->syncSubmissions())
                $this->info("Finished syncing submissions form $judgeName.");
            else
                $this->error("Failed to sync $judgeName submissions.");
        }
        // Sync all user's submissions for uHunt
        else {
            $users = Judge::find($judgeId)->users()->get();
            foreach ($users as $user) {
                if (!$syncService->syncSubmissions($user)) {
                    $this->error("Failed to sync $user->username's $judgeName submissions.");
                }
            }
            $this->info("Finished syncing submissions form $judgeName.");
        }
    }
}
