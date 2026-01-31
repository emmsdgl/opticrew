<x-layouts.general-stepper-form title="Employee Request Form" :steps="['Request Details', 'Confirmation']" :currentStep="$currentStep ?? 1">
    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50" x-data="{ toasts: [] }">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-full"
                 x-transition:enter-end="opacity-100 transform translate-x-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 transform translate-x-0"
                 x-transition:leave-end="opacity-0 transform translate-x-full"
                 class="flex items-center w-full max-w-sm p-4 mb-4 text-gray-500 bg-white rounded-lg shadow-lg dark:text-gray-400 dark:bg-gray-800 border-l-4"
                 :class="toast.type === 'error' ? 'border-red-500' : 'border-blue-500'"
                 role="alert">
                <!-- Icon -->
                <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-lg"
                     :class="toast.type === 'error' ? 'text-red-500 bg-red-100 dark:bg-red-800 dark:text-red-200' : 'text-blue-500 bg-blue-100 dark:bg-blue-800 dark:text-blue-200'">
                    <svg x-show="toast.type === 'error'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <svg x-show="toast.type === 'success'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <!-- Message -->
                <div class="ml-3 text-sm font-normal" x-text="toast.message"></div>
                <!-- Close Button -->
                <button type="button"
                        @click="toast.show = false"
                        class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 14 14">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
        </template>
    </div>

    <div class="max-w-2xl mx-auto" x-data="requestForm()">

        <!-- STEP 1: REQUEST DETAILS -->
        <div x-show="currentStep === 1" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-4"
             x-transition:enter-end="opacity-100 transform translate-x-0">
            <div class="rounded-xl p-6 md:p-8">
                <h2 class="text-2xl font-bold text-center mb-2 text-gray-900 dark:text-white italic">
                    What's the absence request details?
                </h2>

                <div class="mt-8 space-y-6">
                    <!-- Absence Type -->
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">
                            Absence Type <span class="text-red-500">*</span>
                        </label>
                        <select x-model="formData.absence_type" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg
                                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select absence type</option>
                            <option value="Sick Leave">Sick Leave</option>
                            <option value="Vacation Leave">Vacation Leave</option>
                            <option value="Emergency Leave">Emergency Leave</option>
                            <option value="Maternity/Paternity Leave">Maternity/Paternity Leave</option>
                            <option value="Unpaid Leave">Unpaid Leave</option>
                            <option value="Other">Other</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Select what type of absence event or sick day to book/leave
                        </p>
                    </div>

                    <!-- Absence Date -->
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">
                            Absence Date <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fi fi-rr-calendar"></i>
                            </span>
                            <input type="date" 
                                   x-model="formData.absence_date" 
                                   required
                                   :min="new Date().toISOString().split('T')[0]"
                                   placeholder="Opening Date"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Time Range -->
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">
                            Time Range <span class="text-red-500">*</span>
                        </label>
                        <select x-model="formData.time_range" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg
                                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select time range</option>
                            <option value="Full Shift">Full Shift</option>
                            <option value="Morning (First Half)">Morning (First Half)</option>
                            <option value="Afternoon (Second Half)">Afternoon (Second Half)</option>
                            <option value="Custom Hours">Custom Hours</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Select when the entire scheduled work shift
                        </p>
                    </div>

                    <!-- Custom Time Range (shown only if Custom Hours is selected) -->
                    <div x-show="formData.time_range === 'Custom Hours'" 
                         x-transition
                         class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">
                                From Time <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <i class="fi fi-rr-clock"></i>
                                </span>
                                <input type="time" 
                                       x-model="formData.from_time"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg
                                              bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">
                                To Time <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <i class="fi fi-rr-clock"></i>
                                </span>
                                <input type="time" 
                                       x-model="formData.to_time"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg
                                              bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Reason for Absence -->
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">
                            Reason for Absence <span class="text-red-500">*</span>
                        </label>
                        <select x-model="formData.reason" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg
                                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select reason</option>
                            <option value="Illness">Illness</option>
                            <option value="Family Emergency">Family Emergency</option>
                            <option value="Personal Matters">Personal Matters</option>
                            <option value="Medical Appointment">Medical Appointment</option>
                            <option value="Bereavement">Bereavement</option>
                            <option value="Other">Other</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Choose what fits in or is a health-related disease
                        </p>
                    </div>

                    <!-- Proof / Documentation -->
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">
                            Proof / Documentation
                        </label>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center bg-gray-50 dark:bg-gray-800/50">
                            <input type="file" 
                                   x-ref="fileInput"
                                   @change="handleFileUpload($event)"
                                   accept="image/*,.pdf"
                                   class="hidden">
                            
                            <div x-show="!formData.proof_file">
                                <div class="mx-auto w-16 h-16 mb-4 flex items-center justify-center">
                                    <i class="fi fi-rr-picture text-4xl text-blue-500"></i>
                                </div>
                                <button type="button" 
                                        @click="$refs.fileInput.click()"
                                        class="text-blue-600 dark:text-blue-400 font-medium hover:underline">
                                    Upload images or videos or
                                </button>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    browse files on your phone
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                                    PNG, JPG, PDF • Max 10MB
                                </p>
                            </div>

                            <div x-show="formData.proof_file" class="flex items-center justify-between bg-white dark:bg-gray-700 rounded-lg p-3">
                                <div class="flex items-center gap-3">
                                    <i class="fi fi-rr-file text-blue-500 text-2xl"></i>
                                    <div class="text-left">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="formData.proof_file_name"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="formData.proof_file_size"></p>
                                    </div>
                                </div>
                                <button type="button" 
                                        @click="removeFile()"
                                        class="text-red-500 hover:text-red-700">
                                    <i class="fi fi-rr-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Reason Description -->
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">
                            Reason Description
                        </label>
                        <textarea x-model="formData.description" 
                                  rows="4" 
                                  maxlength="350"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg
                                         bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                         focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Limit to ~350 characters"></textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 text-right">
                            <span x-text="formData.description.length"></span>/350
                        </p>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('employee.dashboard') }}">
                            <button type="button" 
                                    class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-300 transition-colors">
                                <i class="fi fi-rr-angle-left mr-2"></i>Back
                            </button>
                        </a>
                        <button type="button" 
                                @click="nextStep()"
                                class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors font-medium">
                            Next<i class="fi fi-rr-angle-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 2: CONFIRMATION -->
        <div x-show="currentStep === 2" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-4"
             x-transition:enter-end="opacity-100 transform translate-x-0" 
             x-cloak>
            <div class="rounded-xl p-6 md:p-8">
                <h2 class="text-2xl font-bold text-center mb-2 text-gray-900 dark:text-white italic">
                    Review Your Request
                </h2>

                <div class="mt-8">
                    <!-- Request Summary -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <!-- Employee Details -->
                        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Employee Details</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                                View your details as the client requesting the cleaning service
                            </p>
                            
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-gray-500 dark:text-gray-400">Name</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        {{ $employee->user->name ?? 'N/A' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-gray-500 dark:text-gray-400">Email Address</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        {{ $employee->user->email ?? 'N/A' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-gray-500 dark:text-gray-400">Mobile Number</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        {{ $employee->user->phone ?? 'N/A' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-gray-500 dark:text-gray-400">Role and Position</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        {{ $employee->position ?? 'Cleaning Staff' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-gray-500 dark:text-gray-400">Service Location</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        {{ $employee->service_location ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Service Details -->
                        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Service Details</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                                View the service details availed for this appointment
                            </p>
                            
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-gray-500 dark:text-gray-400">Absence Type</span>
                                    <span class="font-semibold text-gray-900 dark:text-white" x-text="formData.absence_type || '-'"></span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-gray-500 dark:text-gray-400">Absence Date</span>
                                    <span class="font-semibold text-gray-900 dark:text-white" x-text="formData.absence_date || '-'"></span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-gray-500 dark:text-gray-400">Time Range</span>
                                    <span class="font-semibold text-gray-900 dark:text-white" x-text="getTimeRangeDisplay()"></span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-gray-500 dark:text-gray-400">Reason for Absence</span>
                                    <span class="font-semibold text-gray-900 dark:text-white" x-text="formData.reason || '-'"></span>
                                </div>
                                <div class="flex justify-between items-center py-2" x-show="formData.proof_file">
                                    <span class="text-gray-500 dark:text-gray-400">Proof/Documentation</span>
                                    <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                                        <i class="fi fi-rr-link text-sm"></i>
                                        <span x-text="formData.proof_file_name"></span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Reason Description -->
                        <div class="mb-6" x-show="formData.description">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Reason Description</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400" x-text="formData.description"></p>
                        </div>

                        <!-- Submit Button -->
                        <button type="button" 
                                @click="submitForm()"
                                :disabled="submitting"
                                :class="{'opacity-50 cursor-not-allowed': submitting}"
                                class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-colors">
                            <span x-show="!submitting">Submit Request</span>
                            <span x-show="submitting">Processing...</span>
                        </button>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-between items-center pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" 
                                @click="prevStep()" 
                                class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-300 transition-colors">
                            <i class="fi fi-rr-angle-left mr-2"></i>Back
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.general-stepper-form>

<script>
    function requestForm() {
        return {
            currentStep: 1,
            submitting: false,
            
            formData: {
                absence_type: '',
                absence_date: '',
                time_range: '',
                from_time: '',
                to_time: '',
                reason: '',
                proof_file: null,
                proof_file_name: '',
                proof_file_size: '',
                description: ''
            },

            init() {
                // Any initialization logic
            },

            showToast(message, type = 'error') {
                const toastContainer = Alpine.$data(document.querySelector('#toast-container'));
                const toastId = Date.now();
                const toast = {
                    id: toastId,
                    message: message,
                    type: type,
                    show: true
                };

                toastContainer.toasts.push(toast);

                setTimeout(() => {
                    const index = toastContainer.toasts.findIndex(t => t.id === toastId);
                    if (index !== -1) {
                        toastContainer.toasts[index].show = false;
                        setTimeout(() => {
                            toastContainer.toasts.splice(index, 1);
                        }, 300);
                    }
                }, 5000);
            },

            handleFileUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    // Validate file size (max 10MB)
                    if (file.size > 10 * 1024 * 1024) {
                        this.showToast('File size must be less than 10MB');
                        return;
                    }

                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                    if (!allowedTypes.includes(file.type)) {
                        this.showToast('Only JPG, PNG, and PDF files are allowed');
                        return;
                    }

                    this.formData.proof_file = file;
                    this.formData.proof_file_name = file.name;
                    this.formData.proof_file_size = this.formatFileSize(file.size);
                }
            },

            removeFile() {
                this.formData.proof_file = null;
                this.formData.proof_file_name = '';
                this.formData.proof_file_size = '';
                this.$refs.fileInput.value = '';
            },

            formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
            },

            getTimeRangeDisplay() {
                if (this.formData.time_range === 'Custom Hours' && this.formData.from_time && this.formData.to_time) {
                    return `${this.formData.from_time} - ${this.formData.to_time}`;
                }
                return this.formData.time_range || '-';
            },

            validateStep1() {
                const required = [
                    { field: this.formData.absence_type, name: 'Absence Type' },
                    { field: this.formData.absence_date, name: 'Absence Date' },
                    { field: this.formData.time_range, name: 'Time Range' },
                    { field: this.formData.reason, name: 'Reason for Absence' }
                ];

                // If custom hours selected, validate time fields
                if (this.formData.time_range === 'Custom Hours') {
                    required.push(
                        { field: this.formData.from_time, name: 'From Time' },
                        { field: this.formData.to_time, name: 'To Time' }
                    );
                }

                const missing = required.filter(item => !item.field || item.field.trim() === '');

                if (missing.length > 0) {
                    const fields = missing.map(item => item.name).join(', ');
                    this.showToast(`Please fill in the following required fields: ${fields}`);
                    return false;
                }

                return true;
            },

            nextStep() {
                if (this.currentStep === 1) {
                    if (!this.validateStep1()) {
                        return;
                    }
                }

                if (this.currentStep < 2) {
                    this.currentStep++;
                    this.updateStepperUI();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            prevStep() {
                if (this.currentStep > 1) {
                    this.currentStep--;
                    this.updateStepperUI();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            updateStepperUI() {
                window.dispatchEvent(new CustomEvent('update-stepper', {
                    detail: { step: this.currentStep }
                }));
            },

            async submitForm() {
                this.submitting = true;

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    
                    // Create FormData to handle file upload
                    const formData = new FormData();
                    formData.append('absence_type', this.formData.absence_type);
                    formData.append('absence_date', this.formData.absence_date);
                    formData.append('time_range', this.formData.time_range);
                    formData.append('from_time', this.formData.from_time || '');
                    formData.append('to_time', this.formData.to_time || '');
                    formData.append('reason', this.formData.reason);
                    formData.append('description', this.formData.description);
                    
                    if (this.formData.proof_file) {
                        formData.append('proof_document', this.formData.proof_file);
                    }

                    const response = await fetch('{{ route("employee.requests.store") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        this.showToast(data.message || 'Request submitted successfully!', 'success');
                        
                        setTimeout(() => {
                            window.location.href = data.redirect_url || '{{ route("employee.dashboard") }}';
                        }, 1500);
                    } else {
                        if (data.errors) {
                            const errorMessages = Object.entries(data.errors)
                                .map(([field, messages]) => messages.join(', '))
                                .join(', ');
                            this.showToast('Validation errors: ' + errorMessages);
                        } else {
                            this.showToast(data.message || 'Failed to submit request. Please try again.');
                        }
                        this.submitting = false;
                    }

                } catch (error) {
                    console.error('Error submitting request:', error);
                    this.showToast('An error occurred while submitting your request. Please try again.');
                    this.submitting = false;
                }
            }
        }
    }
</script>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>