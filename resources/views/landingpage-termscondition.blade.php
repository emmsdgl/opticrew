@extends('components.layouts.general-landing')

@section('title', 'Terms and Conditions')

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
        <p class="agreement-label">AGREEMENT</p>
        <h1 class="terms-title text-4xl">Terms and Conditions</h1>
        
        <div class="terms-intro">
            <p class="mb-4 text-justify">
                These Terms and Conditions govern the relationship between the Service Provider (Fin-noys), 
                the Employees, and the Clients. By using our Workforce Management & Booking System, CastCrew, for Fin-noys, you agree to 
                comply with and be bound by the following terms.
            </p>
        </div>

        <!-- Section 1: Service Agreement -->
        <h2 class="section-title">1. Service Agreement</h2>
        
        <div class="subsection-title">1.1 For Clients</div>
        <div class="terms-content">
            <p class="mb-3">
                By using the Service Booking feature, clients agree to provide accurate location 
                data and access to premises.
            </p>
            <p>
                <strong>Provisional Billing:</strong> Quotations generated via the Provisional Billing
                tool are estimates based on client-provided data. Final costs may adjust if the on-site workload exceeds 
                the initial request.
            </p>
        </div>

        <div class="subsection-title">1.2 Employee Obligations</div>
        <ul class="terms-list">
            <li>
                <strong>Task Execution:</strong> Employees must adhere to the Cleaning Checklists 
                and time allocations provided by the system.
            </li>
            <li>
                <strong>Attendance:</strong> Employees agree to use the Geofenced Attendance Tracker. Failure to 
                clock in within the geofenced perimeter may result in unrecorded hours.
            </li>
        </ul>

        <div class="subsection-title">1.3 Cancellation & Rescheduling</div>
        <div class="terms-content">
            <p>
                Cancellations must be made via the Service Schedule at least 24 hours in advance 
                to avoid a cancellation fee.
            </p>
        </div>

        <!-- Section 2: User Responsibilities -->
        <h2 class="section-title">2. User Responsibilities</h2>
        
        <div class="subsection-title">2.1 Account Security</div>
        <div class="terms-content">
            <p>
                Users are responsible for maintaining the confidentiality of their account credentials. Any activities 
                that occur under your account are your responsibility.
            </p>
        </div>

        <div class="subsection-title">2.2 Accurate Information</div>
        <div class="terms-content">
            <p>
                All users agree to provide accurate and up-to-date information when using the platform. Inaccurate 
                information may result in service disruptions or account suspension.
            </p>
        </div>

        <!-- Section 3: Payment Terms -->
        <h2 class="section-title">3. Payment Terms</h2>
        
        <div class="terms-content">
            <ul class="terms-list">
                <li>Payment for services must be completed within the specified timeframe as outlined in the service agreement.</li>
                <li>Late payments may incur additional fees as determined by the company's billing policies.</li>
                <li>All prices are subject to change with prior notice to clients.</li>
            </ul>
        </div>

        <!-- Section 4: Limitation of Liability -->
        <h2 class="section-title">4. Limitation of Liability</h2>
        
        <div class="terms-content">
            <p class="mb-3">
                The Service Provider shall not be held liable for any damages arising from:
            </p>
            <ul class="terms-list">
                <li>Unauthorized access to or use of our servers and/or personal information stored therein</li>
                <li>Interruption or cessation of transmission to or from the platform</li>
                <li>Bugs, viruses, or similar items that may be transmitted through the platform by third parties</li>
                <li>Errors or omissions in content or for any loss or damage incurred from the use of content posted via the platform</li>
            </ul>
        </div>

        <!-- Section 5: Dispute Resolution -->
        <h2 class="section-title">5. Dispute Resolution</h2>
        
        <div class="terms-content">
            <p>
                In case of problems or disagreements, users are encouraged to first contact the System Administrator 
                through the Help Center. If disputes cannot be resolved through direct communication, parties agree to 
                seek mediation before pursuing legal action.
            </p>
        </div>

        <!-- Section 6: Modifications to Terms -->
        <h2 class="section-title">6. Modifications to Terms</h2>
        
        <div class="terms-content">
            <p>
                The company reserves the right to modify these Terms and Conditions at any time. Users will be notified 
                of significant changes via the System Notifications panel on their dashboard. Continued use of the platform 
                after such modifications constitutes acceptance of the updated terms.
            </p>
        </div>

        <!-- Section 7: Contact Information -->
        <h2 class="section-title">7. Contact Information</h2>
        
        <div class="terms-content">
            <p>
                If you have questions regarding these Terms and Conditions, please contact the System Administrator 
                through the Help Center or the provided contact details in your Account Management tab.
            </p>
        </div>

        <div class="action-buttons">
            <button class="btn-decline font-medium text-sm">Disagree</button>
            <button class="btn-agree font-medium text-sm">I agree with the terms</button>
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
            alert('Thank you for accepting the Terms and Conditions');
            // ADD VERIFICATION LOGIC HERE
        });
        
        declineBtn.addEventListener('click', function() {
            if(confirm('You must accept the Terms and Conditions to continue using the platform.')) {
                // ADD DISAGREEMENT CONSEQUENCE LOGIC HERE
            }
        });
    });
</script>
@endpush