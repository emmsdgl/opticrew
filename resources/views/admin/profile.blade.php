<x-layouts.general-employer :title="'Profile'">

        <section role="status" class="flex w-full flex-col lg:flex-row p-4 md:p-6 min-h-[calc(100vh-4rem)]">
            <!-- Left Panel - Dashboard Content -->
            <div
                class="flex flex-col gap-6 flex-1 w-full rounded-lg">
                <!-- Inner Up - Dashboard Header -->
                <div
                    class="w-full h-full rounded-lg">
                    @php
                        $user = Auth::user();

                        // Handle both old and new profile picture paths
                        $profilePhotoUrl = null;
                        if ($user->profile_picture) {
                            // Check if it's old format (starts with 'profile_pictures/')
                            if (str_starts_with($user->profile_picture, 'profile_pictures/')) {
                                $profilePhotoUrl = asset('storage/' . $user->profile_picture);
                            } else {
                                // New format (starts with 'uploads/')
                                $profilePhotoUrl = asset($user->profile_picture);
                            }
                            $profilePhotoUrl .= '?v=' . time();
                        }

                        $employer = [
                            'full_name' => $user->name,
                            'work_email' => $user->email,
                            'work_phone' => $user->phone ?? '+358 40 123 4567',
                            'profile_photo' => $profilePhotoUrl,
                            'office_status' => 'Administrator',
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

                    <x-profilecard :name="$employer['full_name']" greeting="Welcome Back,"
                        subtitle="You have a productive day ahead!" :avatar="$employer['profile_photo']"
                        :status="$employer['office_status']" statusIcon="🏢" :email="$employer['work_email']"
                        :phone="$employer['work_phone']" :username="$employer['username']"
                        :location="$employer['work_location']" />

                </div>
            </div>

            <!-- Right Panel - Tasks Overview -->
            <div
                class="flex flex-col flex-1 h-auto justify-start rounded-3xl">

                <!-- Inner Up - Recommendation Service List -->
                <div class="w-full overflow-y-auto rounded-lg h-full sm:h-full md:h-full">
                        <x-profilesummary title="Daily Overview" :cards="$cards" />
                </div>
        </section>
</x-layouts.general-employer>