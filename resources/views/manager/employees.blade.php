<x-layouts.general-manager :title="'Team Members'">
    <div class="flex flex-col gap-6 w-full">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Team Members</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ $employees->count() }} employees assigned to your tasks
                </p>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1 relative">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text"
                           id="searchInput"
                           placeholder="Search by name or skills..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <!-- Filter -->
                <div class="flex gap-2">
                    <select class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Employee Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="employeeGrid">
            @forelse($employees as $employee)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition-shadow duration-200 employee-card"
                     data-name="{{ strtolower($employee->user->name ?? '') }}"
                     data-skills="{{ strtolower(implode(',', $employee->skills ?? [])) }}">
                    <div class="flex items-start gap-4">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 font-semibold text-lg">
                                {{ strtoupper(substr($employee->user->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', $employee->user->name ?? 'U')[1] ?? '', 0, 1)) }}
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $employee->user->name ?? 'Unknown' }}
                                </h3>
                                <!-- Status Indicator -->
                                <span class="flex-shrink-0 w-2 h-2 rounded-full {{ $employee->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ $employee->user->email ?? '' }}
                            </p>

                            <!-- Skills -->
                            @if($employee->skills && count($employee->skills) > 0)
                                <div class="flex flex-wrap gap-1 mt-2">
                                    @foreach(array_slice($employee->skills, 0, 3) as $skill)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                            {{ $skill }}
                                        </span>
                                    @endforeach
                                    @if(count($employee->skills) > 3)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                            +{{ count($employee->skills) - 3 }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 grid grid-cols-3 gap-2 text-center">
                        <div>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $employee->efficiency ?? 0 }}%</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Efficiency</p>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $employee->completed_tasks ?? 0 }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Completed</p>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $employee->years_experience ?? 0 }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Years</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                        <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                            <i class="fa-solid fa-users text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400">No employees assigned yet</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.employee-card').forEach(card => {
                const name = card.dataset.name;
                const skills = card.dataset.skills;
                if (name.includes(searchTerm) || skills.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
    @endpush
</x-layouts.general-manager>
