{{-- Rejected Tasks section — included from resources/views/admin/tasks.blade.php --}}
{{-- Lists tasks rejected by employees and provides a manual reassignment modal. --}}
{{-- API: see app/Http/Controllers/Api/AdminTaskReassignmentController.php --}}

@php
    $rejectionCeiling = (int) config('rejection.per_task_ceiling', 3);
@endphp

<div class="flex flex-col gap-4 w-full px-4" x-data="rejectedTasks()" x-init="init()">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Rejected Tasks</h2>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                Tasks rejected by employees. Per-task ceiling: {{ $rejectionCeiling }} rejections before mandatory admin handling.
            </p>
        </div>
        <button type="button" @click="fetchTasks()"
                class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium flex items-center gap-1">
            <i class="fa-solid fa-arrows-rotate text-xs" :class="loading && 'fa-spin'"></i>
            Refresh
        </button>
    </div>

    {{-- Loading state --}}
    <template x-if="loading">
        <div class="flex items-center justify-center py-8 text-gray-500 dark:text-gray-400 border border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
            <i class="fa-solid fa-spinner fa-spin mr-2"></i>
            Loading rejected tasks…
        </div>
    </template>

    {{-- Empty state — matches the To Do list empty state in this same page. --}}
    <template x-if="!loading && tasks.length === 0">
        <div class="p-8 text-center text-gray-500 dark:text-gray-400 border border-dashed border-gray-400 dark:border-gray-700 rounded-lg">
            <i class="fas fa-check-circle text-3xl mb-3 opacity-50"></i>
            <p class="font-semibold text-sm">No rejected tasks</p>
            <p class="text-xs">When an employee rejects a task, it will appear here for manual reassignment.</p>
        </div>
    </template>

    {{-- Table --}}
    <template x-if="!loading && tasks.length > 0">
        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <th class="px-4 py-3">Task</th>
                        <th class="px-4 py-3">When</th>
                        <th class="px-4 py-3">Client / Location</th>
                        <th class="px-4 py-3">Rejected by</th>
                        <th class="px-4 py-3">Reason</th>
                        <th class="px-4 py-3">Count</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="t in tasks" :key="t.task_id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900 dark:text-white" x-text="t.task_description"></div>
                                <div class="text-xs text-gray-500 dark:text-gray-400" x-text="'#' + t.task_id"></div>
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <div x-text="formatDate(t.scheduled_date)"></div>
                                <div class="text-xs text-gray-500" x-text="formatTime(t.scheduled_time)"></div>
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <div x-text="t.client_name || '—'"></div>
                                <div class="text-xs text-gray-500" x-text="t.location || '—'"></div>
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <span x-text="(t.last_rejection && t.last_rejection.employee_name) || '—'"></span>
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300 max-w-xs">
                                <span class="line-clamp-2" x-text="(t.last_rejection && t.last_rejection.reason) || '—'"></span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                                      :class="t.ceiling_reached
                                          ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300'
                                          : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300'"
                                      x-text="t.rejection_count + '/' + t.rejection_ceiling"></span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button type="button" @click="openReassignModal(t)"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                    <i class="fa-solid fa-arrows-rotate text-xs"></i>
                                    Reassign
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </template>

    {{-- Reassign modal --}}
    <div x-show="modalOpen" x-cloak
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
         @click.self="closeModal()">

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col"
             @click.stop>

            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Reassign Task</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"
                       x-text="selectedTask ? selectedTask.task_description : ''"></p>
                </div>
                <button type="button" @click="closeModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="px-6 py-5 overflow-y-auto flex-1">

                <template x-if="optionsLoading">
                    <div class="flex items-center justify-center py-8 text-gray-500">
                        <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                        Loading reassignment options…
                    </div>
                </template>

                <template x-if="!optionsLoading && options.length === 0">
                    <div class="flex flex-col items-center justify-center py-8 text-gray-500">
                        <i class="fa-solid fa-circle-exclamation text-2xl mb-2 text-yellow-500"></i>
                        <p class="font-medium text-sm">No alternative teams available on this date.</p>
                        <p class="text-xs text-center mt-1">Consider rescheduling the appointment with the client.</p>
                    </div>
                </template>

                <template x-if="!optionsLoading && options.length > 0">
                    <div class="space-y-3">
                        <div class="text-xs text-gray-600 dark:text-gray-400 mb-2">
                            Pick a team to take over this task. Only teams scheduled for the same service date are listed.
                        </div>

                        <template x-for="opt in options" :key="opt.team_id">
                            <label class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer transition-colors"
                                   :class="selectedTeamId === opt.team_id
                                       ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                       : 'border-gray-200 dark:border-gray-700 hover:border-blue-300'">
                                <input type="radio" :value="opt.team_id" x-model.number="selectedTeamId"
                                       class="mt-1 text-blue-600">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-sm text-gray-900 dark:text-white" x-text="opt.team_name"></span>
                                        <span x-show="opt.understaffed"
                                              class="text-[10px] px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300">
                                            Understaffed (&lt; 2 active)
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <span x-text="opt.active_member_count + ' active member(s)'"></span>
                                        ·
                                        <span x-text="opt.current_task_count + ' task(s) today'"></span>
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1" x-show="opt.members.length > 0">
                                        <template x-for="(m, idx) in opt.members" :key="m.employee_id">
                                            <span>
                                                <span x-text="m.name || ('Emp #' + m.employee_id)"></span>
                                                <span x-show="idx < opt.members.length - 1">, </span>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </label>
                        </template>

                        <div class="mt-4">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                Note (optional)
                            </label>
                            <textarea x-model="reassignNote" rows="2"
                                      class="w-full text-sm px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                      placeholder="Why this reassignment? (visible in audit log)"></textarea>
                        </div>
                    </div>
                </template>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-2">
                <button type="button" @click="closeModal()"
                        class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="button" @click="submitReassign()"
                        :disabled="!selectedTeamId || submitting"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold bg-blue-600 hover:bg-blue-700 disabled:bg-blue-300 disabled:cursor-not-allowed text-white rounded-lg transition-colors">
                    <i class="fa-solid fa-spinner fa-spin" x-show="submitting"></i>
                    <span x-text="submitting ? 'Reassigning…' : 'Confirm Reassign'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function rejectedTasks() {
        return {
            loading: true,
            tasks: [],

            modalOpen: false,
            selectedTask: null,
            options: [],
            optionsLoading: false,
            selectedTeamId: null,
            reassignNote: '',
            submitting: false,

            async init() {
                await this.fetchTasks();
            },

            async fetchTasks() {
                this.loading = true;
                try {
                    const res = await fetch('{{ route('admin.rejected-tasks.list') }}', {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });
                    const data = await res.json();
                    this.tasks = data.success ? data.data : [];
                } catch (e) {
                    console.error('Failed to load rejected tasks', e);
                    this.tasks = [];
                } finally {
                    this.loading = false;
                }
            },

            async openReassignModal(task) {
                this.selectedTask = task;
                this.modalOpen = true;
                this.selectedTeamId = null;
                this.reassignNote = '';
                this.options = [];
                this.optionsLoading = true;

                try {
                    const url = '{{ url('admin/rejected-tasks') }}/' + task.task_id + '/options';
                    const res = await fetch(url, {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });
                    const data = await res.json();
                    this.options = data.success ? data.options : [];
                } catch (e) {
                    console.error('Failed to load options', e);
                    this.options = [];
                } finally {
                    this.optionsLoading = false;
                }
            },

            closeModal() {
                this.modalOpen = false;
                this.selectedTask = null;
                this.options = [];
                this.selectedTeamId = null;
                this.reassignNote = '';
            },

            async submitReassign() {
                if (!this.selectedTeamId || !this.selectedTask) return;
                this.submitting = true;

                try {
                    const url = '{{ url('admin/rejected-tasks') }}/' + this.selectedTask.task_id + '/reassign';
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            team_id: this.selectedTeamId,
                            note: this.reassignNote,
                        }),
                    });
                    const data = await res.json();

                    if (res.ok && data.success) {
                        this.closeModal();
                        if (window.showSuccessDialog) {
                            window.showSuccessDialog('Task Reassigned', data.message || 'Task reassigned successfully.');
                        } else {
                            alert(data.message || 'Task reassigned successfully.');
                        }
                        await this.fetchTasks();
                    } else {
                        if (window.showErrorDialog) {
                            window.showErrorDialog('Reassignment Failed', data.message || 'Could not reassign task.');
                        } else {
                            alert(data.message || 'Could not reassign task.');
                        }
                    }
                } catch (e) {
                    console.error('Reassign failed', e);
                    alert('Unexpected error during reassignment. See console.');
                } finally {
                    this.submitting = false;
                }
            },

            formatDate(d) {
                if (!d) return '—';
                try {
                    return new Date(d).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
                } catch (e) { return d; }
            },

            formatTime(t) {
                if (!t) return '';
                const [h, m] = String(t).split(':');
                if (!h) return t;
                const hour = parseInt(h, 10);
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const hour12 = hour % 12 || 12;
                return hour12 + ':' + (m ?? '00') + ' ' + ampm;
            },
        }
    }
</script>
@endpush
