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
    protected $signature = 'sync-judge:problems {name : the name of the online judge to be synced (codeforces, uva, live-archive)}';

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
        $judgeName = $this->argument('name');

        switch ($judgeName) {
            case 'codeforces':
                if ((new CodeforcesSyncService())->syncProblems())
                    $this->info('Codeforces problems synced successfully.');
                else
                    $this->error('Failed to sync Codeforces problems');
                break;
            case 'uva':
                $this->info('Problems synchronization with UVa is not supported yet.');
                break;
            case 'live-archive':
                $this->info('Problems synchronization with Live Archive is not supported yet.');
                break;
            default:
                $this->error($judgeName . ' is not one of the supported online judges by ' . config('app.name'));
                break;
        }
    }
}
