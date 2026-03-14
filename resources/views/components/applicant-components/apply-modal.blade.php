{{--
    apply-modal.blade.php
    Single global modal — include once on the page.
    Opens via: $dispatch('open-apply-modal', { title, type })
--}}

@php
    $authUser     = auth()->user();
    $defaultPhone = $authUser?->phone    ?? '';
    $defaultAddr  = $authUser?->location ?? '';
    $defaultEmail = $authUser?->email    ?? '';
@endphp

@once
<style>
.am-scroll::-webkit-scrollbar { display: none; }
.am-scroll { -ms-overflow-style: none; scrollbar-width: none; }
.am-step-line { height: 2px; transition: background-color .35s; }
.am-step-circle { transition: background-color .3s, border-color .3s, color .3s; }
.am-input {
    width: 100%;
    font-size: .6875rem;
    background: rgb(249 250 251 / 1);
    border: 1px solid rgb(229 231 235 / 1);
    border-radius: .5rem;
    padding: .375rem .625rem;
    color: rgb(55 65 81 / 1);
    outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.am-input:focus { border-color: rgb(156 163 175); box-shadow: 0 0 0 1px rgb(156 163 175 / .4); }
.dark .am-input {
    background: rgb(31 41 55 / 1);
    border-color: rgb(55 65 81 / 1);
    color: rgb(209 213 219 / 1);
}
.dark .am-input:focus { border-color: rgb(107 114 128); box-shadow: 0 0 0 1px rgb(107 114 128 / .4); }
.am-pill {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    font-size: .625rem;
    font-weight: 600;
    padding: .2rem .5rem;
    border-radius: 9999px;
    background: rgb(229 231 235 / 1);
    color: rgb(55 65 81 / 1);
    line-height: 1.2;
}
.dark .am-pill { background: rgb(55 65 81 / 1); color: rgb(209 213 219 / 1); }
.am-pill button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: .875rem;
    height: .875rem;
    border-radius: 9999px;
    background: transparent;
    border: none;
    cursor: pointer;
    color: rgb(107 114 128 / 1);
    transition: color .15s, background .15s;
    padding: 0;
    font-size: .5rem;
}
.am-pill button:hover { background: rgb(209 213 219 / 1); color: rgb(55 65 81 / 1); }
.dark .am-pill button:hover { background: rgb(75 85 99 / 1); color: rgb(229 231 235 / 1); }
.am-tag-input {
    flex: 1;
    min-width: 80px;
    font-size: .625rem;
    background: transparent;
    border: none;
    outline: none;
    color: rgb(55 65 81 / 1);
    padding: .2rem 0;
}
.dark .am-tag-input { color: rgb(209 213 219 / 1); }
.am-tag-input::placeholder { color: rgb(156 163 175 / 1); }
.dark .am-tag-input::placeholder { color: rgb(107 114 128 / 1); }
.am-dropdown::-webkit-scrollbar { width: 4px; }
.am-dropdown::-webkit-scrollbar-thumb { background: rgb(156 163 175 / .4); border-radius: 4px; }
</style>
@endonce

