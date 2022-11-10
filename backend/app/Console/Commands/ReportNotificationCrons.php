<?php

namespace App\Console\Commands;

use App\Mail\ReportNotificationMail;
use App\Models\ReportNotification;
use Illuminate\Support\Facades\Mail;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log as Logger;


class ReportNotificationCrons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:report_notification_crons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Report Notification Crons';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $script_name = "ReportNotificationCrons";

        $date = date("Y-m-d H:i:s");

        try {

            $models = ReportNotification::get();

            foreach ($models as $model) {
                if (in_array("Email", $model->mediums)) {

                    // if ($model->frequency == "Daily") {
                        Mail::to($model->tos)
                            ->cc($model->ccs)
                            ->bcc($model->bccs)
                            ->queue(new ReportNotificationMail($model));
                    // }
                }
                // if (in_array("Whatsapp", $model->mediums)) {
                //     Mail::to($model->tos)->send(new TestMail($model));
                // }
            }
            echo "[" . $date . "] Cron: $script_name. Report Notification Crons has been sent.\n";
            return;
        } catch (\Throwable $th) {
            echo "[" . $date . "] Cron: $script_name. Error occured while inserting logs.\n";
            Logger::channel("custom")->error("Cron: $script_name. Error Details: $th");
            return;
        }
    }
}
