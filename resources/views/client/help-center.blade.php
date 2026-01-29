@php
    $clientFaqs = [
        [
            'id' => 1,
            'question' => 'What cleaning services do you offer?',
            'answer' => 'We offer a comprehensive range of cleaning services including Standard Cleaning, Deep Cleaning, Move In/Out Cleaning, and specialized services tailored to your needs.',
            'keywords' => ['cleaning', 'services', 'offer', 'standard', 'deep', 'move'],
            'quick' => true
        ],
        [
            'id' => 2,
            'question' => 'Can I get a quote for my home?',
            'answer' => 'Yes! We provide customized quotes based on your home type, number of bedrooms, and bathrooms. Fill out our online form for a quote within 24 hours.',
            'keywords' => ['quote', 'price', 'cost', 'home', 'bedrooms', 'bathrooms', 'estimate'],
            'quick' => false
        ],
        [
            'id' => 3,
            'question' => 'What’s included in a Standard cleaning?',
            'answer' => 'Our Standard cleaning includes dusting, vacuuming, mopping, bathroom sanitization, and kitchen cleaning.',
            'keywords' => ['standard', 'cleaning', 'included', 'checklist', 'dusting', 'vacuuming', 'mopping'],
            'quick' => false
        ],
        [
            'id' => 4,
            'question' => 'Can I schedule a recurring cleaning?',
            'answer' => 'Absolutely! We offer flexible recurring cleaning schedules including weekly, bi-weekly, and monthly options with discounted rates.',
            'keywords' => ['schedule', 'recurring', 'weekly', 'bi-weekly', 'monthly', 'regular'],
            'quick' => false
        ],
        [
            'id' => 5,
            'question' => 'How do I reschedule or cancel?',
            'answer' => 'You can reschedule or cancel through your dashboard. We require 24 hours notice to avoid fees.',
            'keywords' => ['reschedule', 'cancel', 'cancellation', 'policy', 'appointment', 'change'],
            'quick' => true
        ],
        [
            'id' => 6,
            'question' => 'What payment methods do you accept?',
            'answer' => 'We accept cash, major credit cards, Airpesa, and Google Pay through our secure app.',
            'keywords' => ['payment', 'methods', 'credit', 'card', 'airpesa', 'google', 'pay', 'accept'],
            'quick' => false
        ],
        [
            'id' => 7,
            'question' => 'Who do I contact for damages?',
            'answer' => 'Please contact us within 24 hours at +2355555888 or through the app if anything was missed or damaged.',
            'keywords' => ['contact', 'missed', 'damaged', 'problem', 'issue', 'complaint', 'support'],
            'quick' => false
        ],
        [
            'id' => 8,
            'question' => 'How do I cancel an appointment?',
            'answer' => 'You can cancel via your dashboard under Upcoming Appointments. Select the appointment and click Cancel.',
            'keywords' => ['cancel', 'cancellation', 'stop', 'delete'],
            'quick' => true
        ],
        [
            'id' => 9,
            'question' => 'Are the appointment details editable?',
            'answer' => 'Yes, you can edit the address, notes for the cleaner, or entry instructions up to 12 hours before the start time.',
            'keywords' => ['edit', 'change', 'details', 'modify', 'update'],
            'quick' => false
        ],
        [
            'id' => 10,
            'question' => 'Until when can I cancel my appointment?',
            'answer' => 'Cancellations are free up to 24 hours before the appointment.',
            'keywords' => ['until', 'when', 'deadline', 'cancel', 'timeframe'],
            'quick' => true
        ]
    ];
@endphp

<x-layouts.general-client title="Help Center">
    <x-layouts.general-helpcenter 
        :faqs="$clientFaqs" 
        title="Hi, How can we help you?"
        supportPhone="+2355555888"
    />
</x-layouts.general-client>