<?php

namespace App\Console\Commands;

use App\Models\AttendanceLog;
use App\Models\Device;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log as Logger;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifyIfLogsDoesNotGenerate;
use Illuminate\Support\Facades\DB;

class UpdateCompanyIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:update_company_ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Company Ids';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = date("Y-m-d H:i:s");

        // get device ids with company ids = 0
        $model = AttendanceLog::query();
        $model->distinct('DeviceID');
        $model->where("company_id", 0);
        $model->take(10);
        $free_device_ids = $model->pluck("DeviceID");

        // get company ids against found device ids
        $rows = Device::whereIn("device_id", $free_device_ids)->get(["company_id", "device_id as DeviceID"])->toArray();

        if (count($rows) == 0 || count($free_device_ids) == 0) {
            echo "[".$date."] Cron: UpdateCompanyIds. No new record found while updating company ids for device.\n";
            return;
        }

        $i = 0;

        foreach ($rows as $arr) {
            try {
                $i++;
                AttendanceLog::where("company_id", 0)->where("DeviceID", $arr["DeviceID"])->update($arr);
            } catch (\Throwable $th) {
                Logger::channel("custom")->error('Cron: UpdateCompanyIds. Error Details: ' . $th);

                $data = [
                    'title' => 'Quick action required',
                    'body' => $th,
                ];

                echo "[".$date."] Cron: UpdateCompanyIds. Error occured while updating company ids.\n";
                Mail::to(env("ADMIN_MAIL_RECEIVERS"))->send(new NotifyIfLogsDoesNotGenerate($data));
                return;
            }
        }
        $log_details = DB::table('attendance_logs')->orderByDesc("id")->take(5)->get(["UserID","DeviceID","SerialNumber","company_id","checked"])->toArray();

        $result = array_reverse($log_details);

        echo "[".$date."] Cron: UpdateCompanyIds. Company IDS has been updated. Details: " . json_encode($result) . ".\n";
        return;
    }
}
