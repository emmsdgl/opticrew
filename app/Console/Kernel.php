<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        // Send clock in reminders every weekday morning at 7:00 AM
        $schedule->command('notifications:clock-in-reminders')
            ->weekdays()
            ->at('07:00')
            ->withoutOverlapping();

        // SCENARIO #11: Process emergency leave escalation every 15 minutes
        $schedule->command('opticrew:escalate-emergency-leaves')
            ->everyFifteenMinutes()
            ->withoutOverlapping();

        // SCENARIO #14/#15: Process unstaffed tasks every 10 minutes
        $schedule->command('opticrew:process-unstaffed-tasks')
            ->everyTenMinutes()
            ->withoutOverlapping();

        // SCENARIO #19/#21: Process task approval grace periods and late clock-ins every 5 minutes
        $schedule->command('opticrew:process-task-grace-periods')
            ->everyFiveMinutes()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
