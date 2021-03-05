<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;

class ClearQueryLogFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'querylog:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear log files';

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
        //
        exec('rm ' . storage_path('logs/query.log'));

        Log::info('Logs have been cleared!');
    }
}
