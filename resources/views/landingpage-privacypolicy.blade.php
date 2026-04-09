@extends('components.layouts.general-landing')

@section('title', __('landing.privacy.title'))

@push('styles')
<style>
    body {
        background-color: #f8f9fa;
        background-image: none;
    }
    
    .dark body {
        background-image: none;
        background-color: #1f2937;
    }

    .terms-container {
        max-width: 900px;
        margin: 0 auto;
        background: white;
        border-radius: 1rem;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    @media (min-width: 640px) {
        .terms-container { padding: 2rem; }
    }

    @media (min-width: 1024px) {
        .terms-container { padding: 3rem; }
    }

    .dark .terms-container {
        background: #1f2937;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    .agreement-label {
        color: #9ca3af;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }

    .terms-title {
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
        line-height: 1.2;
        font-size: 1.5rem;
    }

    @media (min-width: 640px) {
        .terms-title { font-size: 1.875rem; }
    }

    @media (min-width: 1024px) {
        .terms-title { font-size: 2.25rem; }
    }

    .dark .terms-title {
        color: #f9fafb;
    }

    .last-updated {
        color: #6b7280;
        font-size: 0.75rem;
        margin-bottom: 1rem;
    }

    @media (min-width: 640px) {
        .last-updated { font-size: 0.8125rem; margin-bottom: 1.25rem; }
    }

    @media (min-width: 1024px) {
        .last-updated { font-size: 0.875rem; margin-bottom: 1.5rem; }
    }

    .dark .last-updated {
        color: #9ca3af;
    }

    .terms-intro {
        color: #4b5563;
        line-height: 1.6;
        margin-bottom: 1.5rem;
        font-size: 0.75rem;
    }

    @media (min-width: 640px) {
        .terms-intro { font-size: 0.8125rem; margin-bottom: 1.75rem; line-height: 1.7; }
    }

    @media (min-width: 1024px) {
        .terms-intro { font-size: 0.95rem; margin-bottom: 2rem; line-height: 1.75; }
    }

    .dark .terms-intro {
        color: #d1d5db;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
        margin-top: 1.75rem;
        margin-bottom: 0.75rem;
    }

    @media (min-width: 640px) {
        .section-title { font-size: 1.125rem; margin-top: 2rem; margin-bottom: 0.875rem; }
    }

    @media (min-width: 1024px) {
        .section-title { font-size: 1.3rem; margin-top: 2.5rem; margin-bottom: 1rem; }
    }

    .dark .section-title {
        color: #f9fafb;
    }

    .subsection-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #1f2937;
        margin-top: 1.25rem;
        margin-bottom: 0.5rem;
    }

    @media (min-width: 640px) {
        .subsection-title { font-size: 1rem; margin-top: 1.375rem; margin-bottom: 0.625rem; }
    }

    @media (min-width: 1024px) {
        .subsection-title { font-size: 1.1rem; margin-top: 1.5rem; margin-bottom: 0.75rem; }
    }

    .dark .subsection-title {
        color: #e5e7eb;
    }

    .terms-content {
        color: #4b5563;
        line-height: 1.6;
        font-size: 0.75rem;
    }

    @media (min-width: 640px) {
        .terms-content { font-size: 0.8125rem; line-height: 1.7; }
    }

    @media (min-width: 1024px) {
        .terms-content { font-size: 0.95rem; line-height: 1.75; }
    }

    .dark .terms-content {
        color: #d1d5db;
    }

    .terms-list {
        margin-left: 1rem;
        margin-top: 0.75rem;
        margin-bottom: 0.75rem;
    }

    @media (min-width: 640px) {
        .terms-list { margin-left: 1.25rem; margin-top: 0.875rem; margin-bottom: 0.875rem; }
    }

    @media (min-width: 1024px) {
        .terms-list { margin-left: 1.5rem; margin-top: 1rem; margin-bottom: 1rem; }
    }

    .terms-list li {
        color: #4b5563;
        line-height: 1.6;
        margin-bottom: 0.375rem;
        padding-left: 0.25rem;
        font-size: 0.75rem;
    }

    @media (min-width: 640px) {
        .terms-list li { font-size: 0.8125rem; line-height: 1.7; margin-bottom: 0.4375rem; padding-left: 0.375rem; }
    }

    @media (min-width: 1024px) {
        .terms-list li { font-size: 0.95rem; line-height: 1.75; margin-bottom: 0.5rem; padding-left: 0.5rem; }
    }

    .dark .terms-list li {
        color: #d1d5db;
    }

    .terms-list li::marker {
        color: #3b82f6;
    }

    .action-buttons {
        display: flex;
        flex-direction: column-reverse;
        gap: 0.75rem;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e5e7eb;
    }

    @media (min-width: 640px) {
        .action-buttons {
            flex-direction: row;
            gap: 1rem;
            margin-top: 3rem;
            padding-top: 2rem;
        }
    }

    .dark .action-buttons {
        border-top-color: #374151;
    }

    .btn-agree {
        background: #2563eb;
        color: white;
        padding: 0.625rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        text-align: center;
        font-size: 0.8125rem;
    }

    @media (min-width: 640px) {
        .btn-agree { width: auto; padding: 0.75rem 2rem; font-size: 0.875rem; }
    }

    .btn-agree:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .btn-decline {
        background: transparent;
        color: #6b7280;
        padding: 0.625rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        text-align: center;
        font-size: 0.8125rem;
    }

    @media (min-width: 640px) {
        .btn-decline { width: auto; padding: 0.75rem 2rem; font-size: 0.875rem; }
    }

    .btn-decline:hover {
        color: #111827;
        background: #f3f4f6;
    }

    .dark .btn-decline {
        color: #9ca3af;
    }

    .dark .btn-decline:hover {
        color: #f9fafb;
        background: #374151;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="terms-container">
        <p class="agreement-label">{{ __('landing.privacy.privacy_label') }}</p>
        <h1 class="terms-title text-4xl sm:text-5xl lg:text-6xl">{{ __('landing.privacy.title') }}</h1>
        <p class="last-updated">{{ __('landing.privacy.last_updated') }}</p>

        <div class="terms-intro">
            <p class="mb-4 text-sm sm:text-xs lg:text-base text-justify">
                {{ __('landing.privacy.intro') }}
            </p>
        </div>

        <!-- Section 1: Information We Collect -->
        <h2 class="section-title">{{ __('landing.privacy.section_1') }}</h2>

        <div class="terms-content">
            <p class="mb-3">
                {{ __('landing.privacy.section_1_p') }}
            </p>
            <ul class="terms-list">
                <li>
                    <strong>{{ __('landing.privacy.section_1_strong_1') }}</strong> {{ __('landing.privacy.section_1_li_1') }}
                </li>
                <li>
                    <strong>{{ __('landing.privacy.section_1_strong_2') }}</strong> {{ __('landing.privacy.section_1_li_2') }}
                </li>
                <li>
                    <strong>{{ __('landing.privacy.section_1_strong_3') }}</strong> {{ __('landing.privacy.section_1_li_3') }}
                </li>
                <li>
                    <strong>{{ __('landing.privacy.section_1_strong_4') }}</strong> {{ __('landing.privacy.section_1_li_4') }}
                </li>
            </ul>
        </div>

        <!-- Section 2: How We Use Your Information -->
        <h2 class="section-title">{{ __('landing.privacy.section_2') }}</h2>

        <div class="terms-content">
            <p class="mb-3">
                {{ __('landing.privacy.section_2_p') }}
            </p>
            <ul class="terms-list">
                <li>
                    <strong>{{ __('landing.privacy.section_2_strong_1') }}</strong> {{ __('landing.privacy.section_2_li_1') }}
                </li>
                <li>
                    <strong>{{ __('landing.privacy.section_2_strong_2') }}</strong> {{ __('landing.privacy.section_2_li_2') }}
                </li>
                <li>
                    <strong>{{ __('landing.privacy.section_2_strong_3') }}</strong> {{ __('landing.privacy.section_2_li_3') }}
                </li>
                <li>
                    <strong>{{ __('landing.privacy.section_2_strong_4') }}</strong> {{ __('landing.privacy.section_2_li_4') }}
                </li>
                <li>
                    <strong>{{ __('landing.privacy.section_2_strong_5') }}</strong> {{ __('landing.privacy.section_2_li_5') }}
                </li>
            </ul>
        </div>

        <!-- Section 3: Data Storage and Security -->
        <h2 class="section-title">{{ __('landing.privacy.section_3') }}</h2>

        <div class="terms-content">
            <p class="mb-3">
                {{ __('landing.privacy.section_3_p') }}
            </p>
            <ul class="terms-list">
                <li>{{ __('landing.privacy.section_3_li_1') }}</li>
                <li>{{ __('landing.privacy.section_3_li_2') }}</li>
                <li>{{ __('landing.privacy.section_3_li_3') }}</li>
            </ul>
        </div>

        <!-- Section 4: Data Sharing and Disclosure -->
        <h2 class="section-title">{{ __('landing.privacy.section_4') }}</h2>

        <div class="terms-content">
            <ul class="terms-list">
                <li>
                    <strong>{{ __('landing.privacy.section_4_strong_1') }}</strong> {{ __('landing.privacy.section_4_li_1') }}
                </li>
                <li>
                    <strong>{{ __('landing.privacy.section_4_strong_2') }}</strong> {{ __('landing.privacy.section_4_li_2') }}
                </li>
                <li>
                    <strong>{{ __('landing.privacy.section_4_strong_3') }}</strong> {{ __('landing.privacy.section_4_li_3') }}
                </li>
            </ul>
        </div>

        <!-- Section 5: User Rights and Controls -->
        <h2 class="section-title">{{ __('landing.privacy.section_5') }}</h2>

        <div class="terms-content">
            <p class="mb-3">
                {{ __('landing.privacy.section_5_p') }}
            </p>
            <ul class="terms-list">
                <li>
                    <strong>{{ __('landing.privacy.section_5_strong_1') }}</strong> {{ __('landing.privacy.section_5_li_1') }}
                </li>
                <li>
                    <strong>{{ __('landing.privacy.section_5_strong_2') }}</strong> {{ __('landing.privacy.section_5_li_2') }}
                </li>
                <li>
                    <strong>{{ __('landing.privacy.section_5_strong_3') }}</strong> {{ __('landing.privacy.section_5_li_3') }}
                </li>
                <li>
                    <strong>{{ __('landing.privacy.section_5_strong_4') }}</strong> {{ __('landing.privacy.section_5_li_4') }}
                </li>
            </ul>
        </div>

        <!-- Section 6: Geolocation Tracking Policy -->
        <h2 class="section-title">{{ __('landing.privacy.section_6') }}</h2>

        <div class="terms-content">
            <p>
                {{ __('landing.privacy.section_6_p') }}
            </p>
        </div>

        <!-- Section 7: Changes to this Policy -->
        <h2 class="section-title">{{ __('landing.privacy.section_7') }}</h2>

        <div class="terms-content">
            <p>
                {{ __('landing.privacy.section_7_p') }}
            </p>
        </div>

        <!-- Section 8: Contact Information -->
        <h2 class="section-title">{{ __('landing.privacy.section_8') }}</h2>

        <div class="terms-content">
            <p>
                {{ __('landing.privacy.section_8_p') }}
            </p>
        </div>

        <!-- Section 9: European Data Privacy (GDPR Compliance) -->
        <h2 class="section-title">{{ __('landing.privacy.section_9') }}</h2>

        <div class="terms-content">
            <p class="mb-3">
                {{ __('landing.privacy.section_9_p') }}
            </p>
            <ul class="terms-list">
                <li>
                    <strong>{{ __('landing.privacy.section_9_strong_1') }}</strong> {{ __('landing.privacy.section_9_li_1') }}
                </li>
                <li>
                    <strong>{{ __('landing.privacy.section_9_strong_2') }}</strong> {{ __('landing.privacy.section_9_li_2') }}
                </li>
                <li>
                    <strong>{{ __('landing.privacy.section_9_strong_3') }}</strong> {{ __('landing.privacy.section_9_li_3') }}
                </li>
                <li>
                    <strong>{{ __('landing.privacy.section_9_strong_4') }}</strong> {{ __('landing.privacy.section_9_li_4') }}
                </li>
                <li>
                    <strong>{{ __('landing.privacy.section_9_strong_5') }}</strong> {{ __('landing.privacy.section_9_li_5') }}
                </li>
                <li>
                    <strong>{{ __('landing.privacy.section_9_strong_6') }}</strong> {{ __('landing.privacy.section_9_li_6') }}
                </li>
                <li>
                    <strong>{{ __('landing.privacy.section_9_strong_7') }}</strong> {{ __('landing.privacy.section_9_li_7') }}
                </li>
            </ul>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const agreeBtn = document.querySelector('.btn-agree');
        const declineBtn = document.querySelector('.btn-decline');
        const urlParams = new URLSearchParams(window.location.search);
        const fromRecruitment = urlParams.get('from') === 'recruitment';
        const jobId = urlParams.get('job') || '';

        agreeBtn.addEventListener('click', function() {
            // Set cookie for 30 days
            document.cookie = 'finnoys_policy_accepted=1; path=/; max-age=' + (30 * 24 * 60 * 60);

            if (fromRecruitment) {
                const redirectUrl = '/recruitment' + (jobId ? '?apply_job=' + jobId : '');
                window.showSuccessDialog('Policy Accepted', 'Thank you for accepting the Privacy Policy.', 'Continue', redirectUrl);
            } else {
                window.showSuccessDialog('Policy Accepted', 'Thank you for accepting the Privacy Policy.');
            }
        });

        declineBtn.addEventListener('click', function() {
            if (fromRecruitment) {
                const redirectUrl = '/recruitment' + (jobId ? '?apply_job=' + jobId : '');
                window.showErrorDialog('Policy Required', 'You must accept the Privacy Policy before applying.', 'Back to Recruitment', redirectUrl);
            } else {
                if(confirm('You must accept the Privacy Policy to continue using the platform.')) {
                }
            }
        });
    });
</script>
@endpush