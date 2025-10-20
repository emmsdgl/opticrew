    <x-layouts.general-employer :title="'Profile'">
        <section role="status" class="flex w-full flex-col lg:flex-row p-4 md:p-6 min-h-[calc(100vh-4rem)]">
            <!-- Left Panel - Dashboard Content -->
            <div
                class="flex flex-col gap-6 flex-1 w-full rounded-lg">
                <!-- Inner Up - Dashboard Header -->
                <div
                    class="w-full flex-1 h-full rounded-lg">
                    @php
                        $client = [
                            'full_name' => 'Robert Johnson',
                            'work_email' => 'r.johnson@company.com',
                            'work_phone' => '+1 (415) 555-8888',
                            'profile_photo' => null,
                            'office_status' => 'In Headquarters',
                            'username' => 'robert.j',
                            'work_location' => 'Inari, Finland'
                        ];
                    @endphp

                    <x-profilecard :name="$client['full_name']" greeting="Welcome Back,"
                        subtitle="You have a productive day ahead!" :avatar="$client['profile_photo']"
                        :status="$client['office_status']" statusIcon="🏢" :email="$client['work_email']"
                        :phone="$client['work_phone']" :username="$client['username']"
                        :location="$client['work_location']" />

                </div>
            </div>

            <!-- Right Panel - Tasks Overview -->
            <div
                class="flex flex-col flex-1 h-full w-1/2 justify-start rounded-3xl">

                <!-- Inner Up - Recommendation Service List -->
                <div class="w-full overflow-y-auto rounded-lg h-full sm:h-full md:h-full">
                    <div
                        class="flex gap-6 p-2 overflow-y-auto snap-x snap-mandatory scroll-smooth scrollbar-custom w-full">
                        <x-profilesummary title="Daily Overview" :cards="[
            [
                'label' => 'Total Tasks Completed',
                'amount' => '30',
                'description' => 'Boost your productivity today',
                'icon' => '<i class=&quot;fas fa-check-circle&quot;></i>',
                'percentage' => '+12%',
                'percentageColor' => '#10b981',
                'bgColor' => '#fef3c7',
            ],
            [
                'label' => 'Incomplete Tasks',
                'amount' => '1,240',
                'description' => 'Check out your list',
                'icon' => '<i class=&quot;fas fa-times-circle&quot;></i>',
                'percentage' => '+8%',
                'percentageColor' => '#3b82f6',
            ],
            [
                'label' => 'Pending Tasks',
                'amount' => '1,240',
                'description' => 'Your tasks await',
                'icon' => '<i class=&quot;fas fa-hourglass-half&quot;></i>',
                'percentage' => '+8%',
                'percentageColor' => '#3b82f6',
            ],
        ]" />
                    </div>
                </div>
        </section>
    </x-layouts.general-employer>