<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Log;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';

    protected $description = 'Backup the database';

    protected $process;

    public function __construct()
    {
        parent::__construct();

        $today = today()->format('Y-m-d');
        if (!is_dir(storage_path('backups'))) mkdir(storage_path('backups'));

        $this->process = new Process(sprintf(
            'mysqldump --compact --skip-comments -u%s -p%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            storage_path("backups/{$today}.sql")
        ));
    }

    public function handle()
    {
        try {
            $this->process->mustRun();
            Log::info('Daily DB Backup - Success');
        } catch (ProcessFailedException $exception) {
            Log::error('Daily DB Backup - Failed', $exception);
        }
    }
}