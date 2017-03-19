<?php

namespace App\Console\Commands;

use App\Utilities\Constants;
use App\Services\CodeforcesSyncService;
use App\Services\UVaSyncService;
use App\Services\LiveArchiveSyncService;
use Illuminate\Console\Command;

class SyncJudgeProblems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-judge:problems
                            {--judge=codeforces : the name of the online judge to be synced (codeforces, uva, live-archive)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch problems from online judge and sync them with local database';

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
        $judgeName = $this->option('judge');

        switch ($judgeName) {
            case 'codeforces':
                if ((new CodeforcesSyncService())->syncProblems())
                    $this->info(Constants::CODEFORCES_NAME . " problems synced successfully.");
                else
                    $this->error("Failed to sync " . Constants::CODEFORCES_NAME . " problems.");
                break;

            case 'uva':
                if ((new UVaSyncService())->syncProblems())
                    $this->info(Constants::UVA_NAME . " problems synced successfully.");
                else
                    $this->error("Failed to sync " . Constants::UVA_NAME . " problems.");
                break;

            case 'live-archive':
                if ((new LiveArchiveSyncService())->syncProblems())
                    $this->info(Constants::LIVE_ARCHIVE_NAME . " problems synced successfully.");
                else
                    $this->error("Failed to sync " . Constants::LIVE_ARCHIVE_NAME . " problems.");
                break;

            default:
                $this->error($judgeName . " is not one of the supported online judges by " . config('app.name') . ".");
                break;
        }
    }
}
