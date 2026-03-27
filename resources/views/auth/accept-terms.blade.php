<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accept Terms & Conditions</title>
    <link rel="icon" href="{{ asset('images/icons/castcrew/castcrew-pic-logo.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        @import url('{{ asset('app.css') }}');

        .doc-container {
            max-height: 350px;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 24px;
            background: #f9fafb;
        }

        .doc-container::-webkit-scrollbar {
            width: 8px;
        }

        .doc-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .doc-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .doc-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .read-badge {
            display: none;
            align-items: center;
            gap: 4px;
            font-size: 0.75rem;
            color: #16a34a;
            font-weight: 600;
        }

        .read-badge.visible {
            display: inline-flex;
        }

        input[type="checkbox"] {
            appearance: none;
            width: 20px;
            height: 20px;
            border: 1px solid #868282;
            border-radius: 4px;
            position: relative;
            cursor: pointer;
            flex-shrink: 0;
        }

        input[type="checkbox"]:checked {
            background-color: #0077FF;
        }

        input[type="checkbox"]:checked::after {
            content: "\2713";
            color: white;
            font-size: 14px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        input[type="checkbox"]:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                fontFamily: {
                    'sans': ['Familjen Grotesk', 'system-ui', 'sans-serif'],
                }
            }
        }
    </script>
</head>

