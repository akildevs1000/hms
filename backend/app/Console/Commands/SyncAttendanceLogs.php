<?php

namespace App\Console\Commands;

use App\Models\AttendanceLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log as Logger;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifyIfLogsDoesNotGenerate;


class SyncAttendanceLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:sync_attendance_logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Attendance Logs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = date("Y-m-d H:i:s");

        $file = base_path() . "/logs/logs.csv";

        if (!file_exists($file)) {

            echo "[".$date."] Cron: SyncAttendanceLogs. No new data found.\n";
            return;
        }

        $data = [];

        if (($handle = fopen($file, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {

                $data[] = array_combine(["UserID", "DeviceID", "LogTime", "SerialNumber"], $row);

            }
            fclose($handle);
        }
        try {
            $created = AttendanceLog::insert($data);
            $created ? unlink($file) : 0;
            $count = count($data);
            echo "[".$date."] Cron: SyncAttendanceLogs. " . $count . " new logs has been inserted. Old file has been deleted.\n";
            echo "[".$date."] Cron: SyncAttendanceLogs. Log Details: " . json_encode($data) . "\n";
            return;
        } catch (\Throwable $th) {

            Logger::channel("custom")->error('Cron: SyncAttendanceLogs. Error Details: ' . $th);

            $data = [
                'title' => 'Quick action required',
                'body' => $th,
            ];

            Mail::to(env("ADMIN_MAIL_RECEIVERS"))->send(new NotifyIfLogsDoesNotGenerate($data));
            echo "[".$date."] Cron: SyncAttendanceLogs. Error occured while inserting logs.\n";
            return;
        }
    }
}
