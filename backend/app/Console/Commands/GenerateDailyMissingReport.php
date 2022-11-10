<?php

namespace App\Console\Commands;

use App\Http\Controllers\Reports\DailyController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log as Logger;

class GenerateDailyMissingReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:generate_daily_missing_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily Missing Report';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $script_name = "GenerateDailyMissingReport";

        $date = date("Y-m-d H:i:s");

        try {
            $Attendance = new DailyController;

            if (!$Attendance->generateMissingReport()) {
                echo "[" . $date . "] Cron: $script_name. Daily Missing report file cannot generated.\n";
                return;
            }

            echo "[" . $date . "] Cron: $script_name. Daily Missing report generated.\n";
            return;
        } catch (\Throwable $th) {
            echo "[" . $date . "] Cron: $script_name. Error occured while inserting logs.\n";
            Logger::channel("custom")->error("Cron: $script_name. Error Details: $th");
            return;
        }
    }
}
