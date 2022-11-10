<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
// use Illuminate\Support\Facades\Log as Logger;
// use Illuminate\Support\Facades\Mail;
// use App\Mail\NotifyIfLogsDoesNotGenerate;


class RestartSDK extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:restart_sdk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restart SDK';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo exec("sudo pm2 reload all");
    }
}
