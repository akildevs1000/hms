<?php

namespace App\Console\Commands;

use App\Http\Controllers\Reports\DailyController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log as Logger;


class GenerateSummaryReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:generate_summary_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Summary Report';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $script_name = "GenerateSummaryReport";

        $date = date("Y-m-d H:i:s");

        try {
            $Attendance = new DailyController;

            if (!$Attendance->generateSummaryReport()) {
                echo "[" . $date . "] Cron: $script_name. Summary report cannot generated.\n";
                return;
            }

            echo "[" . $date . "] Cron: $script_name. Summary report generated.\n";
            return;
        } catch (\Throwable $th) {
            echo "[" . $date . "] Cron: $script_name. Error occured while inserting logs.\n";
            Logger::channel("custom")->error("Cron: $script_name. Error Details: $th");
            return;
        }
    }
}
