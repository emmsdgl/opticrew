    <x-layouts.general-client :title="'Profile'">
        <x-skeleton-page :preset="'profile'">
        <section role="status" class="flex w-full flex-col lg:flex-row p-4 md:p-6 min-h-[calc(100vh-4rem)]">
            <!-- Left Panel - Dashboard Content -->
            <div class="flex flex-col gap-6 flex-1 w-full rounded-lg">
                <div class="w-full h-full rounded-lg">
                    @php
                        $user = Auth::user();
                        $client = $user->client;

                        // Handle both old and new profile picture paths
                        $profilePhotoUrl = null;
                        if ($user->profile_picture) {
                            if (str_starts_with($user->profile_picture, 'profile_pictures/')) {
                                $profilePhotoUrl = asset('storage/' . $user->profile_picture);
                            } else {
                                $profilePhotoUrl = asset($user->profile_picture);
                            }
                            $profilePhotoUrl .= '?v=' . time();
                        }

                        // Appointment stats
                        $totalAppointments = $client ? $client->appointments()->count() : 0;
                        $completedAppointments = $client ? $client->appointments()->where('status', 'Completed')->count() : 0;
                        $pendingAppointments = $client ? $client->appointments()->whereIn('status', ['Pending', 'Scheduled'])->count() : 0;
                    @endphp

                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    <x-material-ui.profile-card
                        :user="$user"
                        :stats="[
                            ['value' => $totalAppointments, 'label' => 'Appointments', 'color' => 'text-blue-500 dark:text-blue-400'],
                            ['value' => $completedAppointments, 'label' => 'Completed', 'color' => 'text-green-500 dark:text-green-400'],
                            ['value' => $pendingAppointments, 'label' => 'Pending', 'color' => 'text-yellow-500 dark:text-yellow-400'],
                        ]"
                        coverUploadRoute="{{ route('client.profile.upload-cover') }}"
                    />

                    {{-- Profile Details --}}
                    <div class="mt-6">
                        <x-profilecard :name="$user->name" greeting="Welcome Back,"
                            subtitle="You have a productive day ahead!" :avatar="$profilePhotoUrl"
                            :status="'Client'" statusIcon="🏠" :email="$user->email"
                            :phone="$user->phone ?? '+358 40 123 4567'" :username="$user->username"
                            :location="$user->location ?? 'Inari, Finland'" />
                    </div>

                </div>
            </div>
        </section>
        </x-skeleton-page>
    </x-layouts.general-client>