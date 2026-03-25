<x-layouts.general-manager :title="'Profile'">
    <div class="flex flex-col lg:flex-row gap-6 w-full">
        <!-- Left Panel -->
        <div class="flex flex-col gap-6 flex-1 w-full rounded-lg">
            <div class="w-full h-full rounded-lg">
                @php
                    $user = Auth::user();

                    $profilePhotoUrl = null;
                    if ($user->profile_picture) {
                        if (str_starts_with($user->profile_picture, 'profile_pictures/')) {
                            $profilePhotoUrl = asset('storage/' . $user->profile_picture);
                        } else {
                            $profilePhotoUrl = asset($user->profile_picture);
                        }
                        $profilePhotoUrl .= '?v=' . time();
                    }

                    $employer = [
                        'full_name' => $user->name,
                        'work_email' => $user->email,
                        'work_phone' => $user->phone ?? '',
                        'profile_photo' => $profilePhotoUrl,
                        'office_status' => 'Company Manager',
                        'username' => $user->username,
                        'work_location' => $user->location ?? 'Finland'
                    ];
                @endphp

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

        <!-- Right Panel -->
        <div class="flex flex-col flex-1 h-auto justify-start rounded-3xl">
            <div class="w-full overflow-y-auto rounded-lg h-full">
                <x-profilesummary title="Daily Overview" :cards="$cards" />
            </div>
        </div>
    </div>
</x-layouts.general-manager>
