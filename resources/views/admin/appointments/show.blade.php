<x-layouts.general-employer :title="'Appointment Details'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]" x-data="appointmentDetail()">
        <div>
            <a href="{{ route('admin.appointments.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white mb-2 inline-flex items-center">
                <i class="fi fi-rr-angle-left mr-1"></i> Back to Appointments
            </a>
        </div>
        <!-- Header -->
        <div class="flex flex-row items-center justify-between gap-12">
            
                <!-- Status Badge -->
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Appointment No. {{ $appointment->id }}</h1>
                @if($appointment->status === 'pending')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400">
                        Pending Review
                    </span>
                @elseif($appointment->status === 'approved')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                        Approved
                    </span>
                @elseif($appointment->status === 'rejected')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                        Rejected
                    </span>
                @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Client Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    @if($appointment->is_company_inquiry)
                        <!-- Company Inquiry Information -->
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fi fi-rr-building mr-2"></i> Company Information
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Company Name</p>
                                <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $appointment->client->company_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Contact Person</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->client->first_name }} {{ $appointment->client->last_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Business ID</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->client->business_id }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->client->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->client->phone_number }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">E-Invoice Number</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->client->einvoice_number }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Address</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->client->address }}</p>
                            </div>
                        </div>
                    @else
                        <!-- Personal Client Information -->
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fi fi-rr-user mr-2"></i> Client Information
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Name</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->client->first_name }} {{ $appointment->client->last_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Booking Type</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white capitalize">{{ $appointment->booking_type }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->client->user->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->client->user->phone }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Address</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->client->address }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Service Details -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    @if($appointment->is_company_inquiry)
                        <!-- Company Service Inquiry -->
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fi fi-rr-document mr-2"></i> Service Inquiry Details
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Requested Services</p>
                                <div class="flex flex-wrap gap-2">
                                    @php
                                        $serviceTypes = $appointment->company_service_types;
                                        if (is_string($serviceTypes)) {
                                            $serviceTypes = json_decode($serviceTypes, true);
                                        }
                                    @endphp
                                    @if($serviceTypes && is_array($serviceTypes))
                                        @foreach($serviceTypes as $serviceType)
                                            <span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 rounded-full text-xs font-medium">
                                                {{ $serviceType }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">No services specified</span>
                                    @endif
                                </div>
                            </div>
                            @if($appointment->other_concerns)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Additional Information / Other Concerns</p>
                                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $appointment->other_concerns }}</p>
                                </div>
                            </div>
                            @endif
                            <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                <p class="text-sm text-yellow-800 dark:text-yellow-400">
                                    <i class="fi fi-rr-info-circle mr-1"></i>
                                    This is a service inquiry. A custom quotation needs to be prepared and sent to the client via email.
                                </p>
                            </div>
                        </div>
                    @else
                        <!-- Personal Client Service Details -->
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fi fi-rr-broom mr-2"></i> Service Details
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Service Type</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->service_type }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Number of Units</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->number_of_units }} unit(s)</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Service Date</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $appointment->service_date->format('l, F d, Y') }}
                                    @if($appointment->is_sunday)
                                        <span class="ml-2 text-xs text-orange-600 dark:text-orange-400 font-semibold">(Sunday)</span>
                                    @endif
                                    @if($appointment->is_holiday)
                                        <span class="ml-2 text-xs text-orange-600 dark:text-orange-400 font-semibold">(Holiday)</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Service Time</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($appointment->service_time)->format('g:i A') }}</p>
                            </div>
                        </div>

                        <!-- Unit Details -->
                        <div class="mt-6">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Unit Details</h4>
                            @if($appointment->unit_details && is_array($appointment->unit_details) && count($appointment->unit_details) > 0)
                                <div class="space-y-3">
                                    @foreach($appointment->unit_details as $index => $unit)
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                                                    Unit {{ $index + 1 }}
                                                </div>
                                                @if(isset($unit['price']))
                                                <div class="text-right">
                                                    <div class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                                        €{{ number_format($unit['price'], 2) }}
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="grid grid-cols-2 gap-3 text-sm">
                                                <div>
                                                    <span class="text-gray-600 dark:text-gray-400">Name:</span>
                                                    <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $unit['name'] ?? '-' }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-600 dark:text-gray-400">Size:</span>
                                                    <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $unit['size'] ?? '-' }} m²</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <!-- Fallback to old single unit display -->
                                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-gray-600 dark:text-gray-400">Cabin/Unit Name:</span>
                                            <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $appointment->cabin_name }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 dark:text-gray-400">Unit Size:</span>
                                            <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $appointment->unit_size }} m²</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($appointment->special_requests)
                        <div class="mt-6">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Special Requests</p>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->special_requests }}</p>
                            </div>
                        </div>
                        @endif
                    @endif
                </div>

                <!-- Service Checklist -->
                @if(!$appointment->is_company_inquiry)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fi fi-rr-list-check mr-2"></i> Service Checklist
                    </h3>

                    @php
                        // Checklist templates organized by service type
                        $checklistTemplates = [
                            'daily_cleaning' => [
                                'Sweep and mop floors',
                                'Vacuum carpets/rugs',
                                'Dust furniture and surfaces',
                                'Wipe tables and countertops',
                                'Empty trash bins',
                                'Wipe kitchen counters',
                                'Clean sink',
                                'Wash visible dishes',
                                'Wipe appliance exteriors',
                                'Clean toilet and sink',
                                'Wipe mirrors',
                                'Mop floor',
                                'Organize cluttered areas',
                                'Light deodorizing',
                            ],
                            'snowout_cleaning' => [
                                'Remove mud, water, and debris',
                                'Clean door mats',
                                'Mop and dry floors',
                                'Deep vacuum carpets',
                                'Mop with disinfectant solution',
                                'Wipe walls near entrances',
                                'Dry wet surfaces',
                                'Check for water accumulation',
                                'Clean and sanitize affected areas',
                                'Dispose of tracked-in debris',
                                'Replace trash liners',
                            ],
                            'deep_cleaning' => [
                                'Dust high and low areas (vents, corners, baseboards)',
                                'Clean behind and under furniture',
                                'Wash walls and remove stains',
                                'Deep vacuum carpets',
                                'Clean inside microwave',
                                'Degrease stove and range hood',
                                'Clean inside refrigerator (if included)',
                                'Scrub tile grout',
                                'Remove limescale and mold buildup',
                                'Deep scrub tiles and grout',
                                'Sanitize all fixtures thoroughly',
                                'Clean window interiors',
                                'Polish handles and knobs',
                                'Disinfect frequently touched surfaces',
                            ],
                            'general_cleaning' => [
                                'Dust surfaces',
                                'Sweep/vacuum floors',
                                'Mop hard floors',
                                'Clean glass and mirrors',
                                'Wipe countertops',
                                'Clean sink',
                                'Take out trash',
                                'Clean toilet, sink, and mirror',
                                'Mop floor',
                                'Arrange items neatly',
                                'Dispose of garbage',
                                'Light air freshening',
                            ],
                            'hotel_cleaning' => [
                                'Make bed with fresh linens',
                                'Replace pillowcases and sheets',
                                'Dust all surfaces (tables, headboard, shelves)',
                                'Vacuum carpet / sweep & mop floor',
                                'Clean mirrors and glass surfaces',
                                'Check under bed for trash/items',
                                'Empty trash bins and replace liners',
                                'Clean and disinfect toilet',
                                'Scrub shower walls, tub, and floor',
                                'Clean sink and countertop',
                                'Polish fixtures',
                                'Replace towels, bath mat, tissue, and toiletries',
                                'Mop bathroom floor',
                                'Refill water, coffee, and room amenities',
                                'Replace slippers and hygiene kits',
                                'Check minibar (if applicable)',
                                'Ensure lights, AC, TV working',
                                'Arrange curtains neatly',
                                'Deodorize room',
                            ],
                        ];

                        // Determine service type from appointment service_type
                        $serviceTypeRaw = strtolower($appointment->service_type ?? '');
                        $serviceType = 'general_cleaning'; // Default

                        if (str_contains($serviceTypeRaw, 'daily') || str_contains($serviceTypeRaw, 'routine')) {
                            $serviceType = 'daily_cleaning';
                        } elseif (str_contains($serviceTypeRaw, 'snowout') || str_contains($serviceTypeRaw, 'weather')) {
                            $serviceType = 'snowout_cleaning';
                        } elseif (str_contains($serviceTypeRaw, 'deep')) {
                            $serviceType = 'deep_cleaning';
                        } elseif (str_contains($serviceTypeRaw, 'hotel') || str_contains($serviceTypeRaw, 'room turnover')) {
                            $serviceType = 'hotel_cleaning';
                        }

                        $checklistItems = $checklistTemplates[$serviceType] ?? $checklistTemplates['general_cleaning'];
                    @endphp

                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                        This checklist shows the tasks to be completed by the assigned employee (read-only view).
                    </p>
                    <div class="space-y-2">
                        @forelse($checklistItems as $index => $item)
                            <div class="flex items-start gap-3 p-2 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                <!-- Read-only status icon -->
                                <div class="flex items-center h-5 mt-0.5">
                                    <div class="w-4 h-4 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
                                        <i class="fas fa-circle text-gray-400 dark:text-gray-500 text-[5px]"></i>
                                    </div>
                                </div>
                                <span class="flex-1 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $item }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <i class="fas fa-clipboard-list text-3xl text-gray-300 dark:text-gray-600 mb-2"></i>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">No checklist items</p>
                            </div>
                        @endforelse
                    </div>

                    @if(count($checklistItems) > 0)
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Checklist Items</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ count($checklistItems) }} items total
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                <i class="fi fi-rr-info-circle mr-1"></i>
                                Progress will be tracked when the employee starts the task.
                            </p>
                        </div>
                    @endif
                </div>
                @endif

                <!-- Team Assignment (Only show if approved) -->
                @if($appointment->status === 'approved' && !$appointment->assigned_team_id)
                <div class="rounded-lg p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fi fi-rr-users-alt mr-2"></i> Team Assignment
                    </h3>

                    @if($availableTeams && $availableTeams->count() > 0)
                        <!-- Scenario 1: Teams exist for this date - Show team dropdown -->

                        <!-- Recommended Team -->
                        @if($appointment->recommendedTeam)
                        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i class="fi fi-rr-star text-green-600 dark:text-green-400 mr-2"></i>
                                <span class="text-sm font-semibold text-green-800 dark:text-green-400">Recommended Team</span>
                            </div>
                            <p class="text-sm text-gray-900 dark:text-white">
                                Team #{{ $appointment->recommendedTeam->id }}:
                                @foreach($appointment->recommendedTeam->employees as $employee)
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                    @if($employee->has_driving_license)
                                        <span class="text-blue-600 dark:text-blue-400">(Driver)</span>
                                    @endif
                                    @if(!$loop->last), @endif
                                @endforeach
                            </p>
                        </div>
                        @endif

                        <!-- Team Dropdown -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Select Team to Assign
                            </label>
                            <select x-model="selectedTeamId" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Choose a team --</option>
                                @foreach($availableTeams as $team)
                                    <option value="{{ $team['id'] }}" {{ $appointment->recommended_team_id == $team['id'] ? 'selected' : '' }}>
                                        {{ $team['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button @click="assignExistingTeam()" :disabled="!selectedTeamId || assigning"
                            class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!assigning">Confirm Team Assignment</span>
                            <span x-show="assigning">Assigning...</span>
                        </button>

                    @else
                        <!-- Scenario 2: No teams exist - Create team via optimization -->
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg mb-4">
                            <div class="flex items-start">
                                <i class="fi fi-rr-info-circle text-blue-600 dark:text-blue-400 mt-0.5 mr-2"></i>
                                <div>
                                    <p class="text-sm font-medium text-blue-800 dark:text-blue-400 mb-1">No teams found for this date</p>
                                    <p class="text-xs text-gray-700 dark:text-gray-300">The system will run optimization to create teams and assign the best employees for this task.</p>
                                </div>
                            </div>
                        </div>

                        <button @click="createTeamViaOptimization()" :disabled="assigning"
                            class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!assigning">Assign Team</span>
                            <span x-show="assigning">Creating teams via optimization...</span>
                        </button>

                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 text-center">
                            This will analyze available employees and create optimal teams for {{ \Carbon\Carbon::parse($appointment->service_date)->format('M d, Y') }}
                        </p>
                    @endif

                    @if($appointment->assignedTeam)
                    <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded">
                        <p class="text-sm text-blue-800 dark:text-blue-400">
                            <i class="fi fi-rr-check-circle mr-1"></i>
                            Currently assigned to Team #{{ $appointment->assignedTeam->id }}
                        </p>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Rejection Reason (if rejected) -->
                @if($appointment->status === 'rejected' && $appointment->rejection_reason)
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg shadow p-6 border border-red-200 dark:border-red-800">
                    <h3 class="text-lg font-semibold text-red-800 dark:text-red-400 mb-2 flex items-center">
                        <i class="fi fi-rr-cross-circle mr-2"></i> Rejection Reason
                    </h3>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $appointment->rejection_reason }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        Rejected by {{ $appointment->rejectedBy->name }} on {{ $appointment->rejected_at->format('M d, Y H:i') }}
                    </p>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Pricing Summary (Only for personal bookings) -->
                @if(!$appointment->is_company_inquiry)
                <div class="rounded-lg p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fi fi-rr-receipt mr-2"></i> Pricing
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Subtotal</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">€{{ number_format($appointment->quotation, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">VAT (24%)</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">€{{ number_format($appointment->vat_amount, 2) }}</span>
                        </div>
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700 flex justify-between">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">Total</span>
                            <span class="text-sm font-bold text-blue-600 dark:text-blue-400">€{{ number_format($appointment->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
                @else
                <!-- Quotation Notice for Company Inquiries -->
                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg shadow p-6 border border-purple-200 dark:border-purple-800">
                    <h3 class="text-lg font-semibold text-purple-900 dark:text-purple-400 mb-2 flex items-center">
                        <i class="fi fi-rr-file-invoice mr-2"></i> Quotation Required
                    </h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        This is a service inquiry from a company. Please prepare a custom quotation based on their requirements and send it to their email address.
                    </p>
                    <div class="mt-4 p-3 bg-white dark:bg-gray-800 rounded border border-purple-200 dark:border-purple-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Contact Email</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->client->email }}</p>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                @if($appointment->status === 'pending')
                <div class="rounded-lg p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                    <div class="space-y-3">
                        <button @click="approveAppointment()" :disabled="approving"
                            class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fi fi-rr-check mr-2"></i>
                            <span class="text-sm" x-show="!approving">Approve Appointment</span>
                            <span class="text-sm" x-show="approving">Approving...</span>
                        </button>

                        <button @click="showRejectModal = true"
                            class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors text-sm">
                            <i class="fi fi-rr-cross mr-2"></i> Reject Appointment
                        </button>
                    </div>
                </div>
                @endif

                <!-- Timestamps -->
                <div class="rounded-lg p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Timeline</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Submitted</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        @if($appointment->approved_at)
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Approved</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->approved_at->format('M d, Y H:i') }}</p>
                        </div>
                        @endif
                        @if($appointment->rejected_at)
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Rejected</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->rejected_at->format('M d, Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div x-show="showRejectModal"
             x-cloak
             @click.away="showRejectModal = false"
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50"></div>

                <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Reject Appointment</h3>

                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Please provide a reason for rejection. This will be sent to the client.
                    </p>

                    <textarea x-model="rejectionReason"
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500"
                        placeholder="Enter rejection reason..."></textarea>

                    <div class="flex gap-3 mt-4">
                        <button @click="showRejectModal = false"
                            class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            Cancel
                        </button>
                        <button @click="rejectAppointment()" :disabled="!rejectionReason || rejecting"
                            class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!rejecting">Confirm Rejection</span>
                            <span x-show="rejecting">Rejecting...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function appointmentDetail() {
            return {
                approving: false,
                rejecting: false,
                assigning: false,
                showRejectModal: false,
                rejectionReason: '',
                selectedTeamId: '{{ $appointment->recommended_team_id ?? '' }}',

                async approveAppointment() {
                    if (!confirm('Are you sure you want to approve this appointment? This will trigger optimization and create teams.')) {
                        return;
                    }

                    this.approving = true;

                    try {
                        const response = await fetch('{{ route("admin.appointments.approve", $appointment->id) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            alert('✅ ' + data.message);
                            window.location.reload();
                        } else {
                            alert('❌ ' + (data.message || 'Failed to approve appointment'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('❌ An error occurred while approving the appointment');
                    } finally {
                        this.approving = false;
                    }
                },

                async rejectAppointment() {
                    if (!this.rejectionReason.trim()) {
                        alert('Please provide a rejection reason');
                        return;
                    }

                    this.rejecting = true;

                    try {
                        const response = await fetch('{{ route("admin.appointments.reject", $appointment->id) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                rejection_reason: this.rejectionReason
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            alert('✅ ' + data.message);
                            window.location.reload();
                        } else {
                            alert('❌ ' + (data.message || 'Failed to reject appointment'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('❌ An error occurred while rejecting the appointment');
                    } finally {
                        this.rejecting = false;
                    }
                },

                async assignExistingTeam() {
                    if (!this.selectedTeamId) {
                        alert('Please select a team');
                        return;
                    }

                    if (!confirm('Confirm team assignment?')) {
                        return;
                    }

                    this.assigning = true;

                    try {
                        const response = await fetch('{{ route("admin.appointments.assign-team", $appointment->id) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                team_id: this.selectedTeamId
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            alert('✅ ' + data.message);
                            window.location.reload();
                        } else {
                            alert('❌ ' + (data.message || 'Failed to assign team'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('❌ An error occurred while assigning the team');
                    } finally {
                        this.assigning = false;
                    }
                },

                async createTeamViaOptimization() {
                    if (!confirm('Run optimization to create teams and assign employees for this date?')) {
                        return;
                    }

                    this.assigning = true;

                    try {
                        const response = await fetch('{{ route("admin.appointments.assign-team", $appointment->id) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                // No team_id - triggers optimization
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            alert('✅ ' + data.message);
                            window.location.reload();
                        } else {
                            alert('❌ ' + (data.message || 'Failed to create team'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('❌ An error occurred while creating teams');
                    } finally {
                        this.assigning = false;
                    }
                }
            }
        }

    </script>
</x-layouts.general-employer>
