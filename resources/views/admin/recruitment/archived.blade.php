<x-layouts.general-employer :title="'Archived — Recruitment'">
    <section class="w-full flex flex-col lg:flex-col gap-4 p-4 md:p-6" x-data="archivedData()">
        <!-- Header -->
        <div class="flex flex-col gap-2 mb-2">
            <div class="my-4">
                <x-employer-components.breadcrumb :items="[
                    ['label' => 'Recruitment', 'url' => route('admin.recruitment.index')],
                    ['label' => 'Archived'],
                ]" />
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Archived</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">View and manage deleted applications and archived job postings</p>
        </div>

        <!-- Deleted Applications Section -->
        <div>
            <div class="flex items-center gap-3 mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Deleted Applications</h2>
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400"
                    x-text="deletedApplications.length"></span>
            </div>

            <div x-show="deletedApplications.length > 0"
                class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Job Title</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Job Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Deleted At</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(app, index) in deletedApplications" :key="'app-'+app.id">
                            <tr class="even:bg-gray-50 dark:even:bg-gray-800/50">
                                <!-- Email -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white" x-text="app.email"></div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400" x-show="app.alternative_email" x-text="app.alternative_email"></div>
                                </td>

                                <!-- Job Title -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-200" x-text="app.job_title"></div>
                                </td>

                                <!-- Job Type -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full"
                                        :class="getAppTypeBadgeClass(app.job_type)"
                                        x-text="app.job_type ? app.job_type.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'N/A'"></span>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full"
                                        :class="getAppStatusBadgeClass(app.status)"
                                        x-text="getAppStatusLabel(app.status)"></span>
                                </td>

                                <!-- Deleted At -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400" x-text="app.deleted_at"></div>
                                </td>

                                <!-- Action -->
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <button @click="restoreApplication(app.id, index)"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                                        <i class="fa-solid fa-rotate-left mr-1.5"></i>Restore
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <template x-if="deletedApplications.length === 0">
                <div class="w-full rounded-lg border-1 border-dashed border-gray-200 dark:border-gray-700 px-6 py-16 text-center">
                    <i class="fa-solid fa-file-circle-xmark text-3xl mb-3 block w-full text-gray-400 dark:text-gray-500"></i>
                    <p class="text-base font-medium text-gray-500 dark:text-gray-400">No deleted applications</p>
                    <p class="text-xs mt-2 text-gray-400 dark:text-gray-500">Deleted applications will appear here</p>
                </div>
            </template>
        </div>

        <!-- Archived Job Postings Section -->
        <div class="mt-6">
            <div class="flex items-center gap-3 mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Archived Job Postings</h2>
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400"
                    x-text="archivedPostings.length"></span>
            </div>

            <div x-show="archivedPostings.length > 0"
                class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Job Title</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Location</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Salary</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Applicants</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(job, index) in archivedPostings" :key="'job-'+job.id">
                            <tr class="even:bg-gray-50 dark:even:bg-gray-800/50">
                                <!-- Job Title + Icon -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                                            :class="getIconBgClass(job.iconColor)">
                                            <i class="fas text-sm" :class="[job.icon, getIconTextClass(job.iconColor)]"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white" x-text="job.title"></div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 max-w-[200px] truncate" x-text="job.description"></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Type -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full"
                                        :class="getTypeBadgeClass(job.type)" x-text="job.typeBadge"></span>
                                </td>

                                <!-- Location -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1 text-sm text-gray-900 dark:text-gray-200">
                                        <i class="fas fa-map-marker-alt text-gray-400 text-xs"></i>
                                        <span x-text="job.location"></span>
                                    </div>
                                </td>

                                <!-- Salary -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white" x-text="job.salary"></div>
                                </td>

                                <!-- Applicants -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-200"
                                        x-text="(job.applicantCount || 0) + ' ' + ((job.applicantCount === 1) ? 'applicant' : 'applicants')">
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                        Archived
                                    </span>
                                </td>

                                <!-- Action -->
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <button @click="restoreJob(job.id, index)"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                                        <i class="fa-solid fa-rotate-left mr-1.5"></i>Restore
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <template x-if="archivedPostings.length === 0">
                <div class="w-full rounded-lg border-1 border-dashed border-gray-200 dark:border-gray-700 px-6 py-16 text-center">
                    <i class="fa-solid fa-archive text-3xl mb-3 block w-full text-gray-400 dark:text-gray-500"></i>
                    <p class="text-base font-medium text-gray-500 dark:text-gray-400">No archived job postings</p>
                    <p class="text-xs mt-2 text-gray-400 dark:text-gray-500">Archived job postings will appear here</p>
                </div>
            </template>
        </div>

        <!-- Success Dialog -->
        <template x-teleport="body">
            <x-employer-components.success-dialog title="Success" message="" buttonText="Continue" />
        </template>
    </section>

    <script>
        function archivedData() {
            const applicantCounts = @json($applicantCounts ?? new \stdClass());
            const dbPostings = @json($archivedPostings ?? []).map(job => ({
                id: job.id,
                title: job.title,
                description: job.description,
                location: job.location,
                salary: job.salary,
                type: job.type,
                typeBadge: job.type_badge,
                icon: job.icon,
                iconColor: job.icon_color,
                is_active: job.is_active,
                status: job.status,
                applicantCount: applicantCounts[job.title] || 0
            }));

            const dbDeletedApps = @json($deletedApplications ?? []).map(app => ({
                id: app.id,
                email: app.email,
                alternative_email: app.alternative_email,
                job_title: app.job_title,
                job_type: app.job_type,
                status: app.status,
                deleted_at: new Date(app.deleted_at).toLocaleDateString('en-US', {
                    month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true
                })
            }));

            return {
                archivedPostings: dbPostings,
                deletedApplications: dbDeletedApps,
                showSuccess: false,
                successTitle: '',
                successMessage: '',
                successButtonText: 'Continue',
                successRedirectUrl: '',

                async restoreJob(id, index) {
                    try {
                        const response = await fetch(`/admin/job-postings/${id}/restore`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.archivedPostings.splice(index, 1);
                            this.successTitle = 'Job Posting Restored';
                            this.successMessage = 'The job posting has been restored and is now active.';
                            this.successRedirectUrl = window.location.href;
                            this.showSuccess = true;
                        } else {
                            window.showErrorDialog('Restore Failed', data.message || 'Failed to restore job posting.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        window.showErrorDialog('Restore Failed', 'An error occurred while restoring the job posting.');
                    }
                },

                async restoreApplication(id, index) {
                    try {
                        const response = await fetch(`/admin/recruitment/${id}/restore`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.deletedApplications.splice(index, 1);
                            this.successTitle = 'Application Restored';
                            this.successMessage = 'The application has been restored successfully.';
                            this.successRedirectUrl = window.location.href;
                            this.showSuccess = true;
                        } else {
                            window.showErrorDialog('Restore Failed', data.message || 'Failed to restore application.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        window.showErrorDialog('Restore Failed', 'An error occurred while restoring the application.');
                    }
                },

                getIconBgClass(color) {
                    const classes = {
                        'blue': 'bg-blue-50 dark:bg-blue-900/30',
                        'green': 'bg-green-50 dark:bg-green-900/30',
                        'purple': 'bg-purple-50 dark:bg-purple-900/30',
                        'orange': 'bg-orange-50 dark:bg-orange-900/30',
                        'red': 'bg-red-50 dark:bg-red-900/30'
                    };
                    return classes[color] || classes['blue'];
                },

                getIconTextClass(color) {
                    const classes = {
                        'blue': 'text-blue-600 dark:text-blue-400',
                        'green': 'text-green-600 dark:text-green-400',
                        'purple': 'text-purple-600 dark:text-purple-400',
                        'orange': 'text-orange-600 dark:text-orange-400',
                        'red': 'text-red-600 dark:text-red-400'
                    };
                    return classes[color] || classes['blue'];
                },

                getTypeBadgeClass(type) {
                    const classes = {
                        'full-time': 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
                        'part-time': 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
                        'remote': 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400'
                    };
                    return classes[type] || classes['full-time'];
                },

                getAppTypeBadgeClass(type) {
                    const classes = {
                        'full-time': 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
                        'part-time': 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
                        'remote': 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400'
                    };
                    return classes[type] || 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400';
                },

                getAppStatusBadgeClass(status) {
                    const classes = {
                        'pending': 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
                        'reviewed': 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                        'interview_scheduled': 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400',
                        'hired': 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                        'rejected': 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
                    };
                    return classes[status] || 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400';
                },

                getAppStatusLabel(status) {
                    const labels = {
                        'pending': 'Pending',
                        'reviewed': 'Reviewed',
                        'interview_scheduled': 'Interview Scheduled',
                        'hired': 'Hired',
                        'rejected': 'Rejected'
                    };
                    return labels[status] || status;
                }
            };
        }
    </script>
</x-layouts.general-employer>
