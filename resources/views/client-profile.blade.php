<x-layouts.general-client :title="'Profile'">

    <div class="flex flex-row w-full sm:flex-col md:flex-col lg:flex-row">

        @php
            $employee = [
                'full_name' => 'Robert Johnson',
                'work_email' => 'r.johnson@company.com',
                'work_phone' => '+1 (415) 555-8888',
                'profile_photo' => null,
                'office_status' => 'In Headquarters',
                'username' => 'robert.j',
                'work_location' => 'Inari, Finland'
            ];
        @endphp

        <x-profilecard :name="$employee['full_name']" greeting="Welcome Back,"
            subtitle="You have a productive day ahead!" :avatar="$employee['profile_photo']"
            :status="$employee['office_status']" statusIcon="🏢" :email="$employee['work_email']"
            :phone="$employee['work_phone']" :username="$employee['username']" :location="$employee['work_location']" />

        <x-profilesummary title="Daily Overview" :cards="[
        [
            'label' => 'Total Tasks Completed',
            'amount' => '30',
            'description' => 'Boost your productivity today',
            'icon' => '<i class=&quot;fas fa-dollar-sign&quot;></i>',
            'percentage' => '+12%',
            'percentageColor' => '#10b981',
            'bgColor' => '#fef3c7',
        ],
        [
            'label' => 'Incomplete Tasks',
            'amount' => '1,240',
            'description' => 'Check out your list',
            'icon' => '<i class=&quot;fas fa-user&quot;></i>',
            'percentage' => '+8%',
            'percentageColor' => '#3b82f6',
        ],
        [
            'label' => 'Pending Tasks',
            'amount' => '1,240',
            'description' => 'Your tasks await',
            'icon' => '<i class=&quot;fas fa-user&quot;></i>',
            'percentage' => '+8%',
            'percentageColor' => '#3b82f6',
        ]
    ]" />
    </div>
</x-layouts.general-client>