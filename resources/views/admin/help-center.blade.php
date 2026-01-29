@php
    $employerFaqs = [
        [
            'id' => 1,
            'question' => 'How do I approve client service requests?',
            'answer' => 'Navigate to the Task Manager. You can review incoming requests and approve them, which automatically converts them into active tasks on the assigned employee’s dashboard.',
            'keywords' => ['approve', 'request', 'convert', 'task', 'manager'],
            'quick' => true
        ],
        [
            'id' => 2,
            'question' => 'How does the system prevent employee overassignment?',
            'answer' => 'The system uses Workload Balancing to automatically distribute tasks fairly based on workload limits and employee availability to prevent scheduling conflicts.',
            'keywords' => ['overassignment', 'workload', 'balancing', 'limit', 'conflict'],
            'quick' => true
        ],
        [
            'id' => 3,
            'question' => 'What happens if a task exceeds the 12-hour work limit?',
            'answer' => 'The system detects the violation and applies your chosen configuration: it will either reassign the task to the next available day or allow an override based on your settings.',
            'keywords' => ['12-hour', 'limit', 'reschedule', 'override', 'duration'],
            'quick' => false
        ],
        [
            'id' => 4,
            'question' => 'How do I configure salary and premium pay rates?',
            'answer' => 'In the Payroll and Finance module, you can define rules for regular hours, holidays, Sundays, and premium pay. These rates are saved securely and applied to all computations.',
            'keywords' => ['salary', 'payroll', 'premium', 'holiday', 'rates'],
            'quick' => true
        ],
        [
            'id' => 5,
            'question' => 'Can I track if employees are at the correct location?',
            'answer' => 'Yes. The Attendance Tracker uses geofencing to ensure employees only clock in or out when they are within the approved work locations.',
            'keywords' => ['geofencing', 'location', 'clock-in', 'attendance', 'gps'],
            'quick' => true
        ],
        [
            'id' => 6,
            'question' => 'How do I create a custom cleaning checklist?',
            'answer' => 'Use the Employee Cleaning Checklist Configuration to create standardized lists. You can include step-by-step instructions and set specific time allocations for each task.',
            'keywords' => ['checklist', 'instructions', 'cleaning', 'standardized', 'job'],
            'quick' => false
        ],
        [
            'id' => 7,
            'question' => 'Where can I see customer satisfaction scores?',
            'answer' => 'Customer Satisfaction (CSAT) scores are automatically calculated from client feedback and displayed within the service performance records linked to completed tasks.',
            'keywords' => ['CSAT', 'feedback', 'rating', 'satisfaction', 'score'],
            'quick' => false
        ],
        [
            'id' => 8,
            'question' => 'How do I manage job applicants?',
            'answer' => 'The Recruitment module allows you to post openings and track applicants. The system automatically highlights candidates who match your predefined skill tags.',
            'keywords' => ['recruitment', 'applicants', 'hiring', 'skills', 'matching'],
            'quick' => true
        ],
        [
            'id' => 9,
            'question' => 'Can I export performance and payroll reports?',
            'answer' => 'Yes, the Data Export Tools allow you to download reports regarding revenue, payroll, and performance analytics for external analysis.',
            'keywords' => ['export', 'download', 'report', 'analytics', 'payroll'],
            'quick' => false
        ],
        [
            'id' => 10,
            'question' => 'How do I update my business billing information?',
            'answer' => 'Business and billing details can be updated in Account Details Management. Changes are validated and reflected across the system immediately.',
            'keywords' => ['billing', 'business', 'account', 'details', 'profile'],
            'quick' => false
        ]
    ];
@endphp

<x-layouts.general-client title="Employer Help Center">
    <x-layouts.general-helpcenter
        :faqs="$employerFaqs" 
        title="Employer Administrative Portal"
        supportPhone="+2355555888 (IT Support)"
    />
</x-layouts.general-client>