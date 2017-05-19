<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\CodeforcesSyncService;
use App\Services\LiveArchiveSyncService;
use App\Services\UVaSyncService;
use App\Utilities\Constants;
use Illuminate\Console\Command;
use DB;

class SyncNewHandles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:handles-submissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch new handles from the db queue to sync their submissions!';

    /**
     * Create a new command instance.
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
        \Log::info("Syncing new users handles");
        // Get user handles to sync their submissions from database table
        $handles = \DB::table(Constants::TBL_HANDLES_SYNC_QUEUE)
            ->select()
            ->limit(5)
            ->get();

        // iterate over the queue first 5 handles to sync their submissions
        $handles->each(function ($handle) {
            $handle = (array)$handle;
            $userID = $handle[Constants::FLD_HANDLES_SYNC_QUEUE_USER_ID];
            $judgeID = $handle[Constants::FLD_HANDLES_SYNC_QUEUE_JUDGE_ID];
            switch ($judgeID) {
                case Constants::JUDGE_CODEFORCES_ID:
                    $syncService = new CodeforcesSyncService();
                    break;
                case Constants::JUDGE_UVA_ID:
                    $syncService = new UVaSyncService();
                    break;
                case Constants::JUDGE_LIVE_ARCHIVE_ID:
                    $syncService = new LiveArchiveSyncService();
                    break;
            }
            $syncStatus = $syncService->syncSubmissions(User::find($userID)->first());

            // Remove the handle from the table if sync succeeded
            if ($syncStatus) {
                DB::table(Constants::TBL_HANDLES_SYNC_QUEUE)
                    ->where(Constants::FLD_HANDLES_SYNC_QUEUE_USER_ID, $userID)
                    ->where(Constants::FLD_HANDLES_SYNC_QUEUE_JUDGE_ID, $judgeID)
                    ->delete();
            }
        });
    }
}
