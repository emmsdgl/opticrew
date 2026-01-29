@php
    $employeeFaqs = [
        [
            'id' => 1,
            'question' => 'How do I manage my assigned tasks?',
            'answer' => 'You can view all tasks assigned by your employer in the Task Management module. You must approve or decline tasks; once approved, they appear in your interactive calendar[cite: 20, 22].',
            'keywords' => ['task', 'assignment', 'approve', 'decline', 'calendar'],
            'quick' => true
        ],
        [
            'id' => 2,
            'question' => 'How do I update the progress of a cleaning task?',
            'answer' => 'While working, you can update task status to "In Progress" or "Completed." These updates reflect in real-time on the employer dashboard[cite: 24, 25].',
            'keywords' => ['progress', 'status', 'complete', 'update', 'real-time'],
            'quick' => true
        ],
        [
            'id' => 3,
            'question' => 'Where can I find the cleaning checklists?',
            'answer' => 'The system provides standardized checklists for each task, including step-by-step instructions and specific time allocations[cite: 26].',
            'keywords' => ['checklist', 'instructions', 'steps', 'standardized'],
            'quick' => false
        ],
        [
            'id' => 4,
            'question' => 'How do I submit a leave or vacation request?',
            'answer' => 'Submit leave requests (vacation, sick, or personal) via the Absence Management module. Status updates (approved, pending, rejected) appear in real-time on your dashboard[cite: 31].',
            'keywords' => ['leave', 'vacation', 'sick', 'request', 'absence'],
            'quick' => true
        ],
        [
            'id' => 5,
            'question' => 'How can I view my scheduled day-offs?',
            'answer' => 'Approved day-offs are displayed in an interactive calendar view to prevent scheduling conflicts with assigned tasks[cite: 50].',
            'keywords' => ['day-off', 'holiday', 'schedule', 'calendar', 'conflict'],
            'quick' => false
        ],
        [
            'id' => 6,
            'question' => 'Where can I see my performance metrics?',
            'answer' => 'The Performance Tracker displays metrics such as task completion rates, attendance, training progress, and customer satisfaction scores[cite: 61].',
            'keywords' => ['performance', 'metrics', 'rating', 'scores', 'attendance'],
            'quick' => true
        ],
        [
            'id' => 7,
            'question' => 'How do I access training videos?',
            'answer' => 'Access assigned video tutorials and skill-based courses in the Skill Training Hub. Your certification status updates automatically upon completion[cite: 63, 64].',
            'keywords' => ['training', 'video', 'skill', 'hub', 'course', 'certification'],
            'quick' => false
        ],
        [
            'id' => 8,
            'question' => 'How do I submit feedback about my work experience?',
            'answer' => 'You can submit monthly evaluation forms securely through the Feedback Submission section for employer review[cite: 66, 68].',
            'keywords' => ['feedback', 'evaluation', 'form', 'survey', 'experience'],
            'quick' => false
        ],
        [
            'id' => 9,
            'question' => 'Can I update my personal contact information?',
            'answer' => 'Yes, you can update your contact details, address, and login credentials in Account Details Management. Changes reflect immediately across the system[cite: 86].',
            'keywords' => ['account', 'profile', 'edit', 'update', 'login', 'credentials'],
            'quick' => false
        ],
        [
            'id' => 10,
            'question' => 'Where do I find notifications for schedule changes?',
            'answer' => 'Real-time alerts regarding task updates, schedule changes, and system announcements appear in your dashboard notification panel[cite: 86].',
            'keywords' => ['notification', 'alert', 'message', 'announcement', 'change'],
            'quick' => true
        ]
    ];
@endphp

<x-layouts.general-client title="Employee Help Center">
    <x-help-center 
        :faqs="$employeeFaqs" 
        title="Employee Support Portal"
        supportPhone="+2355555888"
    />
</x-layouts.general-client>