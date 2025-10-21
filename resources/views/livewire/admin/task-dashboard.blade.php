<div wire:poll.5s>
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="flex items-center justify-between px-8 py-4">
            <h2 class="text-2xl font-bold text-gray-800">Task Management</h2>
            <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-plus mr-2"></i>New Task
            </button>
        </div>
    </header>

    <div class="p-8">
        <!-- Success/Error Messages -->
        @if (session()->has('message'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p class="font-bold">Success</p>
                <p>{{ session('message') }}</p>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p class="font-bold">Error</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <!-- Cinema-Style Booking Interface Section -->
        <section class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4">New Job Creation</h3>
            
            <!-- Client Selection and Date Picker -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Contracted Client</label>
                    <select wire:model="selectedClientId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($contractedClients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Service Date</label>
                    <input type="date" wire:model="serviceDate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Cinema-Style Cabin Selection Grid -->
            <div class="mb-6">
                <h4 class="text-lg font-semibold text-gray-700 mb-3">Select Cabins/Units</h4>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach($locations as $location)
                        <button 
                            wire:click="toggleLocation({{ $location->id }})"
                            class="{{ in_array($location->id, $selectedLocations) ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-blue-500 hover:text-white text-gray-700' }} font-medium py-3 px-2 rounded-lg transition text-sm">
                            {{ $location->location_name }}
                        </button>
                    @endforeach
                </div>
            </div>

        <!-- Your "Optimize & Assign Tasks" Button -->
        <div class="flex justify-center">
            <button wire:click="optimizeAndAssign" wire:loading.attr="disabled" class="bg-green-600 text-white font-bold px-8 py-3 rounded-lg hover:bg-green-700 transition text-lg disabled:opacity-50">
                <i class="fas fa-magic mr-2"></i>
                <span wire:loading.remove>Optimize & Assign Tasks</span>
                <span wire:loading>Optimizing...</span>
            </button>
        </div>

<!-- Add this loading indicator block -->
<div wire:loading wire:target="optimizeAndAssign" class="w-full text-center my-4">
    <div class="flex justify-center items-center bg-indigo-100 border border-indigo-300 text-indigo-700 px-4 py-3 rounded-lg" role="alert">
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Running Optimization Algorithm... Please wait.</span>
    </div>
</div>

        </section>

        <!-- Kanban Boards Section -->
        <section class="mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Task Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- To Do Column -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-bold text-gray-700 mb-4 flex items-center">
                        <span class="bg-yellow-400 w-3 h-3 rounded-full mr-2"></span>
                        To Do
                    </h4>
                    <div class="space-y-3">
                        @forelse($tasks->where('status', 'Scheduled') as $task)
                            <div class="bg-white rounded-lg p-4 shadow-sm border-l-4 {{ $task->arrival_status ? 'border-red-500' : 'border-yellow-500' }}">
                                <div class="flex items-start justify-between mb-2">
                                    <h5 class="font-semibold text-gray-800 flex-1">{{ $task->task_description }}</h5>
                                    @if($task->arrival_status)
                                        <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 font-semibold ml-2 flex items-center gap-1">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            URGENT
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 mb-2">{{ $task->location->location_name ?? 'External Client' }}</p>
                                @if($task->optimizationTeam && $task->optimizationTeam->members->isNotEmpty())
                                    <div class="flex items-center gap-2 mt-2">
                                        <div class="flex -space-x-2">
                                            @foreach($task->optimizationTeam->members->take(3) as $member)
                                                <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 border-2 border-white flex items-center justify-center text-white text-xs font-semibold" title="{{ $member->employee->full_name }}">
                                                    {{ substr($member->employee->full_name, 0, 1) }}
                                                </div>
                                            @endforeach
                                            @if($task->optimizationTeam->members->count() > 3)
                                                <div class="w-6 h-6 rounded-full bg-gray-400 border-2 border-white flex items-center justify-center text-white text-xs font-semibold">
                                                    +{{ $task->optimizationTeam->members->count() - 3 }}
                                                </div>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500">{{ $task->optimizationTeam->members->pluck('employee.full_name')->join(', ') }}</p>
                                    </div>
                                @else
                                    <p class="text-xs text-gray-400 italic">No team assigned</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No tasks are scheduled.</p>
                        @endforelse
                    </div>
                </div>

                <!-- In Progress Column -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-bold text-gray-700 mb-4 flex items-center">
                        <span class="bg-blue-400 w-3 h-3 rounded-full mr-2"></span>
                        In Progress
                    </h4>
                    <div class="space-y-3">
                        @forelse($tasks->where('status', 'In Progress') as $task)
                            <div class="bg-white rounded-lg p-4 shadow-sm border-l-4 {{ $task->arrival_status ? 'border-red-500' : 'border-blue-500' }}">
                                <div class="flex items-start justify-between mb-2">
                                    <h5 class="font-semibold text-gray-800 flex-1">{{ $task->task_description }}</h5>
                                    @if($task->arrival_status)
                                        <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 font-semibold ml-2 flex items-center gap-1">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            URGENT
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 mb-2">{{ $task->location->location_name ?? 'External Client' }}</p>
                                @if($task->optimizationTeam && $task->optimizationTeam->members->isNotEmpty())
                                    <div class="flex items-center gap-2 mt-2">
                                        <div class="flex -space-x-2">
                                            @foreach($task->optimizationTeam->members->take(3) as $member)
                                                <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 border-2 border-white flex items-center justify-center text-white text-xs font-semibold" title="{{ $member->employee->full_name }}">
                                                    {{ substr($member->employee->full_name, 0, 1) }}
                                                </div>
                                            @endforeach
                                            @if($task->optimizationTeam->members->count() > 3)
                                                <div class="w-6 h-6 rounded-full bg-gray-400 border-2 border-white flex items-center justify-center text-white text-xs font-semibold">
                                                    +{{ $task->optimizationTeam->members->count() - 3 }}
                                                </div>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500">{{ $task->optimizationTeam->members->pluck('employee.full_name')->join(', ') }}</p>
                                    </div>
                                @else
                                    <p class="text-xs text-gray-400 italic">No team assigned</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No tasks are in progress.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Completed Column -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-bold text-gray-700 mb-4 flex items-center">
                        <span class="bg-green-400 w-3 h-3 rounded-full mr-2"></span>
                        Completed
                    </h4>
                    <div class="space-y-3">
                        @forelse($tasks->where('status', 'Completed') as $task)
                            <div class="bg-white rounded-lg p-4 shadow-sm border-l-4 border-green-500 opacity-75">
                                <div class="flex items-start justify-between mb-2">
                                    <h5 class="font-semibold text-gray-800 flex-1">{{ $task->task_description }}</h5>
                                    @if($task->arrival_status)
                                        <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 font-semibold ml-2 flex items-center gap-1 opacity-60">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            URGENT
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 mb-2">{{ $task->location->location_name ?? 'External Client' }}</p>
                                @if($task->optimizationTeam && $task->optimizationTeam->members->isNotEmpty())
                                    <div class="flex items-center gap-2 mt-2">
                                        <div class="flex -space-x-2">
                                            @foreach($task->optimizationTeam->members->take(3) as $member)
                                                <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 border-2 border-white flex items-center justify-center text-white text-xs font-semibold" title="{{ $member->employee->full_name }}">
                                                    {{ substr($member->employee->full_name, 0, 1) }}
                                                </div>
                                            @endforeach
                                            @if($task->optimizationTeam->members->count() > 3)
                                                <div class="w-6 h-6 rounded-full bg-gray-400 border-2 border-white flex items-center justify-center text-white text-xs font-semibold">
                                                    +{{ $task->optimizationTeam->members->count() - 3 }}
                                                </div>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500">{{ $task->optimizationTeam->members->pluck('employee.full_name')->join(', ') }}</p>
                                    </div>
                                @else
                                    <p class="text-xs text-gray-400 italic">No team assigned</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No tasks have been completed.</p>
                        @endforelse
                    </div>
                </div>
                
            </div>
        </section>
    </div>
</div>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>OptiCrew</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Livewire Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
            </main>
        </div>
        
        <!-- Livewire Scripts -->
        @livewireScripts
    </body>
</html>