@php
    $applicantFaqs = [
        [
            'id' => 1,
            'question' => 'How do I apply for a job?',
            'answer' => 'Browse available positions on the Recruitment page, click on a job listing, and fill out the application form. You can upload your resume and it will be auto-parsed to speed up the process.',
            'keywords' => ['apply', 'job', 'application', 'resume', 'submit', 'position'],
            'quick' => true
        ],
        [
            'id' => 2,
            'question' => 'How do I check my application status?',
            'answer' => 'You can view the status of all your applications on your Applicant Dashboard. Each application will show its current status such as Submitted, Reviewed, Interview Scheduled, Hired, or Rejected.',
            'keywords' => ['status', 'check', 'application', 'progress', 'update', 'dashboard'],
            'quick' => true
        ],
        [
            'id' => 3,
            'question' => 'Can I withdraw my application?',
            'answer' => 'Yes, you can withdraw your application at any time from your dashboard. Simply find the application and click the Withdraw button. Please note that this action cannot be undone.',
            'keywords' => ['withdraw', 'cancel', 'remove', 'application', 'undo'],
            'quick' => true
        ],
        [
            'id' => 4,
            'question' => 'How will I know if I have an interview?',
            'answer' => 'If your application is shortlisted, you will receive an email notification with the interview date, time, and details. You can also view your scheduled interviews on the Interviews page in your dashboard.',
            'keywords' => ['interview', 'schedule', 'notification', 'email', 'shortlisted', 'date'],
            'quick' => false
        ],
        [
            'id' => 5,
            'question' => 'Can I apply to multiple positions?',
            'answer' => 'Yes, you can apply to as many open positions as you like. Each application is reviewed independently by our recruitment team.',
            'keywords' => ['multiple', 'positions', 'apply', 'more', 'several', 'jobs'],
            'quick' => false
        ],
        [
            'id' => 6,
            'question' => 'How do I save a job for later?',
            'answer' => 'Click the heart/save icon on any job listing to save it. You can view all your saved jobs on the Saved page in your dashboard.',
            'keywords' => ['save', 'bookmark', 'favorite', 'later', 'saved', 'heart'],
            'quick' => false
        ],
        [
            'id' => 7,
            'question' => 'Can I edit my application after submitting?',
            'answer' => 'Once submitted, applications cannot be edited. However, you can withdraw and reapply if the position is still open. Make sure to review all details before submitting.',
            'keywords' => ['edit', 'change', 'modify', 'update', 'submitted', 'reapply'],
            'quick' => false
        ],
        [
            'id' => 8,
            'question' => 'What happens after I get hired?',
            'answer' => 'Once you are hired, you will receive an email with onboarding instructions. Your account will be upgraded to an employee role with access to the employee dashboard, task assignments, and scheduling.',
            'keywords' => ['hired', 'onboarding', 'next', 'steps', 'employee', 'account'],
            'quick' => true
        ],
        [
            'id' => 9,
            'question' => 'How do I update my profile or resume?',
            'answer' => 'You can update your profile information from your dashboard by clicking on your profile. Upload a new resume or edit your personal details at any time.',
            'keywords' => ['profile', 'resume', 'update', 'edit', 'personal', 'information'],
            'quick' => false
        ],
        [
            'id' => 10,
            'question' => 'Who do I contact for support?',
            'answer' => 'If you need assistance, you can reach our recruitment team by email at finnoys0823@gmail.com or through the Contact page on our website.',
            'keywords' => ['contact', 'support', 'help', 'email', 'recruitment', 'team'],
            'quick' => false
        ]
    ];
@endphp

<x-layouts.general-applicant title="Help Center">
    <x-layouts.general-helpcenter
        :faqs="$applicantFaqs"
        title="Hi, How can we help you?"
        supportPhone="+2355555888"
    />
</x-layouts.general-applicant>
