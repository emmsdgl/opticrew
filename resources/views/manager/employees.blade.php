<x-layouts.general-manager :title="'Team Members'">
    <div class="flex flex-col gap-6 w-full" x-data="employeesView()">
        

        @php
            $totalEmployees = $employees->count();
            $activeCount = $employees->filter(fn($e) => $e->is_active)->count();
            $efficiencyValues = $employees->pluck('efficiency')->filter(fn($v) => is_numeric($v));
            $overallEfficiency = $efficiencyValues->count() > 0 ? (int) round($efficiencyValues->avg()) : 0;
        @endphp

        <!-- Stat Cards -->
        <div class="px-4">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-px bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
                {{-- Total Employees --}}
                <div class="bg-white dark:bg-slate-900 px-6 py-5">
                    <div class="flex items-center gap-2 mb-2 ml-3">
                        <i class="fa-solid fa-users" style="color: #3b82f6"></i>
                        <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Total Employees</p>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $totalEmployees }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 ml-3">Assigned to your tasks</p>
                </div>

                {{-- Overall Efficiency --}}
                <div class="bg-white dark:bg-slate-900 px-6 py-5">
                    <div class="flex items-center gap-2 mb-2 ml-3">
                        <i class="fa-solid fa-gauge-high" style="color: #10b981"></i>
                        <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Overall Efficiency</p>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $overallEfficiency }}%</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 ml-3">Across the team</p>
                </div>

                {{-- Active Employees --}}
                <div class="bg-white dark:bg-slate-900 px-6 py-5">
                    <div class="flex items-center gap-2 mb-2 ml-3">
                        <i class="fa-solid fa-user-check" style="color: #f59e0b"></i>
                        <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Active Employees</p>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $activeCount }}</p>
                    @php $inactiveCount = $totalEmployees - $activeCount; @endphp
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 ml-3">
                        @if($inactiveCount > 0)
                            <span class="text-amber-500 font-semibold">{{ $inactiveCount }}</span> inactive
                        @else
                            All employees active
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div>
        <div class="bg-white dark:bg-transparent rounded-xl border border-gray-200 dark:border-none p-4">
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1 relative">
                    <input type="search"
                           id="searchInput"
                           x-model="search"
                           @input="applyFilters()"
                           placeholder="Search by name or skills..."
                           class="w-full px-4 py-2 pl-10 pr-4 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-800 dark:text-white">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <!-- Filter + View Toggle -->
                <div class="flex gap-2">
                    <!-- Filter by Status Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open"
                                class="inline-flex items-center gap-2 h-[38px] px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-filter text-xs"></i>
                            <span class="text-xs" x-text="status === '' ? 'Filter by Status' : (status.charAt(0).toUpperCase() + status.slice(1))"></span>
                            <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition x-cloak
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <button type="button" @click="status = ''; applyFilters(); open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        :class="status === '' ? 'font-semibold bg-gray-50 dark:bg-gray-700/50' : ''">
                                    All Status
                                </button>
                                <button type="button" @click="status = 'active'; applyFilters(); open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        :class="status === 'active' ? 'font-semibold bg-gray-50 dark:bg-gray-700/50' : ''">
                                    Active
                                </button>
                                <button type="button" @click="status = 'inactive'; applyFilters(); open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        :class="status === 'inactive' ? 'font-semibold bg-gray-50 dark:bg-gray-700/50' : ''">
                                    Inactive
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sort Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open"
                                class="inline-flex items-center gap-2 h-[38px] px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-sort-amount-down text-xs"></i>
                            <span class="text-xs" x-text="sortLabel"></span>
                            <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition x-cloak
                             class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <button type="button" @click="sort = ''; applyFilters(); open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        :class="sort === '' ? 'font-semibold bg-gray-50 dark:bg-gray-700/50' : ''">
                                    Default
                                </button>
                                <button type="button" @click="sort = 'name-asc'; applyFilters(); open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        :class="sort === 'name-asc' ? 'font-semibold bg-gray-50 dark:bg-gray-700/50' : ''">
                                    Name (A &ndash; Z)
                                </button>
                                <button type="button" @click="sort = 'name-desc'; applyFilters(); open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        :class="sort === 'name-desc' ? 'font-semibold bg-gray-50 dark:bg-gray-700/50' : ''">
                                    Name (Z &ndash; A)
                                </button>
                                <button type="button" @click="sort = 'efficiency-desc'; applyFilters(); open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        :class="sort === 'efficiency-desc' ? 'font-semibold bg-gray-50 dark:bg-gray-700/50' : ''">
                                    Efficiency (High &rarr; Low)
                                </button>
                                <button type="button" @click="sort = 'efficiency-asc'; applyFilters(); open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        :class="sort === 'efficiency-asc' ? 'font-semibold bg-gray-50 dark:bg-gray-700/50' : ''">
                                    Efficiency (Low &rarr; High)
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- View Toggle -->
                    <div class="inline-flex h-[38px] rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden bg-white dark:bg-gray-700"
                         role="group" aria-label="View mode">
                        <button type="button" @click="setView('grid')"
                                :class="viewMode === 'grid'
                                    ? 'bg-blue-600 text-white'
                                    : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600'"
                                class="px-3 py-2 text-sm font-medium transition-colors flex items-center gap-1.5"
                                aria-label="Card view"
                                :aria-pressed="viewMode === 'grid'">
                            <i class="fa-solid fa-table-cells-large text-xs"></i>
                        </button>
                        <button type="button" @click="setView('list')"
                                :class="viewMode === 'list'
                                    ? 'bg-blue-600 text-white'
                                    : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600'"
                                class="px-3 py-2 text-sm font-medium transition-colors flex items-center gap-1.5 border-l border-gray-300 dark:border-gray-600"
                                aria-label="List view"
                                :aria-pressed="viewMode === 'list'">
                            <i class="fa-solid fa-list text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        </div>

        @php
            $hasEmployees = $employees->count() > 0;
        @endphp

        @if($hasEmployees)
            <!-- Table Label -->
            <div class="px-4">
                <x-labelwithvalue label="Team Members" count="({{ $employees->count() }})" />
            </div>

            <!-- Card View -->
            <div x-show="viewMode === 'grid'"
                 class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 px-4" id="employeeGrid">
                @foreach($employees as $employee)
                    @php
                        $skillsList = is_array($employee->skills) ? $employee->skills : [];
                        $skillsString = strtolower(implode(',', $skillsList));
                        $nameLower = strtolower($employee->user->name ?? '');
                        $statusLabel = $employee->is_active ? 'active' : 'inactive';
                        $firstInitial = strtoupper(substr($employee->user->name ?? 'U', 0, 1));
                        $lastInitial = strtoupper(substr(explode(' ', $employee->user->name ?? 'U')[1] ?? '', 0, 1));
                    @endphp
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition-shadow duration-200 employee-card"
                         data-name="{{ $nameLower }}"
                         data-skills="{{ $skillsString }}"
                         data-status="{{ $statusLabel }}"
                         data-efficiency="{{ (int) ($employee->efficiency ?? 0) }}">
                        <div class="flex items-start gap-4">
                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 font-semibold text-lg">
                                    {{ $firstInitial }}{{ $lastInitial }}
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $employee->user->name ?? 'Unknown' }}
                                    </h3>
                                    <span class="flex-shrink-0 w-2 h-2 rounded-full {{ $employee->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ $employee->user->email ?? '' }}
                                </p>

                                @if(count($skillsList) > 0)
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        @foreach(array_slice($skillsList, 0, 3) as $skill)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                                {{ $skill }}
                                            </span>
                                        @endforeach
                                        @if(count($skillsList) > 3)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                                +{{ count($skillsList) - 3 }}
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
                @endforeach
            </div>

            <!-- List View -->
            <div x-show="viewMode === 'list'" class="px-4">
                <div class="rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                        <thead>
                            <tr>
                                <th scope="col" class="px-4 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Name</th>
                                <th scope="col" class="px-4 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white hidden md:table-cell">Email</th>
                                <th scope="col" class="px-4 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white hidden lg:table-cell">Skills</th>
                                <th scope="col" class="px-4 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Status</th>
                                <th scope="col" class="px-4 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">Efficiency</th>
                                <th scope="col" class="px-4 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white hidden sm:table-cell">Completed</th>
                                <th scope="col" class="px-4 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white hidden sm:table-cell">Years</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                @php
                                    $skillsList = is_array($employee->skills) ? $employee->skills : [];
                                    $skillsString = strtolower(implode(',', $skillsList));
                                    $nameLower = strtolower($employee->user->name ?? '');
                                    $statusLabel = $employee->is_active ? 'active' : 'inactive';
                                    $firstInitial = strtoupper(substr($employee->user->name ?? 'U', 0, 1));
                                    $lastInitial = strtoupper(substr(explode(' ', $employee->user->name ?? 'U')[1] ?? '', 0, 1));
                                @endphp
                                <tr class="employee-row odd:bg-white even:bg-gray-50 dark:odd:bg-gray-800 dark:even:bg-gray-900/40 transition-colors"
                                    data-name="{{ $nameLower }}"
                                    data-skills="{{ $skillsString }}"
                                    data-status="{{ $statusLabel }}"
                                    data-efficiency="{{ (int) ($employee->efficiency ?? 0) }}">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 flex-shrink-0 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 font-semibold text-sm">
                                                {{ $firstInitial }}{{ $lastInitial }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                                    {{ $employee->user->name ?? 'Unknown' }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate md:hidden">
                                                    {{ $employee->user->email ?? '' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400 hidden md:table-cell">
                                        {{ $employee->user->email ?? '' }}
                                    </td>
                                    <td class="px-4 py-4 hidden lg:table-cell">
                                        @if(count($skillsList) > 0)
                                            <div class="flex flex-wrap gap-1 max-w-xs">
                                                @foreach(array_slice($skillsList, 0, 2) as $skill)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                                        {{ $skill }}
                                                    </span>
                                                @endforeach
                                                @if(count($skillsList) > 2)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                                        +{{ count($skillsList) - 2 }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 dark:text-gray-500">&mdash;</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if($employee->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                        {{ $employee->efficiency ?? 0 }}%
                                    </td>
                                    <td class="px-4 py-4 text-right text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap hidden sm:table-cell">
                                        {{ $employee->completed_tasks ?? 0 }}
                                    </td>
                                    <td class="px-4 py-4 text-right text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap hidden sm:table-cell">
                                        {{ $employee->years_experience ?? 0 }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div x-show="visibleCount > perPage"
                 class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Showing <span x-text="rangeStart"></span>&ndash;<span x-text="rangeEnd"></span> of <span x-text="visibleCount"></span>
                </p>
                <div class="flex items-center gap-1">
                    <button @click="setPage(currentPage - 1)" :disabled="currentPage === 1"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                        Previous
                    </button>
                    <template x-for="page in visiblePages" :key="page">
                        <button @click="setPage(page)"
                                class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors"
                                :class="currentPage === page ? 'bg-blue-600 text-white' : 'border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                x-text="page"></button>
                    </template>
                    <button @click="setPage(currentPage + 1)" :disabled="currentPage >= totalPages"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                        Next
                    </button>
                </div>
            </div>

            <!-- No-results state (shown when filters hide everything) -->
            <div id="noResultsState" x-show="noResults"
                 class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                    <i class="fa-solid fa-magnifying-glass text-2xl text-gray-400"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400">No employees match the current filters.</p>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                    <i class="fa-solid fa-users text-2xl text-gray-400"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400">No employees assigned yet</p>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function employeesView() {
            return {
                viewMode: localStorage.getItem('managerEmployeesView') || 'grid',
                search: '',
                status: '',
                sort: '',
                noResults: false,
                perPage: 5,
                currentPage: 1,
                visibleCount: 0,

                get sortLabel() {
                    switch (this.sort) {
                        case 'name-asc': return 'Name (A–Z)';
                        case 'name-desc': return 'Name (Z–A)';
                        case 'efficiency-desc': return 'Efficiency: High → Low';
                        case 'efficiency-asc': return 'Efficiency: Low → High';
                        default: return 'Sort By';
                    }
                },

                init() {
                    this.applyFilters();
                },

                setView(mode) {
                    this.viewMode = mode;
                    try { localStorage.setItem('managerEmployeesView', mode); } catch (e) {}
                },

                applyFilters() {
                    const term = (this.search || '').toLowerCase().trim();
                    const status = this.status || '';
                    document.querySelectorAll('.employee-card, .employee-row').forEach(el => {
                        const name = el.dataset.name || '';
                        const skills = el.dataset.skills || '';
                        const elStatus = el.dataset.status || '';
                        const matchesSearch = !term || name.includes(term) || skills.includes(term);
                        const matchesStatus = !status || elStatus === status;
                        el.dataset.matches = (matchesSearch && matchesStatus) ? '1' : '0';
                    });
                    // Count from the cards only — every employee renders one card and one row,
                    // so the cards alone give the true logical count.
                    const cards = document.querySelectorAll('.employee-card');
                    this.visibleCount = Array.from(cards).filter(el => el.dataset.matches === '1').length;
                    this.currentPage = 1;
                    this.noResults = cards.length > 0 && this.visibleCount === 0;
                    this.applySort();
                    this.applyPagination();
                },

                applySort() {
                    const sortBy = this.sort;
                    if (!sortBy) return;
                    const cmp = (a, b) => {
                        switch (sortBy) {
                            case 'name-asc':
                                return (a.dataset.name || '').localeCompare(b.dataset.name || '');
                            case 'name-desc':
                                return (b.dataset.name || '').localeCompare(a.dataset.name || '');
                            case 'efficiency-desc':
                                return (parseInt(b.dataset.efficiency) || 0) - (parseInt(a.dataset.efficiency) || 0);
                            case 'efficiency-asc':
                                return (parseInt(a.dataset.efficiency) || 0) - (parseInt(b.dataset.efficiency) || 0);
                        }
                        return 0;
                    };
                    ['.employee-card', '.employee-row'].forEach(sel => {
                        const items = Array.from(document.querySelectorAll(sel));
                        if (items.length === 0) return;
                        const parent = items[0].parentElement;
                        items.sort(cmp).forEach(el => parent.appendChild(el));
                    });
                },

                setPage(page) {
                    const total = this.totalPages;
                    this.currentPage = Math.max(1, Math.min(total, page));
                    this.applyPagination();
                },

                applyPagination() {
                    const start = (this.currentPage - 1) * this.perPage;
                    const end = start + this.perPage;
                    ['.employee-card', '.employee-row'].forEach(sel => {
                        const matchedEls = Array.from(document.querySelectorAll(sel))
                            .filter(el => el.dataset.matches !== '0');
                        matchedEls.forEach((el, i) => {
                            el.style.display = (i >= start && i < end) ? '' : 'none';
                        });
                        // Hide non-matched
                        document.querySelectorAll(sel).forEach(el => {
                            if (el.dataset.matches === '0') el.style.display = 'none';
                        });
                    });
                },

                get totalPages() {
                    return Math.max(1, Math.ceil(this.visibleCount / this.perPage));
                },

                get visiblePages() {
                    const total = this.totalPages;
                    const w = 5;
                    if (total < w + 1) {
                        return Array.from({ length: total }, (_, i) => i + 1);
                    }
                    let start = Math.max(1, this.currentPage - 2);
                    let end = start + w - 1;
                    if (end > total) {
                        end = total;
                        start = end - w + 1;
                    }
                    return Array.from({ length: end - start + 1 }, (_, i) => start + i);
                },

                get rangeStart() {
                    return this.visibleCount === 0 ? 0 : (this.currentPage - 1) * this.perPage + 1;
                },

                get rangeEnd() {
                    return Math.min(this.currentPage * this.perPage, this.visibleCount);
                }
            };
        }
    </script>
    @endpush
</x-layouts.general-manager>