<body class="font-normal font-sans bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-lg max-w-3xl w-full p-8 md:p-12">
        <div class="text-center mb-8">
            <img src="{{ asset('/images/finnoys-text-logo-light.svg') }}" alt="Fin-noys" class="h-16 mx-auto mb-4">
            <h1 class="text-3xl font-bold text-[#081032] font-sans">Welcome to Castcrew</h1>
            <p class="text-[#07185788] text-sm mt-2">
                Before you continue, please read and accept our Terms & Conditions and Privacy Policy.
            </p>
        </div>

        <!-- Terms and Conditions -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-lg font-bold text-gray-900">Terms & Conditions</h2>
                <span id="terms-badge" class="read-badge">
                    <i class="fa-solid fa-circle-check"></i> Read
                </span>
            </div>
            <div class="doc-container" id="termsContent">
                <div class="space-y-4 text-gray-700">
                    <p class="text-sm text-gray-500 font-medium">Last Updated: November 5, 2025</p>

                    <h3 class="text-base font-bold text-gray-900 mt-4">1. Acceptance of Terms</h3>
                    <p class="text-sm leading-relaxed">
                        By accessing or using the Castcrew workforce management and scheduling platform (the "System"),
                        you ("User") agree to comply with and be bound by these Terms and Conditions. These Terms govern
                        the automated allocation of tasks, scheduling, and employee management functions within the System.
                    </p>
                    <p class="text-sm leading-relaxed">
                        If you do not agree with any part of these Terms, you must refrain from using the System.
                    </p>

                    <h3 class="text-base font-bold text-gray-900 mt-4">2. System Operations and Allocation Rules</h3>

                    <h4 class="text-sm font-semibold text-gray-900 mt-3">2.1 Workforce Allocation</h4>
                    <p class="text-sm leading-relaxed">
                        Castcrew automatically determines the optimal number of employees required for each task based on an analysis of:
                    </p>
                    <ul class="list-disc list-inside text-sm space-y-1 ml-4">
                        <li>Employee availability (excluding those on scheduled leave or days off).</li>
                        <li>Pending workload and task specifications.</li>
                        <li>Budget and resource constraints.</li>
                        <li>Utilization targets designed to maximize operational efficiency.</li>
                    </ul>

                    <h4 class="text-sm font-semibold text-gray-900 mt-3">2.2 Team Composition and Driver Requirement</h4>
                    <p class="text-sm leading-relaxed">
                        For mobility between client locations, each assigned team (pair or trio) must include at least
                        one employee registered in the system as having valid driving skills.
                    </p>

                    <h4 class="text-sm font-semibold text-gray-900 mt-3">2.3 Task Prioritization</h4>
                    <p class="text-sm leading-relaxed">
                        Tasks labeled with an "Arrival Status" are assigned the highest scheduling priority. This
                        designation indicates immediate service needs due to a client or guest arrival and will take
                        precedence over other pending tasks.
                    </p>

                    <h4 class="text-sm font-semibold text-gray-900 mt-3">2.4 Schedule Generation and Re-Optimization</h4>
                    <ul class="list-disc list-inside text-sm space-y-1 ml-4">
                        <li>Schedules are generated automatically by the System.</li>
                        <li>If a new task is added before a daily schedule is officially saved and finalized, the System
                            will delete the unsaved version and generate a new optimized schedule that includes the new task.</li>
                        <li>Re-optimization occurs independently within each client or company entity.</li>
                    </ul>

                    <h4 class="text-sm font-semibold text-gray-900 mt-3">2.5 Employee Utilization and Fair Distribution</h4>
                    <p class="text-sm leading-relaxed">The System is designed to:</p>
                    <ul class="list-disc list-inside text-sm space-y-1 ml-4">
                        <li>Maximize employee utilization by assigning all available employees to at least one task whenever possible.</li>
                        <li>Ensure equitable task distribution among available staff to maintain a balanced workload.</li>
                    </ul>

                    <h4 class="text-sm font-semibold text-gray-900 mt-3">2.6 Working Hours Compliance</h4>
                    <p class="text-sm leading-relaxed">
                        To comply with Finnish labor standards, the System enforces a maximum of 12 working hours per
                        day for any individual employee. This limit is applied automatically during scheduling and task allocation.
                    </p>

                    <h4 class="text-sm font-semibold text-gray-900 mt-3">2.7 Real-Time Task Additions</h4>
                    <p class="text-sm leading-relaxed">For the current operational day:</p>
                    <ul class="list-disc list-inside text-sm space-y-1 ml-4">
                        <li>Once a schedule is finalized, adding a new task will not modify existing team assignments.</li>
                        <li>The System will only allocate the newly added task, assigning it to one of the existing teams without altering prior allocations.</li>
                    </ul>

                    <h4 class="text-sm font-semibold text-gray-900 mt-3">2.8 Team Stability and Contingency Scenarios</h4>
                    <ul class="list-disc list-inside text-sm space-y-1 ml-4">
                        <li>Once teams are formed for a given day, their composition remains stable under normal circumstances.</li>
                        <li>In the event of a "What-If" scenario (e.g., an employee's sudden absence or sickness), the System will automatically reassign or supplement team members as needed.</li>
                        <li>All teams are required to consist of two (2) or three (3) members. No team will exceed three members under any condition.</li>
                    </ul>

                    <h3 class="text-base font-bold text-gray-900 mt-4">3. System Authority and Finality</h3>
                    <p class="text-sm leading-relaxed">
                        You acknowledge that all task assignments and schedules are the outcome of automated, rule-based
                        optimization performed by Castcrew. The System's allocations are deemed final and binding for
                        operational purposes, except in cases involving verified technical errors or the invocation of a "What-If" scenario.
                    </p>

                    <h3 class="text-base font-bold text-gray-900 mt-4">4. Modifications to System Rules</h3>
                    <p class="text-sm leading-relaxed">
                        Castcrew and its parent company, Fin-noys, reserve the right to modify or update these Terms and
                        operational rules at any time to reflect evolving business or regulatory requirements. Continued
                        use of the System following any such updates constitutes your acceptance of the revised Terms.
                    </p>

                    <h3 class="text-base font-bold text-gray-900 mt-4">5. Contact Information</h3>
                    <p class="text-sm leading-relaxed">
                        For inquiries or clarifications regarding these Terms & Conditions, please contact:
                    </p>
                    <div class="bg-white p-4 rounded-lg mt-2 border">
                        <p class="text-sm font-semibold text-gray-900">Castcrew Support</p>
                        <p class="text-sm text-gray-700 mt-1">castcrewhelpcenter@gmail.com</p>
                        <p class="text-sm text-gray-700">Philippines, Makati City</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Privacy Policy -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-lg font-bold text-gray-900">Privacy Policy</h2>
                <span id="privacy-badge" class="read-badge">
                    <i class="fa-solid fa-circle-check"></i> Read
                </span>
            </div>
            <div class="doc-container" id="privacyContent">
                <div class="space-y-4 text-gray-700">
                    <p class="text-sm text-gray-500">Last updated: January 2024</p>

                    <h3 class="text-base font-semibold text-gray-900 mt-4">1. Information We Collect</h3>
                    <p class="text-sm leading-relaxed">
                        We collect information you provide directly to us, including your name, email address, phone
                        number, payment information, and service preferences. We also collect information about your use of our services.
                    </p>

                    <h3 class="text-base font-semibold text-gray-900 mt-4">2. How We Use Your Information</h3>
                    <p class="text-sm leading-relaxed">
                        We use the information we collect to provide, maintain, and improve our services, to process
                        your bookings, to communicate with you, and to personalize your experience.
                    </p>

                    <h3 class="text-base font-semibold text-gray-900 mt-4">3. Information Sharing</h3>
                    <p class="text-sm leading-relaxed">
                        We do not sell or rent your personal information to third parties. We may share your information
                        with service providers who assist us in operating our platform and providing services to you.
                    </p>

                    <h3 class="text-base font-semibold text-gray-900 mt-4">4. Data Security</h3>
                    <p class="text-sm leading-relaxed">
                        We implement appropriate technical and organizational measures to protect your personal
                        information against unauthorized access, alteration, disclosure, or destruction.
                    </p>

                    <h3 class="text-base font-semibold text-gray-900 mt-4">5. Your Rights</h3>
                    <p class="text-sm leading-relaxed">
                        You have the right to access, update, or delete your personal information. You may also opt-out
                        of receiving promotional communications from us at any time.
                    </p>

                    <h3 class="text-base font-semibold text-gray-900 mt-4">6. Cookies and Tracking</h3>
                    <p class="text-sm leading-relaxed">
                        We use cookies and similar tracking technologies to collect information about your browsing
                        activities and to improve our services. You can control cookies through your browser settings.
                    </p>

                    <h3 class="text-base font-semibold text-gray-900 mt-4">7. Contact Us</h3>
                    <p class="text-sm leading-relaxed">
                        If you have any questions about this Privacy Policy, please contact us at privacy@finnoys.com.
                    </p>
                </div>
            </div>
        </div>

        <!-- Acceptance Form -->
        <form action="{{ route('terms.store') }}" method="POST" id="accept-form">
            @csrf

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p class="text-sm text-red-600">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="relative mb-6 group/tip">
                <label class="flex items-start space-x-3 cursor-pointer">
                    <input type="checkbox" name="terms" id="terms-checkbox" disabled>
                    <span class="text-sm text-gray-600 text-justify">
                        I have read and understood the <strong>Terms & Conditions</strong> and <strong>Privacy Policy</strong>,
                        and I agree to be bound by them.
                    </span>
                </label>
                <div id="terms-tooltip" class="invisible group-hover/tip:visible opacity-0 group-hover/tip:opacity-100 transition-opacity duration-200 absolute left-1/2 -translate-x-1/2 -bottom-10 px-3 py-1.5 bg-gray-800 text-white text-xs rounded-lg whitespace-nowrap shadow-lg z-10">
                    You have to read the Terms & Conditions and Privacy Policy to proceed
                    <div class="absolute left-1/2 -translate-x-1/2 -top-1 w-2 h-2 bg-gray-800 rotate-45"></div>
                </div>
            </div>

        </form>

        <div class="flex flex-row items-center justify-center gap-4 mt-6">
            <button type="submit" form="accept-form" id="btn-accept"
                class="px-8 py-3 bg-[#0077FF] text-white rounded-full font-semibold text-sm hover:bg-blue-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                disabled>
                Accept & Continue
            </button>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="px-8 py-3 border border-gray-300 text-gray-600 rounded-full font-semibold text-sm hover:bg-gray-100 transition-colors">
                    Log out
                </button>
            </form>
        </div>
    </div>

    <script>
        let termsRead = false;
        let privacyRead = false;

        function updateAcceptButton() {
            const checkbox = document.getElementById('terms-checkbox');
            if (termsRead && privacyRead) {
                checkbox.disabled = false;
                var tooltip = document.getElementById('terms-tooltip');
                if (tooltip) tooltip.remove();
            }
        }

        function setupScrollTracking(containerId, badgeId, onComplete) {
            const container = document.getElementById(containerId);
            if (!container) return;

            // Check if content is short enough that no scrolling is needed
            if (container.scrollHeight <= container.clientHeight + 20) {
                onComplete();
                document.getElementById(badgeId).classList.add('visible');
                return;
            }

            container.addEventListener('scroll', function () {
                const atBottom = container.scrollHeight - container.scrollTop - container.clientHeight < 20;
                if (atBottom) {
                    onComplete();
                    document.getElementById(badgeId).classList.add('visible');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            setupScrollTracking('termsContent', 'terms-badge', function () {
                termsRead = true;
                updateAcceptButton();
            });
            setupScrollTracking('privacyContent', 'privacy-badge', function () {
                privacyRead = true;
                updateAcceptButton();
            });

            // Enable submit button when checkbox is checked
            const checkbox = document.getElementById('terms-checkbox');
            const btn = document.getElementById('btn-accept');
            checkbox.addEventListener('change', function () {
                btn.disabled = !this.checked;
            });
        });
    </script>
</body>

</html>
