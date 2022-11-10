<?php

namespace App\Console\Commands;

use App\Mail\DbBackupMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Log as Logger;
// use Illuminate\Support\Facades\Mail;
// use App\Mail\NotifyIfLogsDoesNotGenerate;


class DbBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:db_backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Database Backup';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo exec("php artisan backup:run --only-db");

        $data = [
            'file' => collect(glob(storage_path("app/ideahrms/*.zip")))->last(),
            'date' => date('Y-M-d'),
            'body' => 'ideahrms Database Backup',
        ];

        Mail::to(env("ADMIN_MAIL_RECEIVERS"))->queue(new DbBackupMail($data));
    }
}