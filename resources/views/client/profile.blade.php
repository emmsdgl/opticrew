    <x-layouts.general-client :title="'Profile'">
        <section role="status" class="flex w-full flex-col lg:flex-row p-4 md:p-6 min-h-[calc(100vh-4rem)]">
            <!-- Left Panel - Dashboard Content -->
            <div
                class="flex flex-col gap-6 flex-1 w-full rounded-lg">
                <!-- Inner Up - Dashboard Header -->
                <div
                    class="w-full h-full rounded-lg">
                    @php
                        $user = Auth::user();
                        $client = [
                            'full_name' => $user->name,
                            'work_email' => $user->email,
                            'work_phone' => $user->phone ?? '+358 40 123 4567',
                            'profile_photo' => $user->profile_picture ? asset($user->profile_picture) . '?v=' . time() : null,
                            'office_status' => 'Client',
                            'username' => $user->username,
                            'work_location' => $user->location ?? 'Inari, Finland'
                        ];
                    @endphp

                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    <x-profilecard :name="$client['full_name']" greeting="Welcome Back,"
                        subtitle="You have a productive day ahead!" :avatar="$client['profile_photo']"
                        :status="$client['office_status']" statusIcon="🏢" :email="$client['work_email']"
                        :phone="$client['work_phone']" :username="$client['username']"
                        :location="$client['work_location']" />

                </div>
            </div>
        </section>
    </x-layouts.general-client>