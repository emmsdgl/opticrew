<x-layouts.general-employer :title="'Edit Profile'">
    <section class="flex w-full flex-col p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <div class="max-w-4xl mx-auto w-full">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Profile</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Update your personal information and profile picture</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Profile Picture Upload -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Profile Picture</h2>

                <div class="flex items-center space-x-6">
                    <!-- Current Profile Picture -->
                    <div class="relative">
                        <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://i.pravatar.cc/150' }}"
                             alt="Profile Picture"
                             class="w-24 h-24 rounded-full object-cover border-4 border-gray-200 dark:border-gray-700">
                    </div>

                    <!-- Upload Form -->
                    <div class="flex-1">
                        <form action="{{ route('admin.profile.upload-picture') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Choose new picture
                                </label>
                                <input type="file"
                                       name="profile_picture"
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500 dark:text-gray-400
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-lg file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-blue-50 file:text-blue-700
                                              hover:file:bg-blue-100
                                              dark:file:bg-blue-900 dark:file:text-blue-200
                                              dark:hover:file:bg-blue-800"
                                       required>
                            </div>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                Upload Picture
                            </button>
                        </form>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">JPG, PNG or GIF. Max size 2MB.</p>
                    </div>
                </div>
            </div>

            <!-- Profile Information Form -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h2>

                <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Full Name
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', $user->name) }}"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Username
                        </label>
                        <input type="text"
                               name="username"
                               id="username"
                               value="{{ old('username', $user->username) }}"
                               placeholder="Enter username"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Email Address
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email', $user->email) }}"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Phone Number
                        </label>
                        <input type="text"
                               name="phone"
                               id="phone"
                               value="{{ old('phone', $user->phone) }}"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Location
                        </label>
                        <input type="text"
                               name="location"
                               id="location"
                               value="{{ old('location', $user->location) }}"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-4 pt-4">
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            Save Changes
                        </button>
                        <a href="{{ route('admin.profile') }}"
                           class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.general-employer>
