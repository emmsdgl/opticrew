<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Notification;
use App\Models\User;
use App\Services\Notification\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendMonthlyEvaluationReminder extends Command
{
    protected $signature = 'opticrew:monthly-evaluation-reminder';
    protected $description = 'Notify admin to conduct monthly performance evaluations';

    public function handle()
    {
        $notificationService = app(NotificationService::class);

        $activeEmployees = Employee::where('is_active', true)->count();
        $lastMonth = now()->subMonth();
        $monthName = $lastMonth->format('F Y');

        // Find admin users (role = admin or employer)
        $admins = User::where('role', 'admin')
            ->orWhere('role', 'employer')
            ->get();

        if ($admins->isEmpty()) {
            $this->info('No admin users found.');
            return;
        }

        foreach ($admins as $admin) {
            // Check if reminder already sent this month
            $alreadySent = Notification::where('user_id', $admin->id)
                ->where('type', Notification::TYPE_MONTHLY_EVALUATION_REMINDER)
                ->where('created_at', '>=', now()->startOfMonth())
                ->exists();

            if ($alreadySent) {
                continue;
            }

            $notificationService->create(
                $admin,
                Notification::TYPE_MONTHLY_EVALUATION_REMINDER,
                'Monthly Performance Evaluations Due',
                "It's time to conduct performance evaluations for {$monthName}. You have {$activeEmployees} active employees to evaluate. Use the auto-fill feature to speed up the process.",
                [
                    'link' => route('admin.reports.performance.index', [
                        'month' => $lastMonth->month,
                        'year' => $lastMonth->year,
                    ]),
                    'employee_count' => $activeEmployees,
                    'period' => $monthName,
                ]
            );
        }

        $this->info("Monthly evaluation reminders sent to {$admins->count()} admin(s).");
    }
}