<div
    x-data="{
        open: false,
        step: 1,
        loading: false,
        showConfirm: false,
        showSuccess: false,
        showError: false,
        errorMsg: '',
        altEmailError: '',

        jobTitle: '',
        jobType: '',
        requiredDocs: [],

        files: [],
        fileNames: [],
        draggingIndex: null,

        form: {
            first_name:        '',
            last_name:         '',
            middle_initial:    '',
            birthdate:         '',
            phone:             '{{ addslashes($defaultPhone) }}',
            email:             '{{ addslashes($defaultEmail) }}',
            alternative_email: '',

            city:              '',
            country:           '',
            linkedin:          '',
        },

        skillsList:    [],
        languagesList: [],
        skillInput:    '',
        langInput:     '',

        // Country / City dropdown state
        countries:       [],
        cities:          [],
        countrySearch:   '',
        citySearch:      '',
        showCountryDrop: false,
        showCityDrop:    false,
        loadingCities:   false,

        get filteredCountries() {
            const q = this.countrySearch.toLowerCase();
            if (!q) return this.countries.slice(0, 50);
            return this.countries.filter(c => c.toLowerCase().includes(q)).slice(0, 50);
        },
        get filteredCities() {
            const q = this.citySearch.toLowerCase();
            if (!q) return this.cities.slice(0, 50);
            return this.cities.filter(c => c.toLowerCase().includes(q)).slice(0, 50);
        },

        selectCountry(name) {
            this.form.country    = name;
            this.countrySearch   = name;
            this.showCountryDrop = false;
            this.form.city       = '';
            this.citySearch      = '';
            this.cities          = [];
            this.fetchCities(name);
        },
        selectCity(name) {
            this.form.city    = name;
            this.citySearch   = name;
            this.showCityDrop = false;
        },

        async fetchCountries() {
            if (this.countries.length) return;
            try {
                const res  = await fetch('https://countriesnow.space/api/v0.1/countries', { signal: AbortSignal.timeout(8000) });
                const data = await res.json();
                if (!data.error && Array.isArray(data.data)) {
                    this.countries = data.data.map(c => c.country).sort();
                }
            } catch (_) {}
        },
        async fetchCities(country) {
            if (!country) return;
            this.loadingCities = true;
            try {
                const res  = await fetch('https://countriesnow.space/api/v0.1/countries/cities', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ country }),
                    signal: AbortSignal.timeout(8000),
                });
                const data = await res.json();
                if (!data.error && Array.isArray(data.data)) {
                    this.cities = data.data.sort();
                }
            } catch (_) {}
            this.loadingCities = false;
        },

        addSkill() {
            const v = this.skillInput.trim();
            if (v && !this.skillsList.includes(v)) this.skillsList.push(v);
            this.skillInput = '';
        },
        removeSkill(i) { this.skillsList.splice(i, 1); },

        addLang() {
            const v = this.langInput.trim();
            if (v && !this.languagesList.includes(v)) this.languagesList.push(v);
            this.langInput = '';
        },
        removeLang(i) { this.languagesList.splice(i, 1); },

        resetForm() {
            this.form = {
                first_name:        '',
                last_name:         '',
                middle_initial:    '',
                birthdate:         '',
                phone:             '{{ addslashes($defaultPhone) }}',
                email:             '{{ addslashes($defaultEmail) }}',
                alternative_email: '',

                city:              '',
                country:           '',
                linkedin:          '',
            };
            this.skillsList    = [];
            this.languagesList = [];
            this.skillInput    = '';
            this.langInput     = '';
            this.countrySearch = '';
            this.citySearch    = '';
            this.cities        = [];
            this.altEmailError = '';
        },

        validateAltEmail() {
            const v = this.form.alternative_email.trim();
            if (!v) { this.altEmailError = ''; return; }
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            this.altEmailError = re.test(v) ? '' : 'Please enter a valid email address';
        },

        validateAndConfirm() {
            const missing = [];
            if (!(this.form.first_name || '').trim()) missing.push('First Name');
            if (!(this.form.last_name || '').trim()) missing.push('Last Name');
            if (!(this.form.birthdate || '').trim()) missing.push('Birthdate');
            if (!(this.form.phone || '').trim()) missing.push('Phone / Mobile');
            if (!(this.form.email || '').trim()) missing.push('Email Address');
            if (!(this.form.country || '').trim()) missing.push('Country');
            if (!(this.form.city || '').trim()) missing.push('City');

            if (this.altEmailError) {
                missing.push('Alternative Email (invalid format)');
            }

            if (missing.length > 0) {
                this.errorMsg = 'Please fill in the following required fields: ' + missing.join(', ');
                this.showError = true;
                return;
            }
            this.showConfirm = true;
        },

        pickField(fields, aliases = []) {
            for (const key of aliases) {
                const val = fields?.[key];
                if (val !== undefined && val !== null && String(val).trim() !== '') {
                    return String(val).trim();
                }
            }
            return '';
        },

        applyExtractedFields(fields) {
            if (!fields || typeof fields !== 'object') return;

            const mapped = {
                first_name: this.pickField(fields, ['first_name', 'firstname', 'firstName', 'given_name', 'givenName']),
                last_name: this.pickField(fields, ['last_name', 'lastname', 'lastName', 'surname', 'family_name', 'familyName']),
                middle_initial: this.pickField(fields, ['middle_name', 'middlename', 'middleName', 'middle_initial']),
                birthdate: this.pickField(fields, ['birthdate', 'date_of_birth', 'dob']),
                phone: this.pickField(fields, ['phone', 'mobile', 'mobile_number', 'contact_number', 'phone_number']),
                email: this.pickField(fields, ['email', 'email_address', 'primary_email']),

                city: this.pickField(fields, ['city', 'municipality']),
                country: this.pickField(fields, ['country', 'nationality']),
                linkedin: this.pickField(fields, ['linkedin', 'linkedin_url', 'linkedin_profile']),
            };

            const fullName = this.pickField(fields, ['full_name', 'name']);
            if ((!mapped.first_name || !mapped.last_name) && fullName) {
                const parts = fullName.split(/\s+/).filter(Boolean);
                if (!mapped.first_name && parts.length) mapped.first_name = parts[0];
                if (!mapped.last_name && parts.length > 1) mapped.last_name = parts[parts.length - 1];
            }

            Object.keys(mapped).forEach((k) => {
                if (mapped[k]) this.form[k] = mapped[k];
            });

            // Parse skills and languages into pill arrays
            const rawSkills = this.pickField(fields, ['skills']);
            if (rawSkills) {
                this.skillsList = rawSkills.split(/[,;]+/).map(s => s.trim()).filter(s => s.length > 0);
            }
            const rawLangs = this.pickField(fields, ['languages', 'languages_spoken']);
            if (rawLangs) {
                this.languagesList = rawLangs.split(/[,;]+/).map(s => s.trim()).filter(s => s.length > 0);
            }
        },

        openModal(detail) {
            this.jobTitle      = detail.title ?? '';
            this.jobType       = detail.type  ?? '';
            const docs         = detail.requiredDocs ?? [];
            this.requiredDocs  = docs.length > 0 ? docs : [{ name: 'Resume', fileType: 'docx' }];
            this.files         = new Array(this.requiredDocs.length).fill(null);
            this.fileNames     = new Array(this.requiredDocs.length).fill('');
            this.draggingIndex = null;
            this.step          = 1;
            this.loading       = false;
            this.showConfirm   = false;
            this.showSuccess   = false;
            this.showError     = false;
            this.resetForm();
            this.open          = true;
            this.fetchCountries();
        },

        closeModal() { this.open = false; },

        setFile(index, f) {
            const ok = ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!f || !ok.includes(f.type)) return;
            this.files.splice(index, 1, f);
            this.fileNames.splice(index, 1, f.name);
        },

        clearFile(index) {
            this.files.splice(index, 1, null);
            this.fileNames.splice(index, 1, '');
            const el = document.getElementById('fileInput' + index);
            if (el) el.value = '';
        },

        handleDrop(index, e) {
            this.draggingIndex = null;
            this.setFile(index, e.dataTransfer.files[0]);
        },

        handleInput(index, e) { this.setFile(index, e.target.files[0]); },

        get allFilesUploaded() {
            return this.files.length > 0 && this.files.every(f => f !== null);
        },

        get resumeFile() {
            const idx = this.requiredDocs.findIndex(d => (typeof d === 'object' ? d.name : d).toLowerCase() === 'resume');
            return idx >= 0 ? this.files[idx] : this.files[0];
        },

        get resumeFileName() {
            const idx = this.requiredDocs.findIndex(d => (typeof d === 'object' ? d.name : d).toLowerCase() === 'resume');
            return idx >= 0 ? this.fileNames[idx] : this.fileNames[0];
        },

        async extractAndNext() {
            if (!this.allFilesUploaded) return;
            this.loading = true;
            try {
                const fd = new FormData();
                fd.append('resume', this.resumeFile);
                fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
                const res  = await fetch('{{ route('applicant.apply.extract') }}', { method: 'POST', body: fd });
                const data = await res.json();

                // Reset all fields before applying so stale data from a previous extraction is cleared
                this.resetForm();

                if (data.success) {
                    const fields = data.fields ?? data.data?.fields ?? {};
                    this.applyExtractedFields(fields);
                    // Sync dropdown search fields with extracted values
                    if (this.form.country) {
                        this.countrySearch = this.form.country;
                        await this.fetchCities(this.form.country);
                    }
                    // Validate extracted city against the API city list
                    if (this.form.city && this.cities.length) {
                        const extractedCity = this.form.city.toLowerCase().trim();
                        const matched = this.cities.find(c => c.toLowerCase() === extractedCity);
                        if (matched) {
                            this.form.city  = matched;
                            this.citySearch = matched;
                        } else {
                            // No exact match — clear the city so user picks manually
                            this.form.city  = '';
                            this.citySearch = '';
                        }
                    } else if (this.form.city) {
                        this.citySearch = this.form.city;
                    }
                }
            } catch (_) {}
            this.loading = false;
            this.step = 2;
        },

        async submitApply() {
            this.showConfirm = false;
            this.loading     = true;
            try {
                const fd = new FormData();
                fd.append('_token',    document.querySelector('meta[name=csrf-token]').content);
                fd.append('job_title', this.jobTitle);
                fd.append('job_type',  this.jobType);
                this.files.forEach((f, i) => {
                    if (f) {
                        const docName = typeof this.requiredDocs[i] === 'object' ? this.requiredDocs[i].name : this.requiredDocs[i];
                        fd.append('documents[]', f);
                        fd.append('document_labels[]', docName);
                        if (docName.toLowerCase() === 'resume') fd.append('resume', f);
                    }
                });
                if (!fd.has('resume') && this.files[0]) fd.append('resume', this.files[0]);
                Object.entries(this.form).forEach(([k, v]) => fd.append(k, v));
                fd.append('skills', this.skillsList.join(', '));
                fd.append('languages', this.languagesList.join(', '));
                const res  = await fetch('{{ route('applicant.apply.submit') }}', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.success) {
                    this.showSuccess = true;
                } else {
                    this.errorMsg  = data.message || 'Something went wrong.';
                    this.showError = true;
                }
            } catch (_) {
                this.errorMsg  = 'Network error. Please try again.';
                this.showError = true;
            }
            this.loading = false;
        },

    }"
    @open-apply-modal.window="openModal($event.detail)"
