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
        // //Backup
        $schedule
            ->command('task:db_backup')
            ->dailyAt('3:00')
            // ->everyMinute()
            ->appendOutputTo("db_backup.log")
            ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

        // PDF
        $schedule
            ->command('task:generate_audit_report')
            // ->everyMinute()
            // ->everyThirtyMinutes()
            ->dailyAt('1:00')
            //->hourly()
            ->appendOutputTo(storage_path("logs/pdf.log"))
            ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));




        // if (env("APP_ENV") == "production") {
        //     $schedule
        //         ->command('task:sync_attendance_logs')
        //         // ->everyThirtyMinutes()
        //         ->everyMinute()
        //         ->between('7:00', '23:59')
        //         ->appendOutputTo("scheduler.log")
        //         ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));
        //     $schedule
        //         ->command('task:update_company_ids')
        //         // ->everyThirtyMinutes()
        //         ->everyMinute()
        //         ->between('7:00', '23:59')
        //         ->withoutOverlapping()
        //         ->appendOutputTo("scheduler.log")
        //         ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));
        //     $schedule
        //         ->command('task:sync_attendance')
        //         // ->everyThirtyMinutes()
        //         ->everyMinute()
        //         ->between('7:00', '23:59')
        //         ->withoutOverlapping()
        //         ->appendOutputTo("scheduler.log")
        //         ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));
        //     $schedule
        //         ->command('task:sync_absent')
        //         ->dailyAt('1:00')
        //         ->appendOutputTo("scheduler.log")
        //         ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));
        // }
        // PDF
        // $schedule
        //     ->command('task:generate_summary_report')
        //     ->everyMinute()
        //     // ->dailyAt('2:00')
        //     ->appendOutputTo("pdf.log")
        //     ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));
        // $schedule
        //     ->command('task:generate_daily_present_report')
        //     ->everyMinute()
        //     // ->dailyAt('2:00')
        //     ->appendOutputTo("pdf.log")
        //     ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));
        // $schedule
        //     ->command('task:generate_daily_absent_report')
        //     ->everyMinute()
        //     // ->dailyAt('2:00')
        //     ->appendOutputTo("pdf.log")
        //     ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));
        // $schedule
        //     ->command('task:generate_daily_missing_report')
        //     ->everyMinute()
        //     // ->dailyAt('2:00')
        //     ->appendOutputTo("pdf.log")
        //     ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));
        // $schedule
        //     ->command('task:generate_daily_manual_report')
        //     ->everyMinute()
        //     // ->dailyAt('2:00')
        //     ->appendOutputTo("pdf.log")
        //     ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));
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
