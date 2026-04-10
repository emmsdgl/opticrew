@extends('components.layouts.general-landing')

@section('title', 'Privacy Policy — Finnoys Cleaning Services')

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

    .info-box {
        background-color: #f0f4fa;
        border-left: 3px solid #2563eb;
        border-radius: 0.375rem;
        padding: 0.625rem 0.875rem;
        margin-bottom: 0.5rem;
        font-size: 0.75rem;
        line-height: 1.6;
    }

    @media (min-width: 640px) {
        .info-box { font-size: 0.8125rem; padding: 0.75rem 1rem; }
    }

    @media (min-width: 1024px) {
        .info-box { font-size: 0.95rem; }
    }

    .dark .info-box {
        background-color: #1e3a5f;
        border-left-color: #3b82f6;
    }

    .info-box strong {
        color: #1d4ed8;
    }

    .dark .info-box strong {
        color: #60a5fa;
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

    .policy-footer-note {
        text-align: center;
        border-top: 1px solid #e5e7eb;
        padding-top: 1.25rem;
        margin-top: 2rem;
        color: #9ca3af;
        font-size: 0.7rem;
        font-style: italic;
        line-height: 1.6;
    }

    .dark .policy-footer-note {
        border-top-color: #374151;
        color: #6b7280;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="terms-container">

        <p class="agreement-label">Legal Document</p>
        <h1 class="terms-title text-4xl sm:text-5xl lg:text-6xl">Privacy Policy</h1>
        <p class="last-updated">Last Updated: January 2024 &nbsp;|&nbsp; Effective Date: January 2024 &nbsp;|&nbsp; GDPR-Compliant Version</p>

        <!-- Controller Info Boxes -->
        <div class="mb-4">
            <div class="info-box"><strong>Data Controller:</strong> Finnoys Cleaning Services</div>
            <div class="info-box"><strong>Contact:</strong> privacy@finnoys.com</div>
            <div class="info-box"><strong>Supervisory Authority:</strong> Your national data protection authority (e.g., ICO, CNIL, BfDI)</div>
            <div class="info-box"><strong>Data Protection Officer:</strong> Contact us at privacy@finnoys.com to reach our Data Protection Officer</div>
        </div>

        <div class="terms-intro">
            <p class="mb-4 text-sm sm:text-xs lg:text-base text-justify">
                Finnoys Cleaning Services ("we", "us", or "our") is committed to protecting your personal data in full compliance with Regulation (EU) 2016/679 — the General Data Protection Regulation (GDPR). This Privacy Policy applies to all personal data we collect when you use our services, visit our website, or communicate with us. It is provided in a concise, transparent, and intelligible form in accordance with Articles 13 and 14 GDPR.
            </p>
        </div>

        <!-- Section 1: Information We Collect -->
        <h2 class="section-title">1. Information We Collect</h2>

        <div class="terms-content">
            <p class="mb-3">
                In accordance with the data minimisation principle (Article 5(1)(c) GDPR), we only collect personal data that is adequate, relevant, and limited to what is necessary. This includes:
            </p>
            <ul class="terms-list">
                <li>
                    <strong>Identity &amp; Contact Data:</strong> Full name, email address, phone number, and postal address provided directly by you.
                </li>
                <li>
                    <strong>Payment &amp; Billing Information:</strong> Payment details processed securely via PCI-DSS compliant third-party processors. We do not store raw card data.
                </li>
                <li>
                    <strong>Service Preferences &amp; Booking Details:</strong> Information related to the services you request, scheduling preferences, and booking history.
                </li>
                <li>
                    <strong>Technical &amp; Usage Data:</strong> IP address, browser and device information, website usage data, and cookie identifiers — collected automatically and only where consent has been obtained for non-essential purposes.
                </li>
            </ul>
            <p class="mb-3">
                We do not intentionally collect or process special categories of personal data as defined in Article 9 GDPR (e.g., health data, racial or ethnic origin). If such data is incidentally shared with us, it will be deleted promptly.
            </p>
        </div>

        <!-- Section 2: How We Use Your Information -->
        <h2 class="section-title">2. How We Use Your Information</h2>

        <div class="terms-content">
            <p class="mb-3">
                Your personal data is processed for the following specific and explicit purposes (Article 5(1)(b) GDPR). We will not process your data for any incompatible purpose without prior notice and, where required, your consent.
            </p>
            <ul class="terms-list">
                <li>
                    <strong>Service Delivery (Art. 6(1)(b)):</strong> To provide, manage, and deliver our cleaning services, including processing and confirming bookings.
                </li>
                <li>
                    <strong>Payments (Art. 6(1)(b)):</strong> To process transactions, issue invoices, and manage billing securely.
                </li>
                <li>
                    <strong>Communications (Art. 6(1)(b)):</strong> To send booking confirmations, receipts, service updates, and respond to your enquiries.
                </li>
                <li>
                    <strong>Legal Compliance (Art. 6(1)(c)):</strong> To fulfil our obligations under applicable tax, accounting, employment, and regulatory law.
                </li>
                <li>
                    <strong>Marketing (Art. 6(1)(a)):</strong> To send promotional communications — only where you have provided prior, freely given, and specific consent. You may withdraw consent at any time.
                </li>
            </ul>
        </div>

        <!-- Section 3: Data Storage and Security -->
        <h2 class="section-title">3. Data Storage and Security</h2>

        <div class="terms-content">
            <p class="mb-3">
                We implement appropriate technical and organisational security measures to protect your personal data against unauthorised access, alteration, disclosure, or destruction, in accordance with Article 32 GDPR.
            </p>
            <ul class="terms-list">
                <li>TLS/SSL encryption for all data transmitted over the internet, and encryption of personal data at rest using industry-standard algorithms.</li>
                <li>Role-based access control, limiting data access to authorised personnel only, supported by regular staff training on data protection.</li>
                <li>Incident response procedures compliant with Article 33 GDPR — in the event of a personal data breach posing a risk to your rights and freedoms, we will notify the relevant supervisory authority within 72 hours and affected individuals without undue delay.</li>
            </ul>
        </div>

        <!-- Section 4: Data Sharing and Disclosure -->
        <h2 class="section-title">4. Data Sharing and Disclosure</h2>

        <div class="terms-content">
            <p class="mb-3">We do not sell, rent, or trade your personal data. We may share it only in the following circumstances:</p>
            <ul class="terms-list">
                <li>
                    <strong>Authorised Data Processors (Art. 28 GDPR):</strong> Third-party service providers (e.g., payment processors, cloud hosting, email platforms, analytics) acting under written Data Processing Agreements with strict confidentiality and security obligations.
                </li>
                <li>
                    <strong>Legal Disclosures (Art. 6(1)(c)):</strong> When required by applicable law, court order, or to establish, exercise, or defend legal claims.
                </li>
                <li>
                    <strong>Business Transfers:</strong> In the event of a merger, acquisition, or sale of assets, we will notify you before any transfer occurs and you will retain the right to object.
                </li>
            </ul>
        </div>

        <!-- Section 5: User Rights and Controls -->
        <h2 class="section-title">5. Your Rights Under GDPR (Articles 15–22)</h2>

        <div class="terms-content">
            <p class="mb-3">
                As a data subject, you have the following rights. We will honour all requests without undue delay and within one month of receipt (extendable to three months for complex requests). Requests are handled free of charge unless manifestly unfounded or excessive.
            </p>
            <ul class="terms-list">
                <li>
                    <strong>Right of Access (Art. 15):</strong> Request a copy of all personal data we hold about you, along with details of how it is processed.
                </li>
                <li>
                    <strong>Right to Rectification (Art. 16):</strong> Request correction of inaccurate or incomplete personal data without undue delay.
                </li>
                <li>
                    <strong>Right to Erasure / Right to be Forgotten (Art. 17):</strong> Request deletion of your data where it is no longer necessary, consent is withdrawn, or processing is unlawful — subject to legal retention obligations.
                </li>
                <li>
                    <strong>Right to Restriction &amp; Right to Object (Arts. 18 &amp; 21):</strong> Request temporary restriction of processing, or object at any time to processing based on legitimate interests, including profiling.
                </li>
                <li>
                    <strong>Right to Data Portability (Art. 20):</strong> Receive your personal data in a structured, machine-readable format (e.g., JSON/CSV) and request its transfer to another controller where technically feasible.
                </li>
                <li>
                    <strong>Right to Withdraw Consent (Art. 7(3)):</strong> Withdraw consent at any time without affecting the lawfulness of prior processing, via the unsubscribe link in emails or by contacting us directly.
                </li>
                <li>
                    <strong>Right to Lodge a Complaint:</strong> If you believe your rights have been violated, you may lodge a complaint with your national supervisory authority (e.g., ICO in the UK). We encourage you to contact us first so we may address your concern directly.
                </li>
            </ul>
            <p>To exercise any of these rights, please submit a written request to <strong>privacy@finnoys.com</strong>. We may request proof of identity to verify your request.</p>
        </div>

        <!-- Section 6: Geolocation Tracking Policy -->
        <h2 class="section-title">6. Geolocation Data</h2>

        <div class="terms-content">
            <p>
                Where our services involve geolocation tracking (e.g., for service routing or field staff management), such data is collected only where explicit consent has been obtained or where it is strictly necessary for the performance of your service agreement. Geolocation data is processed with the same security standards as other personal data and is not used for any purpose beyond that for which it was collected. You may withdraw consent for geolocation tracking at any time by contacting us at privacy@finnoys.com.
            </p>
        </div>

        <!-- Section 7: Data Retention -->
        <h2 class="section-title">7. Data Retention</h2>

        <div class="terms-content">
            <p class="mb-3">
                We retain personal data only for as long as necessary to fulfil the purposes for which it was collected, in accordance with the storage limitation principle (Article 5(1)(e) GDPR). Default retention periods are:
            </p>
            <ul class="terms-list">
                <li>Service and contract data: up to 6 years after the end of the contract to comply with legal obligations.</li>
                <li>Accounting and invoicing records: a minimum of 7 years under applicable financial regulations.</li>
                <li>Marketing data: until you withdraw consent or object to processing.</li>
                <li>Cookie and analytics data: up to 13 months, or in line with your consent preferences.</li>
            </ul>
            <p>Once the applicable retention period expires, personal data is securely deleted or anonymised in a manner that prevents re-identification.</p>
        </div>

        <!-- Section 8: Cookies and Tracking -->
        <h2 class="section-title">8. Cookies and Tracking Technologies</h2>

        <div class="terms-content">
            <p class="mb-3">
                We use cookies and similar tracking technologies in accordance with the ePrivacy Directive and GDPR. Upon your first visit, you will be presented with a Cookie Consent Banner. Non-essential cookies are only stored with your prior consent, which you may update at any time via our Cookie Settings page.
            </p>
            <ul class="terms-list">
                <li><strong>Strictly Necessary Cookies:</strong> Essential for site operation — no consent required.</li>
                <li><strong>Functional Cookies:</strong> Enhance usability and remember your preferences — consent required.</li>
                <li><strong>Analytics Cookies:</strong> Help us understand site usage — consent required.</li>
                <li><strong>Marketing Cookies:</strong> Deliver relevant communications — explicit prior consent required.</li>
            </ul>
        </div>

        <!-- Section 9: European Data Privacy (GDPR Compliance) -->
        <h2 class="section-title">9. International Data Transfers and Legal Bases</h2>

        <div class="terms-content">
            <p class="mb-3">
                We primarily store and process data within the European Economic Area (EEA). Where transfers to third countries are necessary, we ensure an adequate level of protection through the following safeguards (Chapter V GDPR):
            </p>
            <ul class="terms-list">
                <li>
                    <strong>Adequacy Decision (Art. 45):</strong> Transfer to a country recognised by the European Commission as providing adequate data protection.
                </li>
                <li>
                    <strong>Standard Contractual Clauses (Art. 46(2)(c)):</strong> We enter into EU Commission-approved Standard Contractual Clauses (SCCs) with all third-country recipients.
                </li>
                <li>
                    <strong>Contractual Necessity (Art. 6(1)(b)):</strong> Processing necessary to perform your service agreement.
                </li>
                <li>
                    <strong>Legal Obligation (Art. 6(1)(c)):</strong> Processing required to comply with applicable law.
                </li>
                <li>
                    <strong>Legitimate Interests (Art. 6(1)(f)):</strong> Processing for fraud prevention, security, and service improvement, subject to a Legitimate Interest Assessment (LIA) — available upon request.
                </li>
                <li>
                    <strong>Consent (Art. 6(1)(a)):</strong> For marketing, non-essential cookies, and any processing not otherwise covered, we rely on your freely given, informed, and unambiguous consent.
                </li>
                <li>
                    <strong>Data Protection by Design &amp; Default (Art. 25):</strong> Privacy principles are embedded into our systems from the outset. Data Protection Impact Assessments (DPIAs) are conducted for high-risk processing activities.
                </li>
            </ul>
            <p>You may request a copy of the specific safeguards applicable to your data by contacting <strong>privacy@finnoys.com</strong>.</p>
        </div>

        <!-- Section 10: Changes to This Policy -->
        <h2 class="section-title">10. Changes to This Policy</h2>

        <div class="terms-content">
            <p>
                We may update this Privacy Policy from time to time to reflect changes in our practices or applicable law. We will notify you of material changes by email or via a prominent notice on our website at least 30 days before the changes take effect. Where required by law, we will seek renewed consent for any significant change in processing purposes. The date at the top of this policy reflects the date of the most recent revision.
            </p>
        </div>

        <!-- Section 11: Contact Information -->
        <h2 class="section-title">11. Contact Us</h2>

        <div class="terms-content">
            <p class="mb-3">For any questions, concerns, or to exercise your data subject rights, please contact us:</p>
            <div class="info-box"><strong>Data Controller:</strong> Finnoys Cleaning Services</div>
            <div class="info-box"><strong>Data Protection Email:</strong> privacy@finnoys.com</div>
            <div class="info-box"><strong>Postal Address:</strong> [Insert registered business address]</div>
            <div class="info-box"><strong>Data Protection Officer:</strong> Available upon request at privacy@finnoys.com</div>
            <p class="mt-3">We aim to respond to all data subject requests within 30 days. If your request is complex, we may extend this by a further two months and will inform you accordingly.</p>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button class="btn-decline">Decline</button>
            <button class="btn-agree">I Agree</button>
        </div>

        <!-- Footer note -->
        <div class="policy-footer-note">
            <p>This document constitutes the official Privacy Policy of Finnoys Cleaning Services.</p>
            <p>Prepared in accordance with Regulation (EU) 2016/679 (General Data Protection Regulation).</p>
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