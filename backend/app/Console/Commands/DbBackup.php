<?php

namespace App\Console\Commands;

use App\Mail\DbBackupMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log as Logger;
use Illuminate\Support\Facades\Mail;

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
            'file' => collect(glob(storage_path("app/ezhms/*.zip")))->last(),
            'date' => date('Y-M-d H:i'),
            'body' => 'ezhms Database Backup',
        ];

        Mail::to(env("ADMIN_MAIL_RECEIVERS"))->send(new DbBackupMail($data));
        $this->deleteOldBackupFiles();

    }
    private function deleteOldBackupFiles()
    {
        $storagePath = storage_path('app/ezhms'); // Replace with your actual storage path
        $thresholdDate = Carbon::now()->subDays(7);

        $filesToCollect = File::glob($storagePath . '/*.zip');

        $filteredFiles = array_filter($filesToCollect, function ($file) use ($thresholdDate) {
            return filemtime($file) <= $thresholdDate->timestamp;
        });

// Now you can work with the $filteredFiles array
        foreach ($filteredFiles as $file) {
            echo $file . "\n";

            File::delete($file);
            Logger::channel("custom")->error("Cron: DB Backup - OLD File is  deleted.  : $file");
        }
    }
}
