@php
    $managerFaqs = [
        [
            'id' => 1,
            'question' => 'How do I create and schedule tasks?',
            'answer' => 'Navigate to the Schedule page. Click "Create Task" to open the task form. Select the date, location, and other details. Tasks must be booked at least 3 days in advance (configurable by admin).',
            'keywords' => ['schedule', 'task', 'create', 'booking', 'date'],
            'quick' => true
        ],
        [
            'id' => 2,
            'question' => 'How does the optimization algorithm assign teams?',
            'answer' => 'After creating tasks, click "Optimize Schedule" on the Schedule page. The system uses a Hybrid Algorithm (Rule-Based Preprocessing + Genetic Algorithm) to form teams of 2-3 members with at least one driver per team, then assigns them to tasks based on workload balancing.',
            'keywords' => ['optimization', 'algorithm', 'team', 'assign', 'genetic'],
            'quick' => true
        ],
        [
            'id' => 3,
            'question' => 'How do I manage company checklists?',
            'answer' => 'Go to the Checklist page to create or edit cleaning checklists specific to your company. You can add categories, items with quantities, and mark important reminders. Employees will see these checklists when completing tasks.',
            'keywords' => ['checklist', 'cleaning', 'categories', 'items', 'company'],
            'quick' => true
        ],
        [
            'id' => 4,
            'question' => 'How do I view employee performance?',
            'answer' => 'The Employees page shows all team members assigned to your tasks, including their efficiency ratings, completed tasks count, and skills. The Reports page provides detailed billing and performance analytics.',
            'keywords' => ['employees', 'performance', 'efficiency', 'reports', 'analytics'],
            'quick' => true
        ],
        [
            'id' => 5,
            'question' => 'What happens when an employee declines a task?',
            'answer' => 'If an employee declines more than 1 day before the task, the system sends a notification and attempts to find a replacement. Last-minute declines (within 1 day) trigger an urgent alert to the admin for immediate action.',
            'keywords' => ['decline', 'reject', 'replacement', 'urgent', 'notification'],
            'quick' => false
        ],
        [
            'id' => 6,
            'question' => 'How do leave requests work?',
            'answer' => 'Employees can request standard leave (minimum 4 days in advance) or emergency leave. Standard leave requests go through the approval process. Emergency leave triggers an escalation protocol with 3 levels of urgency.',
            'keywords' => ['leave', 'request', 'emergency', 'escalation', 'approval'],
            'quick' => true
        ],
        [
            'id' => 7,
            'question' => 'Where can I see task history and reviews?',
            'answer' => 'The History page shows all completed tasks. You can submit reviews and ratings for each completed task, which contributes to employee performance tracking.',
            'keywords' => ['history', 'completed', 'review', 'rating', 'feedback'],
            'quick' => false
        ],
        [
            'id' => 8,
            'question' => 'How do I track activity and notifications?',
            'answer' => 'The Activity page shows all system notifications including task assignments, employee approvals/declines, leave requests, and escalation alerts. You can also see notifications via the bell icon in the header.',
            'keywords' => ['activity', 'notifications', 'alerts', 'bell', 'updates'],
            'quick' => false
        ],
    ];
@endphp

<x-layouts.general-manager title="Help Center">
    <x-layouts.general-helpcenter
        :faqs="$managerFaqs"
        title="Company Manager Portal"
        supportPhone="+2355555888 (IT Support)"
    />
</x-layouts.general-manager>
