<?php

namespace App\Console;

use App\Mail\ReportNotificationMail;
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
            ->command('birthday:wish:customer')
            ->dailyAt('00:00')
            ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

        // PDF
        $schedule
            ->command('task:generate_audit_report')
            // ->everyMinute()
            // ->everyThirtyMinutes()
            ->dailyAt('23:50')
            //->hourly()
            ->appendOutputTo(storage_path("logs/pdf.log"))
            ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

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