<x-layouts.general-employer :title="'Backup & Restore'">
    <x-skeleton-page :preset="'default'">
    <section class="flex w-full flex-col p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <div class="max-w-4xl mx-auto w-full" x-data="backupPage()">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Backup &amp; Restore</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Create system backups, restore from previous backups, and manage automatic backup schedules.</p>
            </div>

            <!-- Why this matters card -->
            <div class="mb-6 p-4 rounded-lg border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20">
                <div class="flex gap-3">
                    <i class="fa-solid fa-shield-halved text-blue-500 text-lg mt-0.5"></i>
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        <p class="font-medium text-gray-900 dark:text-white mb-1">Disaster recovery</p>
                        <p>If the system is ever compromised, lost, or corrupted, you can restore CastCrew from a backup. <strong>Full backups</strong> include the database <em>and</em> all uploaded files (resumes, photos, quotation PDFs). <strong>Quick DB-only backups</strong> are faster and useful before risky changes.</p>
                    </div>
                </div>
            </div>

            <!-- Create Backup -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fa-solid fa-cloud-arrow-up mr-2 text-blue-500"></i>
                    Create Backup
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <button type="button" @click="createBackup('full')" :disabled="creating"
                            class="flex items-center justify-center gap-2 px-6 py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-50">
                        <i class="fa-solid fa-box-archive"></i>
                        <span x-show="!(creating && creatingType === 'full')">Create Full Backup</span>
                        <span x-show="creating && creatingType === 'full'"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Creating...</span>
                    </button>

                    <button type="button" @click="createBackup('db')" :disabled="creating"
                            class="flex items-center justify-center gap-2 px-6 py-4 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 font-semibold rounded-lg border border-gray-300 dark:border-gray-600 transition-colors disabled:opacity-50">
                        <i class="fa-solid fa-database"></i>
                        <span x-show="!(creating && creatingType === 'db')">Quick DB-Only Backup</span>
                        <span x-show="creating && creatingType === 'db'"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Creating...</span>
                    </button>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
                    Full backup includes database + uploaded files. Quick DB-only backup includes the database only.
                </p>
            </div>

            <!-- Auto Backup Status -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fa-solid fa-clock-rotate-left mr-2 text-blue-500"></i>
                    Automatic Backups
                </h2>

                <div class="flex items-center justify-between py-2">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Weekly auto-backup</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Runs every Sunday at 2:00 AM. Keeps the 4 most recent backups.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" :checked="autoEnabled" @change="toggleAuto($event.target.checked)">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>

            <!-- Existing Backups -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fa-solid fa-list mr-2 text-blue-500"></i>
                    Existing Backups
                </h2>

                @if (count($backups) === 0)
                    <p class="text-sm text-gray-500 dark:text-gray-400 py-6 text-center">No backups yet. Click "Create Full Backup" above to make your first one.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700 text-left">
                                    <th class="py-2 pr-4 text-gray-600 dark:text-gray-400 font-medium">Filename</th>
                                    <th class="py-2 pr-4 text-gray-600 dark:text-gray-400 font-medium">Type</th>
                                    <th class="py-2 pr-4 text-gray-600 dark:text-gray-400 font-medium">Size</th>
                                    <th class="py-2 pr-4 text-gray-600 dark:text-gray-400 font-medium">Created</th>
                                    <th class="py-2 text-right text-gray-600 dark:text-gray-400 font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($backups as $backup)
                                    <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                        <td class="py-3 pr-4 text-gray-900 dark:text-white">
                                            <span class="font-mono text-xs">{{ $backup['filename'] }}</span>
                                        </td>
                                        <td class="py-3 pr-4">
                                            @if ($backup['type'] === 'full')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300"><i class="fa-solid fa-box-archive text-[10px]"></i> Full</span>
                                            @elseif ($backup['type'] === 'prerestore')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300"><i class="fa-solid fa-shield text-[10px]"></i> Pre-restore</span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300"><i class="fa-solid fa-database text-[10px]"></i> DB only</span>
                                            @endif
                                        </td>
                                        <td class="py-3 pr-4 text-gray-700 dark:text-gray-300">{{ $backup['size_human'] }}</td>
                                        <td class="py-3 pr-4 text-gray-700 dark:text-gray-300">{{ $backup['created_at']->format('M j, Y g:i A') }}</td>
                                        <td class="py-3 text-right whitespace-nowrap">
                                            <a href="{{ route('admin.backup.download', ['filename' => $backup['filename']]) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded">
                                                <i class="fa-solid fa-download"></i> Download
                                            </a>
                                            <button type="button" @click="confirmDelete('{{ $backup['filename'] }}')"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded">
                                                <i class="fa-solid fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Restore Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6 border border-red-200 dark:border-red-900/40">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                    <i class="fa-solid fa-rotate-left mr-2 text-red-500"></i>
                    Restore From Backup
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Upload a backup file (<code class="text-xs">.sql</code> or <code class="text-xs">.zip</code>) to restore the system.
                    <strong class="text-red-600 dark:text-red-400">This will overwrite all current data.</strong>
                    A safety snapshot of the current database is created automatically before restoring.
                </p>

                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-1">Backup file</label>
                        <input type="file" x-ref="restoreFile" accept=".sql,.zip"
                               class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-200">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-1">Your admin password</label>
                        <input type="password" x-model="restorePassword"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter your password to authorize restore">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-1">Type <code>RESTORE</code> to confirm</label>
                        <input type="text" x-model="restoreConfirm"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent font-mono"
                               placeholder="RESTORE">
                    </div>

                    <button type="button" @click="performRestore()"
                            :disabled="restoring || restoreConfirm !== 'RESTORE' || !restorePassword"
                            class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!restoring"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Restore Now</span>
                        <span x-show="restoring"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Restoring... do not close this page</span>
                    </button>
                </div>
            </div>

        </div>
    </section>
    </x-skeleton-page>

    @push('scripts')
    <script>
    function backupPage() {
        return {
            creating: false,
            creatingType: null,
            restoring: false,
            restorePassword: '',
            restoreConfirm: '',
            autoEnabled: @json($autoBackupEnabled),

            async createBackup(type) {
                const label = type === 'full' ? 'Full Backup' : 'Quick DB Backup';
                try {
                    await window.showConfirmDialog(
                        'Create ' + label + '?',
                        type === 'full'
                            ? 'This will back up the database and all uploaded files. It may take a minute or two.'
                            : 'This will back up the database only. Should be fast.',
                        'Create', 'Cancel'
                    );
                } catch (e) { return; }

                this.creating = true;
                this.creatingType = type;
                try {
                    const res = await fetch('{{ route('admin.backup.create') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ type })
                    });
                    const data = await res.json();
                    if (data.success) {
                        window.showSuccessDialog('Backup Created', 'Your backup is ready: ' + data.backup.filename, 'OK');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        window.showErrorDialog('Backup Failed', data.message || 'Failed to create backup.');
                    }
                } catch (e) {
                    window.showErrorDialog('Backup Failed', 'A network error occurred. Please try again.');
                } finally {
                    this.creating = false;
                    this.creatingType = null;
                }
            },

            async toggleAuto(enabled) {
                try {
                    const res = await fetch('{{ route('admin.backup.toggle-auto') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ enabled })
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.autoEnabled = enabled;
                    }
                } catch (e) { /* silent */ }
            },

            async confirmDelete(filename) {
                let password;
                try {
                    password = await window.showPromptDialog
                        ? await window.showPromptDialog('Delete Backup', 'Enter your admin password to delete ' + filename, 'Delete', 'Cancel', 'password')
                        : prompt('Enter your admin password to delete ' + filename);
                } catch (e) { return; }
                if (!password) return;

                try {
                    const res = await fetch('/admin/backup/' + encodeURIComponent(filename), {
                        method: 'DELETE',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ password })
                    });
                    const data = await res.json();
                    if (data.success) {
                        window.showSuccessDialog('Deleted', 'Backup deleted.', 'OK');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        window.showErrorDialog('Delete Failed', data.message || 'Failed to delete backup.');
                    }
                } catch (e) {
                    window.showErrorDialog('Delete Failed', 'A network error occurred.');
                }
            },

            async performRestore() {
                const file = this.$refs.restoreFile.files[0];
                if (!file) {
                    window.showErrorDialog('No File', 'Please choose a backup file first.');
                    return;
                }
                try {
                    await window.showConfirmDialog(
                        'Restore From Backup?',
                        'This will OVERWRITE all current data with the contents of the backup file. A safety snapshot will be created first. Continue?',
                        'Restore', 'Cancel'
                    );
                } catch (e) { return; }

                this.restoring = true;
                try {
                    const fd = new FormData();
                    fd.append('backup_file', file);
                    fd.append('password', this.restorePassword);
                    fd.append('confirm', this.restoreConfirm);

                    const res = await fetch('{{ route('admin.backup.restore') }}', {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: fd
                    });
                    const data = await res.json();
                    if (data.success) {
                        window.showSuccessDialog('Restore Complete', 'Your system has been restored. The page will now reload.', 'OK');
                        setTimeout(() => window.location.reload(), 2000);
                    } else {
                        window.showErrorDialog('Restore Failed', data.message || 'Failed to restore.');
                    }
                } catch (e) {
                    window.showErrorDialog('Restore Failed', 'A network error occurred.');
                } finally {
                    this.restoring = false;
                }
            },
        };
    }
    </script>
    @endpush
</x-layouts.general-employer>
