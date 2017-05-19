<?php

namespace App\Console\Commands;



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
    protected $signature = 'sync:judge-problems
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
     */
    public function __construct()
    {
        parent::__construct();
    }

    //
    // Constants
    //
    const OPT_JUDGE_NAME = 'judge';
    const JUDGE_CODEFORCES = 'codeforces';
    const JUDGE_UVA = 'uva';
    const JUDGE_LIVE_ARCHIVE = 'live-archive';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $judgeName = $this->option(self::OPT_JUDGE_NAME);
        $syncService = null;

        switch ($judgeName) {
            case self::JUDGE_CODEFORCES:
                $syncService = new CodeforcesSyncService();
                break;
            case self::JUDGE_UVA:
                $syncService = new UVaSyncService();
                break;
            case self::JUDGE_LIVE_ARCHIVE:
                $syncService = new LiveArchiveSyncService();
                break;
            default:
                $this->warn("$judgeName is not one of the supported online judges by " . config('app.name') . ".");
                return;
        }

        if ($syncService->syncProblems())
            $this->info("$judgeName problems synced successfully.");
        else
            $this->error("Failed to sync $judgeName problems.");
    }
}
