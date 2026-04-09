@extends('components.layouts.general-landing')

@section('title', __('landing.terms.title'))

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
        padding: 3rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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
        margin-bottom: 1.5rem;
        line-height: 1.2;
    }

    .dark .terms-title {
        color: #f9fafb;
    }

    .terms-intro {
        color: #4b5563;
        line-height: 1.75;
        margin-bottom: 2rem;
        font-size: 0.95rem;
    }

    .dark .terms-intro {
        color: #d1d5db;
    }

    .section-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #111827;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
    }

    .dark .section-title {
        color: #f9fafb;
    }

    .subsection-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }

    .dark .subsection-title {
        color: #e5e7eb;
    }

    .terms-content {
        color: #4b5563;
        line-height: 1.75;
        font-size: 1rem;
    }

    .dark .terms-content {
        color: #d1d5db;
    }

    .terms-list {
        margin-left: 1.5rem;
        margin-top: 1rem;
        margin-bottom: 1rem;
    }

    .terms-list li {
        color: #4b5563;
        line-height: 1.75;
        margin-bottom: 0.5rem;
        padding-left: 0.5rem;
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
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        text-align: center;
        font-size: 0.8125rem;
    }

    @media (min-width: 640px) {
        .btn-agree {
            width: auto;
            padding: 0.75rem 2rem;
            font-size: 0.875rem;
        }
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
        .btn-decline {
            width: auto;
            padding: 0.75rem 2rem;
            font-size: 0.875rem;
        }
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
        <p class="agreement-label">{{ __('landing.terms.agreement_label') }}</p>
        <h1 class="terms-title text-4xl sm:text-5xl lg:text-6xl">{{ __('landing.terms.title') }}</h1>

        <div class="terms-intro">
            <p class="mb-4 text-sm sm:text-xs lg:text-base text-justify">
                {{ __('landing.terms.intro') }}
            </p>
        </div>

        <!-- Section 1: Service Agreement -->
        <h2 class="section-title">{{ __('landing.terms.section_1') }}</h2>

        <div class="subsection-title">{{ __('landing.terms.sub_1_1') }}</div>
        <div class="terms-content text-sm sm:text-xs lg:text-base">
            <p class="mb-3">
                {{ __('landing.terms.sub_1_1_p1') }}
            </p>
            <p>
                <strong>{{ __('landing.terms.sub_1_1_strong') }}</strong> {{ __('landing.terms.sub_1_1_p2') }}
            </p>
        </div>

        <div class="subsection-title">{{ __('landing.terms.sub_1_2') }}</div>
        <ul class="terms-list text-sm sm:text-xs lg:text-base">
            <li>
                <strong>{{ __('landing.terms.sub_1_2_strong_1') }}</strong> {{ __('landing.terms.sub_1_2_li_1') }}
            </li>
            <li>
                <strong>{{ __('landing.terms.sub_1_2_strong_2') }}</strong> {{ __('landing.terms.sub_1_2_li_2') }}
            </li>
        </ul>

        <div class="subsection-title text-sm sm:text-xs lg:text-base">{{ __('landing.terms.sub_1_3') }}</div>
        <div class="terms-content text-sm sm:text-xs lg:text-base">
            <p>
                {{ __('landing.terms.sub_1_3_p') }}
            </p>
        </div>

        <!-- Section 2: User Responsibilities -->
        <h2 class="section-title">{{ __('landing.terms.section_2') }}</h2>

        <div class="subsection-title text-sm sm:text-xs lg:text-base">{{ __('landing.terms.sub_2_1') }}</div>
        <div class="terms-content text-sm sm:text-xs lg:text-base">
            <p>
                {{ __('landing.terms.sub_2_1_p') }}
            </p>
        </div>

        <div class="subsection-title text-sm sm:text-xs lg:text-base">{{ __('landing.terms.sub_2_2') }}</div>
        <div class="terms-content text-sm sm:text-xs lg:text-base">
            <p>
                {{ __('landing.terms.sub_2_2_p') }}
            </p>
        </div>

        <!-- Section 3: Payment Terms -->
        <h2 class="section-title">{{ __('landing.terms.section_3') }}</h2>

        <div class="terms-content text-sm sm:text-xs lg:text-base">
            <ul class="terms-list">
                <li>{{ __('landing.terms.section_3_li_1') }}</li>
                <li>{{ __('landing.terms.section_3_li_2') }}</li>
                <li>{{ __('landing.terms.section_3_li_3') }}</li>
            </ul>
        </div>

        <!-- Section 4: Limitation of Liability -->
        <h2 class="section-title">{{ __('landing.terms.section_4') }}</h2>

        <div class="terms-content text-sm sm:text-xs lg:text-base">
            <p class="mb-3">
                {{ __('landing.terms.section_4_p') }}
            </p>
            <ul class="terms-list text-sm sm:text-xs lg:text-base">
                <li>{{ __('landing.terms.section_4_li_1') }}</li>
                <li>{{ __('landing.terms.section_4_li_2') }}</li>
                <li>{{ __('landing.terms.section_4_li_3') }}</li>
                <li>{{ __('landing.terms.section_4_li_4') }}</li>
            </ul>
        </div>

        <!-- Section 5: Dispute Resolution -->
        <h2 class="section-title">{{ __('landing.terms.section_5') }}</h2>

        <div class="terms-content text-sm sm:text-xs lg:text-base">
            <p>
                {{ __('landing.terms.section_5_p') }}
            </p>
        </div>

        <!-- Section 6: Modifications to Terms -->
        <h2 class="section-title">{{ __('landing.terms.section_6') }}</h2>

        <div class="terms-content text-sm sm:text-xs lg:text-base">
            <p>
                {{ __('landing.terms.section_6_p') }}
            </p>
        </div>

        <!-- Section 7: Contact Information -->
        <h2 class="section-title">{{ __('landing.terms.section_7') }}</h2>

        <div class="terms-content text-sm sm:text-xs lg:text-base">
            <p>
                {{ __('landing.terms.section_7_p') }}
            </p>
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
            document.cookie = 'finnoys_terms_accepted=1; path=/; max-age=' + (30 * 24 * 60 * 60);

            if (fromRecruitment) {
                const redirectUrl = '/recruitment' + (jobId ? '?apply_job=' + jobId : '');
                window.showSuccessDialog('Terms Accepted', 'Thank you for accepting the Terms and Conditions.', 'Continue', redirectUrl);
            } else {
                window.showSuccessDialog('Terms Accepted', 'Thank you for accepting the Terms and Conditions.');
            }
        });

        declineBtn.addEventListener('click', function() {
            if (fromRecruitment) {
                const redirectUrl = '/recruitment' + (jobId ? '?apply_job=' + jobId : '');
                window.showErrorDialog('Terms Required', 'You must accept the Terms and Conditions before applying.', 'Back to Recruitment', redirectUrl);
            } else {
                window.showErrorDialog('Login Failed', 'Please read and accept the terms and conditions.', 'Back to login', '/login');
            }
        });
    });
</script>
@endpush