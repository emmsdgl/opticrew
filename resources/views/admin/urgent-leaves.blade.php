<x-layouts.general-employer :title="'Urgent Leaves'">
    <x-skeleton-page :preset="'default'">
    <section class="flex w-full flex-col p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <div class="max-w-6xl mx-auto w-full" x-data="urgentLeavesPage()">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Urgent Leaves</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Mid-shift exits requiring replacement assignment and compensation.</p>
            </div>

            <!-- Disclaimer card -->
            <div class="mb-6 p-4 rounded-lg border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 text-sm text-gray-700 dark:text-gray-300">
                <i class="fa-solid fa-info-circle mr-2 text-amber-600"></i>
                When an employee submits an Urgent Leave, the system clocks them out immediately and starts a grace period (default 30 min). If you don't assign a replacement and set the compensation amount within that window, the system will auto-assign the employee with the fewest pending tasks today, and you'll still need to come back here to set the compensation amount.
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                @if($leaves->isEmpty())
                    <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                        <i class="fa-solid fa-bed text-3xl mb-2 opacity-40"></i>
                        <p>No Urgent Leaves yet.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700 text-left">
                                    <th class="py-3 px-4 text-gray-600 dark:text-gray-400 font-medium">Employee</th>
                                    <th class="py-3 px-4 text-gray-600 dark:text-gray-400 font-medium">Triggered</th>
                                    <th class="py-3 px-4 text-gray-600 dark:text-gray-400 font-medium">Status</th>
                                    <th class="py-3 px-4 text-gray-600 dark:text-gray-400 font-medium">Replacement</th>
                                    <th class="py-3 px-4 text-gray-600 dark:text-gray-400 font-medium">Compensation</th>
                                    <th class="py-3 px-4 text-right text-gray-600 dark:text-gray-400 font-medium">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaves as $leave)
                                    @php
                                        $employeeName = $leave->employee?->fullName ?? optional($leave->employee?->user)->name ?? 'Unknown';
                                        $replacementName = $leave->replacement ? ($leave->replacement->fullName ?? optional($leave->replacement->user)->name) : null;
                                        $statusBadge = match($leave->status) {
                                            'awaiting_admin' => ['Awaiting Admin', 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'],
                                            'auto_assigned' => ['Auto-Assigned (set fee)', 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300'],
                                            'manually_assigned' => ['Done', 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'],
                                            'cancelled' => ['Cancelled', 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
                                            default => [$leave->status, 'bg-gray-100 text-gray-700'],
                                        };
                                        $needsAction = in_array($leave->status, ['awaiting_admin', 'auto_assigned'], true);
                                    @endphp
                                    <tr class="border-b border-gray-100 dark:border-gray-700/50 {{ $needsAction ? 'bg-red-50/30 dark:bg-red-900/5' : '' }}">
                                        <td class="py-3 px-4 font-medium text-gray-900 dark:text-white">{{ $employeeName }}</td>
                                        <td class="py-3 px-4 text-gray-700 dark:text-gray-300">{{ $leave->triggered_at?->format('M j, g:i A') }}</td>
                                        <td class="py-3 px-4">
                                            <span class="inline-block px-2 py-0.5 text-xs rounded-full {{ $statusBadge[1] }}">{{ $statusBadge[0] }}</span>
                                        </td>
                                        <td class="py-3 px-4 text-gray-700 dark:text-gray-300">{{ $replacementName ?? '—' }}</td>
                                        <td class="py-3 px-4 text-gray-700 dark:text-gray-300">
                                            @if($leave->compensation_amount !== null)
                                                €{{ number_format((float)$leave->compensation_amount, 2) }}
                                            @else
                                                <span class="text-amber-600 italic">not set</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-right whitespace-nowrap">
                                            @if($needsAction)
                                                <button type="button"
                                                        @click="openAssign({{ $leave->id }}, '{{ addslashes($employeeName) }}', {{ $leave->replacement_employee_id ?? 'null' }}, {{ $leave->compensation_amount ?? 'null' }})"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium bg-blue-600 hover:bg-blue-700 text-white rounded">
                                                    <i class="fa-solid fa-user-pen"></i>
                                                    {{ $leave->status === 'auto_assigned' ? 'Set Compensation' : 'Assign + Compensate' }}
                                                </button>
                                            @else
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 py-3">{{ $leaves->links() }}</div>
                @endif
            </div>

            <!-- Assign modal -->
            <div x-show="modalOpen" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/60" @click="modalOpen=false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Assign Replacement</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">For <span x-text="modalEmployeeName" class="font-semibold"></span>'s Urgent Leave.</p>

                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-1">Replacement Employee</label>
                            <select x-model.number="form.replacement_employee_id"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select an employee...</option>
                                @foreach(\App\Models\Employee::with('user')->where('is_active', true)->get() as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->fullName ?? optional($emp->user)->name ?? ('Employee #' . $emp->id) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-1">Compensation Amount (€)</label>
                            <input type="number" min="0" step="0.01" x-model.number="form.compensation_amount"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g. 75.00">
                            <p class="text-xs text-gray-500 mt-1">Visible to admin only. Replacement employee sees "Compensation will vary".</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-1">Notes (optional)</label>
                            <textarea x-model="form.admin_notes" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Any context for the record..."></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-2 justify-end">
                        <button @click="modalOpen=false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">Cancel</button>
                        <button @click="submitAssign()" :disabled="submitting"
                                class="px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-lg disabled:opacity-50">
                            <span x-show="!submitting">Save</span>
                            <span x-show="submitting" x-cloak><i class="fa-solid fa-spinner fa-spin mr-1"></i>Saving...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </x-skeleton-page>

    @push('scripts')
    <script>
    function urgentLeavesPage() {
        return {
            modalOpen: false,
            modalLeaveId: null,
            modalEmployeeName: '',
            submitting: false,
            form: { replacement_employee_id: '', compensation_amount: '', admin_notes: '' },

            openAssign(leaveId, employeeName, currentReplacement, currentComp) {
                this.modalLeaveId = leaveId;
                this.modalEmployeeName = employeeName;
                this.form.replacement_employee_id = currentReplacement || '';
                this.form.compensation_amount = currentComp || '';
                this.form.admin_notes = '';
                this.modalOpen = true;
            },

            async submitAssign() {
                if (!this.form.replacement_employee_id || !this.form.compensation_amount) {
                    window.showErrorDialog('Missing Information', 'Pick a replacement and enter the compensation amount.');
                    return;
                }
                this.submitting = true;
                try {
                    const res = await fetch('/admin/urgent-leaves/' + this.modalLeaveId + '/assign', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify(this.form)
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        window.showSuccessDialog('Saved', data.message, 'OK');
                        setTimeout(() => window.location.reload(), 1200);
                    } else {
                        window.showErrorDialog('Save Failed', data.message || 'Could not save.');
                    }
                } catch (e) {
                    window.showErrorDialog('Save Failed', 'A network error occurred.');
                } finally {
                    this.submitting = false;
                }
            }
        };
    }
    </script>
    @endpush
</x-layouts.general-employer>
