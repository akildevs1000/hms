<?php

namespace App\Console;

use App\Mail\ReportNotificationMail;
use App\Models\Company;
use App\Models\ReportNotification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule
            ->command('app:process-audit-freeze')
            ->dailyAt('00:30')
            ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

        $schedule
            ->command('task:generate_reports')
            ->dailyAt('1:00')
            ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

        $schedule
            ->command('task:db_backup')
            ->dailyAt('3:00')
            ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

        $schedule
            ->command('send:audit_report')
            // ->everyFiveMinutes()
            ->dailyAt('9:00')
            ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));


        // generate records in background for report

        $companyIds = Company::where("is_background_jobs", true)->pluck("id");

        foreach ($companyIds as $companyId) {
            $schedule
                ->command("record:generate-daily-summary $companyId")
                // ->everyFiveMinutes()
                ->dailyAt('1:30')
                ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

            $schedule
                ->command("record:generate-daily-cash $companyId")
                // ->everyFiveMinutes()
                ->dailyAt('1:45')
                ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

            $schedule
                ->command("record:generate-daily-ota $companyId")
                // ->everyFiveMinutes()
                ->dailyAt('2:00')
                ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
