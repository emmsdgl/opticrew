@extends('components.layouts.general-landing')

@section('title', 'Privacy Policy')

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
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .dark .terms-title {
        color: #f9fafb;
    }

    .last-updated {
        color: #6b7280;
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
    }

    .dark .last-updated {
        color: #9ca3af;
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
        font-size: 0.95rem;
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
        gap: 1rem;
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 1px solid #e5e7eb;
    }

    .dark .action-buttons {
        border-top-color: #374151;
    }

    .btn-agree {
        background: #2563eb;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 0.5rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-agree:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .btn-decline {
        background: transparent;
        color: #6b7280;
        padding: 0.75rem 2rem;
        border-radius: 0.5rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
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
        <p class="agreement-label">PRIVACY</p>
        <h1 class="terms-title text-4xl">Privacy Policy</h1>
        <p class="last-updated">Last Updated: January 26, 2026</p>
        
        <div class="terms-intro">
            <p>
                This Privacy Policy describes how our Workforce Management & Booking System collects, uses, and 
                protects the information of our Employers, Employees, and Clients. By using this platform, you 
                agree to the data practices described in this policy.
            </p>
        </div>

        <!-- Section 1: Information We Collect -->
        <h2 class="section-title">1. Information We Collect</h2>
        
        <div class="terms-content">
            <p class="mb-3">
                We collect information to provide a seamless cleaning service experience and to ensure efficient 
                workforce management.
            </p>
            <ul class="terms-list">
                <li>
                    <strong>For Clients:</strong> Name, business/home address, billing details, contact information, 
                    and service history (as required for Service Request Management).
                </li>
                <li>
                    <strong>For Employees:</strong> Personal details, contact information, professional qualifications, 
                    and Skill Tags (as required for Skill-Based Matching).
                </li>
                <li>
                    <strong>For Job Applicants:</strong> Resumes, interview schedules, and application statuses.
                </li>
                <li>
                    <strong>Automated Data:</strong> We collect Geolocation Data from employees only during active 
                    shifts via the Attendance Tracker to verify presence at geofenced job sites.
                </li>
            </ul>
        </div>

        <!-- Section 2: How We Use Your Information -->
        <h2 class="section-title">2. How We Use Your Information</h2>
        
        <div class="terms-content">
            <p class="mb-3">
                The data collected is used strictly for the operational needs of the cleaning company:
            </p>
            <ul class="terms-list">
                <li>
                    <strong>Service Fulfillment:</strong> Matching the right employee to a client based on skills 
                    and location.
                </li>
                <li>
                    <strong>Performance Tracking:</strong> Monitoring task completion rates and Customer Satisfaction 
                    (CSAT) scores.
                </li>
                <li>
                    <strong>Payroll & Attendance:</strong> Using clock-in/out data to calculate salaries, Sunday 
                    premiums, and holiday pay.
                </li>
                <li>
                    <strong>Safety & Compliance:</strong> Using geofencing to ensure staff safety and service 
                    verification at client premises.
                </li>
                <li>
                    <strong>Communication:</strong> Sending real-time notifications regarding schedule changes, 
                    task approvals, or system updates.
                </li>
            </ul>
        </div>

        <!-- Section 3: Data Storage and Security -->
        <h2 class="section-title">3. Data Storage and Security</h2>
        
        <div class="terms-content">
            <p class="mb-3">
                In accordance with our system's General Account Management protocols:
            </p>
            <ul class="terms-list">
                <li>
                    All account information (passwords, login credentials, and billing data) is saved securely 
                    and encrypted.
                </li>
                <li>
                    All changes to profile information are validated before being reflected across the system.
                </li>
                <li>
                    We implement industry-standard firewalls and security layers to prevent unauthorized access 
                    to workforce and client records.
                </li>
            </ul>
        </div>

        <!-- Section 4: Data Sharing and Disclosure -->
        <h2 class="section-title">4. Data Sharing and Disclosure</h2>
        
        <div class="terms-content">
            <ul class="terms-list">
                <li>
                    <strong>Third Parties:</strong> We do not sell, rent, or lease our user lists to third parties.
                </li>
                <li>
                    <strong>Service Transparency:</strong> A client will be able to see the name, profile picture, 
                    and performance rating of the employee assigned to their task.
                </li>
                <li>
                    <strong>Legal Requirements:</strong> We may disclose information if required by law to comply 
                    with legal processes or protect the rights and safety of the company and its users.
                </li>
            </ul>
        </div>

        <!-- Section 5: User Rights and Controls -->
        <h2 class="section-title">5. User Rights and Controls</h2>
        
        <div class="terms-content">
            <p class="mb-3">
                Users have the following rights within the platform:
            </p>
            <ul class="terms-list">
                <li>
                    <strong>Access & Update:</strong> Employees and Clients can update their contact details and 
                    addresses via their respective Account Management dashboards.
                </li>
                <li>
                    <strong>Notifications:</strong> Users can manage their alert preferences for system updates.
                </li>
                <li>
                    <strong>Data Portability:</strong> Employers can export performance analytics and payroll data 
                    in downloadable formats.
                </li>
            </ul>
        </div>

        <!-- Section 6: Geolocation Tracking Policy -->
        <h2 class="section-title">6. Geolocation Tracking Policy</h2>
        
        <div class="terms-content">
            <p>
                Employee geolocation is tracked only for the duration of an assigned task. Tracking begins upon 
                "Clock In" and ends upon "Clock Out." This data is used solely to verify that services are being 
                performed at the client's designated location as per the Geofencing requirement in the test plan.
            </p>
        </div>

        <!-- Section 7: Changes to this Policy -->
        <h2 class="section-title">7. Changes to this Policy</h2>
        
        <div class="terms-content">
            <p>
                The company reserves the right to update this Privacy Policy to reflect system changes or legal 
                requirements. Users will be notified of significant changes via the System Notifications panel 
                on their dashboard.
            </p>
        </div>

        <!-- Section 8: Contact Information -->
        <h2 class="section-title">8. Contact Information</h2>
        
        <div class="terms-content">
            <p>
                If you have questions regarding this Privacy Policy or your data, please contact the System 
                Administrator through the Help Center or the provided contact details in your Account Management tab.
            </p>
        </div>

        <div class="action-buttons">
            <button class="btn-decline font-medium text-sm">Disagree</button>
            <button class="btn-agree font-medium text-sm">I agree with the policy</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const agreeBtn = document.querySelector('.btn-agree');
        const declineBtn = document.querySelector('.btn-decline');

        agreeBtn.addEventListener('click', function() {
            alert('Thank you for accepting the Privacy Policy');
        });

        declineBtn.addEventListener('click', function() {
            if(confirm('You must accept the Privacy Policy to continue using the platform.')) {
            }
        });
    });
</script>
@endpush