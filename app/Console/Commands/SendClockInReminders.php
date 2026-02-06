<?php

namespace App\Console\Commands;

use App\Services\Notification\NotificationService;
use Illuminate\Console\Command;

class SendClockInReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:clock-in-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send clock in reminders to employees who have not clocked in today';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService)
    {
        $this->info('Sending clock in reminders...');

        $notifications = $notificationService->sendDailyClockInReminders();

        $count = $notifications->count();
        $this->info("Sent {$count} clock in reminder(s).");

        return Command::SUCCESS;
    }
}