>
    <template x-teleport="body">
    <div>

        {{-- ── Backdrop ── --}}
        <div
            x-show="open"
            style="display:none"
            class="fixed inset-0 z-[110] bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click.self="closeModal()"
        >
            {{-- ── Modal Panel ── --}}
            <div
                class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg flex flex-col max-h-[90vh]"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.stop
            >
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 dark:border-gray-800 flex-shrink-0">
                    <div>
                        <p class="text-sm font-semibold text-gray-400 dark:text-gray-500 mb-0.5">Applying for</p>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-snug" x-text="jobTitle"></h2>
                    </div>
                    <button @click="closeModal()"
                        class="w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors flex-shrink-0">
                        <i class="fa-solid fa-xmark text-gray-500 dark:text-gray-400 text-xs"></i>
                    </button>
                </div>

                {{-- Stepper --}}
                <div class="px-6 pt-5 pb-3 flex-shrink-0">
                    <div class="flex items-center">
                        {{-- Step 1 --}}
                        <div class="flex flex-col items-center gap-1.5 flex-shrink-0">
                            <div class="am-step-circle w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-bold border-2"
                                :class="step > 1
                                    ? 'bg-blue-600 dark:bg-blue-600 border-gray-900 dark:border-white text-white dark:text-gray-900'
                                    : step === 1
                                        ? 'bg-gray-900 dark:bg-white border-gray-900 dark:border-white text-white dark:text-gray-900'
                                        : 'border-gray-300 dark:border-gray-600 text-gray-400'">
                                <i class="fa-solid fa-check text-[10px]" x-show="step > 1"></i>
                                <span x-show="step <= 1">1</span>
                            </div>
                            <span class="text-xs font-bold"
                                :class="step >= 1 ? 'text-gray-700 dark:text-gray-200' : 'text-gray-400 dark:text-gray-500'">
                                Upload
                            </span>
                        </div>

                        {{-- Connector --}}
                        <div class="am-step-line flex-1 mx-3 mb-4 rounded-full"
                            :class="step >= 2 ? 'bg-gray-900 dark:bg-white' : 'bg-gray-200 dark:bg-gray-700'">
                        </div>

                        {{-- Step 2 --}}
                        <div class="flex flex-col items-center gap-1.5 flex-shrink-0">
                            <div class="am-step-circle w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-bold border-2"
                                :class="step >= 2
                                    ? 'bg-gray-900 dark:bg-white border-gray-900 dark:border-white text-white dark:text-gray-900'
                                    : 'border-gray-300 dark:border-gray-600 text-gray-400'">
                                2
                            </div>
                            <span class="text-xs font-bold"
                                :class="step >= 2 ? 'text-gray-700 dark:text-gray-200' : 'text-gray-400 dark:text-gray-500'">
                                Details
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Step Content --}}
                <div class="am-scroll flex-1 overflow-y-auto px-6 pb-2 min-h-0">

                    {{-- ── Step 1: Upload ── --}}
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">

                        <div class="flex flex-col my-3">
                            <p class="text-xl font-bold text-gray-800 dark:text-white mb-1">Upload Your Documents</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mb-4">
                                Upload the required documents below (<span class="font-semibold" x-text="requiredDocs.length"></span> required). Your resume will be used to auto-extract details.
                            </p>
                        </div>

                        {{-- Dropzones for each required document --}}
                        <div class="space-y-3">
                            <template x-for="(doc, index) in requiredDocs" :key="index">
                                <div>
                                    {{-- Document label --}}
                                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5 flex items-center gap-1.5">
                                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                                            :class="files[index] ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500'"
                                            x-text="files[index] ? '✓' : (index + 1)"></span>
                                        <span x-text="typeof doc === 'object' ? doc.name : doc"></span>
                                        <span class="text-[10px] font-normal text-gray-400 dark:text-gray-500">(.docx)</span>
                                    </p>

                                    {{-- Dropzone --}}
                                    <div
                                        class="relative border-2 border-dashed rounded-xl text-center transition-all cursor-pointer select-none"
                                        :class="[
                                            files[index] ? 'border-green-200 dark:border-green-800 bg-green-50/50 dark:bg-green-900/10 py-4 px-4' : 'py-10 px-6',
                                            draggingIndex === index
                                                ? 'border-blue-400 bg-blue-50 dark:bg-blue-900/10'
                                                : !files[index] ? 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 bg-gray-50/50 dark:bg-gray-800/30' : ''
                                        ]"
                                        @dragover.prevent="draggingIndex = index"
                                        @dragleave.prevent="draggingIndex = null"
                                        @drop.prevent="handleDrop(index, $event)"
                                        @click="document.getElementById('fileInput' + index).click()"
                                    >
                                        <input type="file" :id="'fileInput' + index" class="hidden" accept=".docx" @change="handleInput(index, $event)">

                                        <div x-show="!fileNames[index]">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 dark:text-gray-600 mb-2 mx-auto"><path d="M2 9V5a2 2 0 0 1 2-2h3.9a2 2 0 0 1 1.69.9l.81 1.2a2 2 0 0 0 1.67.9H20a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-1"/><path d="M2 13h10"/><path d="m9 16 3-3-3-3"/></svg>
                                            <p class="text-[11px] font-medium text-gray-500 dark:text-gray-400">
                                                Drag & drop or <span class="text-blue-500 dark:text-blue-400">browse</span>
                                            </p>
                                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">DOCX only — max 10 MB</p>
                                        </div>

                                        <div x-show="fileNames[index]" class="flex items-center justify-center gap-3">
                                            <i class="fa-regular fa-file-word text-blue-400 text-lg flex-shrink-0"></i>
                                            <div class="text-left min-w-0">
                                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-200 truncate" x-text="fileNames[index]"></p>
                                                <p class="text-[10px] text-green-500 dark:text-green-400">Uploaded</p>
                                            </div>
                                            <button @click.stop="clearFile(index)"
                                                class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                                                <i class="fa-solid fa-xmark text-gray-500 dark:text-gray-400 text-[9px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Upload progress indicator --}}
                        <div class="flex items-center gap-2 my-8">
                            <div class="flex-1 bg-gray-100 dark:bg-gray-800 rounded-full h-1.5 overflow-hidden">
                                <div class="h-full bg-blue-500 dark:bg-blue-400 rounded-full transition-all duration-300"
                                    :style="'width: ' + (files.filter(f => f !== null).length / requiredDocs.length * 100) + '%'"></div>
                            </div>
                            <span class="text-[10px] font-semibold text-gray-500 dark:text-gray-400"
                                x-text="files.filter(f => f !== null).length + ' / ' + requiredDocs.length"></span>
                        </div>
                    </div>

                    {{-- ── Step 2: Details ── --}}
                    <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-5">

                        <div>
                            <p class="text-xl font-semibold text-gray-800 dark:text-white mb-0.5">Review Your Information</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500">Fields were extracted from your resume — edit as needed before submitting.</p>
                        </div>

                        {{-- Personal Information --}}
                        <div>
                            <p class="text-sm font-bold text-gray-400 dark:text-gray-500 my-6">
                                <i class="fa-solid fa-user mr-1"></i>Personal Information
                            </p>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-sm font-normal text-gray-500 dark:text-gray-500 block mb-0.5">First Name</label>
                                    <input type="text" x-model="form.first_name" class="am-input" autocomplete="off">
                                </div>
                                <div>
                                    <label class="text-sm font-normal text-gray-500 dark:text-gray-500 block mb-0.5">Last Name</label>
                                    <input type="text" x-model="form.last_name" class="am-input" autocomplete="off">
                                </div>
                                <div>
                                    <label class="text-sm font-normal text-gray-500 dark:text-gray-500 block mb-0.5">Middle Initial <span class="text-gray-400 dark:text-gray-600 font-normal">(optional)</span></label>
                                    <input type="text" x-model="form.middle_initial" maxlength="5" class="am-input">
                                </div>
                                <div>
                                    <label class="text-sm font-normal text-gray-500 dark:text-gray-500 block mb-0.5">Birthdate</label>
                                    <input type="date" x-model="form.birthdate" class="am-input">
                                </div>
                                <div>
                                    <label class="text-sm font-normal text-gray-500 dark:text-gray-500 block mb-0.5">Phone / Mobile</label>
                                    <input type="text" x-model="form.phone" class="am-input">
                                </div>
                                <div>
                                    <label class="text-sm font-normal text-gray-500 dark:text-gray-500 block mb-0.5">Email Address</label>
                                    <input type="email" x-model="form.email" class="am-input" autocomplete="off" autocapitalize="off" spellcheck="false">
                                </div>
                                <div>
                                    <label class="text-sm font-normal text-gray-500 dark:text-gray-500 block mb-0.5">Alternative Email</label>
                                    <input type="email" x-model="form.alternative_email" @input="validateAltEmail()" class="am-input"
                                        :class="altEmailError && 'border-red-400 dark:border-red-500 focus:border-red-400 focus:ring-red-400/40'">
                                    <p x-show="altEmailError" x-text="altEmailError" class="text-[10px] text-red-500 dark:text-red-400 mt-0.5"></p>
                                </div>
                                {{-- Country dropdown --}}
                                <div class="relative" @mousedown.outside="showCountryDrop = false">
                                    <label class="text-sm font-normal text-gray-500 dark:text-gray-500 block mb-0.5">Country</label>
                                    <input type="text"
                                        x-model="countrySearch"
                                        @focus="showCountryDrop = true"
                                        @input="showCountryDrop = true"
                                        placeholder="Search country…"
                                        class="am-input" autocomplete="off">
                                    <div x-show="showCountryDrop && filteredCountries.length" x-cloak
                                        class="am-dropdown absolute z-50 left-0 right-0 mt-1 max-h-36 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg">
                                        <template x-for="c in filteredCountries" :key="c">
                                            <button type="button" @mousedown.prevent="selectCountry(c)"
                                                class="w-full text-left px-3 py-1.5 text-[11px] text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                                x-text="c"></button>
                                        </template>
                                    </div>
                                </div>
                                {{-- City (editable with suggestions) --}}
                                <div class="relative" @mousedown.outside="showCityDrop = false">
                                    <label class="text-sm font-normal text-gray-500 dark:text-gray-500 block mb-0.5">City</label>
                                    <input type="text"
                                        x-model="citySearch"
                                        @focus="if(cities.length) showCityDrop = true"
                                        @input="form.city = citySearch; if(cities.length) showCityDrop = true"
                                        :placeholder="loadingCities ? 'Loading cities…' : 'Type your city…'"
                                        class="am-input" autocomplete="off">
                                    <div x-show="showCityDrop && filteredCities.length" x-cloak
                                        class="am-dropdown absolute z-50 left-0 right-0 mt-1 max-h-36 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg">
                                        <template x-for="c in filteredCities" :key="c">
                                            <button type="button" @mousedown.prevent="selectCity(c)"
                                                class="w-full text-left px-3 py-1.5 text-[11px] text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                                x-text="c"></button>
                                        </template>
                                    </div>
                                </div>
                                {{-- LinkedIn (optional) --}}
                                <div>
                                    <label class="text-sm font-normal text-gray-500 dark:text-gray-500 block mb-0.5">LinkedIn Profile <span class="text-gray-400 dark:text-gray-600 font-normal">(optional)</span></label>
                                    <input type="text" x-model="form.linkedin" placeholder="linkedin.com/in/..." class="am-input">
                                </div>
                            </div>
                        </div>

                        {{-- Qualifications --}}
                        <div>
                            <p class="text-sm font-bold text-gray-400 dark:text-gray-500 mb-2">
                                <i class="fa-solid fa-star mr-1"></i>Qualifications
                            </p>
                            <div class="space-y-2">
                                {{-- Skills pills --}}
                                <div>
                                    <label class="text-sm font-semibold text-gray-400 dark:text-gray-500 block mb-1">Skills</label>
                                    <div class="am-input flex flex-wrap gap-1.5 items-center min-h-[2rem] !p-1.5">
                                        <template x-for="(skill, i) in skillsList" :key="i">
                                            <span class="am-pill">
                                                <span x-text="skill"></span>
                                                <button type="button" @click="removeSkill(i)"><i class="fa-solid fa-xmark"></i></button>
                                            </span>
                                        </template>
                                        <input type="text"
                                            x-model="skillInput"
                                            @keydown.enter.prevent="addSkill()"
                                            @keydown.comma.prevent="addSkill()"
                                            @blur="addSkill()"
                                            placeholder="Type & press Enter…"
                                            class="am-tag-input">
                                    </div>
                                </div>
                                {{-- Languages pills --}}
                                <div>
                                    <label class="text-sm font-semibold text-gray-400 dark:text-gray-500 block mb-1">Languages Spoken</label>
                                    <div class="am-input flex flex-wrap gap-1.5 items-center min-h-[2rem] !p-1.5">
                                        <template x-for="(lang, i) in languagesList" :key="i">
                                            <span class="am-pill">
                                                <span x-text="lang"></span>
                                                <button type="button" @click="removeLang(i)"><i class="fa-solid fa-xmark"></i></button>
                                            </span>
                                        </template>
                                        <input type="text"
                                            x-model="langInput"
                                            @keydown.enter.prevent="addLang()"
                                            @keydown.comma.prevent="addLang()"
                                            @blur="addLang()"
                                            placeholder="Type & press Enter…"
                                            class="am-tag-input">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Attached documents --}}
                        <div class="space-y-1.5">
                            <template x-for="(doc, index) in requiredDocs" :key="'attached-' + index">
                                <div x-show="fileNames[index]" class="flex items-center gap-2 bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-2 border border-gray-200/60 dark:border-gray-700">
                                    <i class="fa-regular fa-file-word text-blue-400 text-sm flex-shrink-0"></i>
                                    <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400 flex-shrink-0" x-text="(typeof doc === 'object' ? doc.name : doc) + ':'"></span>
                                    <span class="text-[10px] text-gray-600 dark:text-gray-300 truncate flex-1" x-text="fileNames[index]"></span>
                                    <span class="text-[9px] text-gray-400 dark:text-gray-500 flex-shrink-0">Attached</span>
                                </div>
                            </template>
                        </div>

                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 pb-5 pt-3 border-t border-gray-100 dark:border-gray-800 flex-shrink-0 flex items-center justify-between gap-3">
                    <button
                        x-show="step > 1"
                        type="button"
                        @click="step--"
                        class="text-xs font-semibold px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        <i class="fa-solid fa-arrow-left mr-1.5 text-[10px]"></i>Previous
                    </button>

                    <div class="flex-1"></div>

                    {{-- Unified action button — always in the DOM, changes behavior per step --}}
                    <button
                        type="button"
                        @click="step === 1 ? extractAndNext() : validateAndConfirm()"
                        :disabled="step === 1 ? (!allFilesUploaded || loading) : loading"
                        class="text-xs font-bold px-5 py-2 rounded-xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-gray-100 transition-colors disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-1.5">
                        <span x-show="step === 1" class="flex items-center gap-1.5">
                            <i x-show="loading"  class="fa-solid fa-spinner fa-spin text-[10px]"></i>
                            <i x-show="!loading" class="fa-solid fa-wand-magic-sparkles text-[10px]"></i>
                            <span x-text="loading ? 'Extracting…' : 'Extract & Continue'"></span>
                        </span>
                        <span x-show="step === 2" class="flex items-center gap-1.5">
                            <i class="fa-solid fa-paper-plane text-sm"></i>Apply
                        </span>
                    </button>
                </div>

            </div>
        </div>

        {{-- ── Confirmation Dialog ── --}}
        <x-dialogs.confirm-dialog show="open && showConfirm" title="Submit Application?" onCancel="showConfirm = false" onConfirm="submitApply()">
            You're about to apply for <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="jobTitle"></span>. Make sure your details are correct before confirming.
        </x-dialogs.confirm-dialog>

        {{-- ── Success Dialog ── --}}
        <x-dialogs.success-dialog show="open && showSuccess" title="Application Submitted!" @click="closeModal(); window.location.reload()">
            Your application for <span class="font-semibold" x-text="jobTitle"></span> has been submitted. We'll reach out soon via your email.
        </x-dialogs.success-dialog>

        {{-- ── Error Dialog ── --}}
        <x-dialogs.error-dialog show="open && showError" title="Submission Failed" @click="showError = false">
            <span x-text="errorMsg"></span>
        </x-dialogs.error-dialog>

    </div>
    </template>
</div>
