<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/datepicker.min.js"></script>
    <script src="https://unpkg.com/flowbite@2.5.1/dist/flowbite.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        @import url('{{ asset('app.css') }}');

        #container-2 {
            display: flex;
            flex-direction: column;
        }

        /* Floating label container */
        .input-group {
            position: relative;
            width: 100%;
            margin-bottom: 1.5rem;
        }

        .input-group input {
            width: 100%;
            padding: 1.2em 1em 0.6em 2.5em;
            border-radius: 8px;
            outline: none;
            border: 1px solid transparent;
            transition: border-color 0.2s ease;
        }

        .input-group input:focus {
            border-color: #0077FF;
        }

        .input-group label {
            position: absolute;
            top: 1.1em;
            left: 2.5em;
            pointer-events: none;
            transition: all 0.2s ease;
            background-color: transparent;
            padding: 0 0.3em;
        }

        .input-group input:focus+label,
        .input-group input.not-empty+label {
            top: -0.6em;
            left: 2.3em;
            font-size: 0.75rem;
            background-color: white;
            color: #0077FF;
        }

        /* ICONS */
        .input-group .icon {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #0077FF;
        }

        #container-2-layer {
            display: flex;
            justify-content: space-between;
            padding-top: 1em;
            padding-bottom: 1em;
        }

        #btn-login {
            width: 100%;
            padding: 1em;
            background: #0077FF;
            color: white;
            border-radius: 25px;
            cursor: pointer;
        }

        /* Checkbox styling */
        input[type="checkbox"] {
            appearance: none;
            width: 20px;
            height: 20px;
            border: 1px solid #868282;
            border-radius: 4px;
            position: relative;
            cursor: pointer;
        }

        input[type="checkbox"]:checked {
            background-color: #0077FF;
        }

        input[type="checkbox"]:checked::after {
            content: "✓";
            color: white;
            font-size: 14px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        #terms-container input[type="checkbox"] {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
            margin-top: 2px;
        }

        #terms-container span {
            text-align: justify;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            border-radius: 16px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow: hidden;
            animation: slideIn 0.3s ease;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 24px;
            max-height: 60vh;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Scrollbar styling for modal body */
        .modal-body::-webkit-scrollbar {
            width: 8px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #555;
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

<body class="font-normal font-sans">
    <div class="flex flex-col md:flex-row min-h-screen max-h-screen w-full h-full">

        <!-- INTERACTIVE PICTURE START -->
        <div class="container-1 w-full md:w-1/2 flex flex-col justify-center m-12 mr-1">
            <div id="container-1-2"
                class="p-6 h-full flex flex-col justify-center items-center rounded-3xl bg-cover bg-no-repeat bg-center lg:block lg:flex md:block md:flex hidden"
                style="background-image: url('{{ asset('images/backgrounds/login_bg2.svg') }}');">

                <h1 id="header1" class="text-5xl font-sans font-bold text-white mb-4 text-left w-2/3">
                    One-stop booking for
                    <span class="text-[#0077FF] font-sans italic" id="spotless_text">a spotless space</span>
                </h1>

                <p id="desc1" class="text-[#688bff89] text-opacity-40 text-base text-left mt-3 w-2/3 ">
                    Finnoys is a cleaning agency catering your cleaning needs with its offered quality cleaning
                    services.
                </p>
            </div>
        </div>

        <!-- LOG IN CONTENTS -->
        <div id="container-2"
            class="w-full h-screen md:w-1/2 flex justify-center align-items-center items-center pt-12">
            <form action="{{ route('login') }}" method="POST" class="space-y-4 w-1/2 h-fit">
                @csrf

                <div id="container-2-1" class="flex flex-col items-start mb-12 w-full">
                    <img src="{{asset('/images/finnoys-text-logo-light.svg')}}" alt=""
                        class="absolute h-20 w-auto top-20 right-[29rem]">
                    <h1 id="login-header" class="font-sans font-bold text-4xl mb-3 mt-6 text-[#081032]">Log In</h1>
                    <p id="login-header2" class="text-[#07185788] font-sans font-normal text-sm mb-3">Welcome to
                        Fin-noys</p>
                </div>

                <!-- Display Validation Errors -->
                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- LOGIN FIELD -->
                <div class="input-group">
                    <i class="fa-solid fa-envelope icon"></i>
                    <input type="text" id="input-username" name="login" class="bg-gray-100">
                    <label for="input-username" class="text-[#07185788] text-sm font-sans">Email / Username</label>
                </div>

                <!-- PASSWORD FIELD -->
                <div class="input-group">
                    <i class="fa-solid fa-key icon"></i>
                    <input type="password" id="input-password" name="password" class="bg-gray-100 pr-10"
                        autocomplete="current-password">

                    <label for="input-password" class="text-[#07185788] text-sm font-sans">Password</label>

                    <button type="button" id="togglePassword"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-300 hover:text-blue-500 focus:outline-none">
                        <i class="fa-solid fa-eye pr-3"></i>
                    </button>
                </div>

                <div id="container-2-layer" class="text-sm">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" class="border border-gray-300" name="remember">
                        <span class="text-gray-500 font-sans text-sm">Remember Me</span>
                    </label>
                    <a href="{{ route('forgot.password') }}" class="text-blue-600 hover:underline text-sm">Forgot
                        Password?</a>
                </div>

                <input type="submit" id="btn-login"
                    class="text-sm font-sans font-semibold hover:bg-blue-800 focus:outline-white" value="Login">

                <div id="container-2-3" class="text-center p-3 text-sm">
                    <p id="donthaveacct" class="text-[#07185788]">
                        Don't have an account?
                        <span>
                            <a href="{{ route('signup') }}" id="createacc-label"
                                class="text-blue-600 font-sans font-bold hover:underline ml-1 text-xs">Create
                                Account</a>
                        </span>
                    </p>
                </div>

                <label id="terms-container" class="flex items-center space-x-2">
                    <input type="checkbox" name="terms">
                    <span class="text-xs italic text-justify text-[#868282]">
                        <p id="text-1">
                            By signing in to your account, you acknowledge that you have read and understood the
                            website's
                            <a id="terms-label" href="#" onclick="openModal('termsModal'); return false;"
                                class="text-blue-600 hover:underline ml-1">Terms and
                                Conditions</a> and
                            <a id="privacy-label" href="#" onclick="openModal('privacyModal'); return false;"
                                class="text-blue-600 hover:underline ml-1">Privacy
                                Policy</a>.
                        </p>
                    </span>
                </label>
            </form>
        </div>
    </div>

    <!-- Terms and Conditions Modal -->
    <div id="termsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="text-2xl font-bold text-gray-900">OptiCrew Terms & Conditions</h2>
                <button onclick="closeModal('termsModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fa-solid fa-times text-2xl"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="space-y-4 text-gray-700">
                    <p class="text-sm text-gray-500 font-medium">Last Updated: November 5, 2025</p>

                    <h3 class="text-lg font-bold text-gray-900 mt-6">1. Acceptance of Terms</h3>
                    <p class="text-sm leading-relaxed">
                        By accessing or using the OptiCrew workforce management and scheduling platform (the "System"),
                        you ("User") agree to comply with and be bound by these Terms and Conditions. These Terms govern
                        the automated allocation of tasks, scheduling, and employee management functions within the
                        System.
                    </p>
                    <p class="text-sm leading-relaxed">
                        If you do not agree with any part of these Terms, you must refrain from using the System.
                    </p>

                    <h3 class="text-lg font-bold text-gray-900 mt-6">2. System Operations and Allocation Rules</h3>

                    <h4 class="text-base font-semibold text-gray-900 mt-4">2.1 Workforce Allocation</h4>
                    <p class="text-sm leading-relaxed">
                        OptiCrew automatically determines the optimal number of employees required for each task based
                        on an analysis of:
                    </p>
                    <ul class="list-disc list-inside text-sm space-y-1 ml-4">
                        <li>Employee availability (excluding those on scheduled leave or days off).</li>
                        <li>Pending workload and task specifications.</li>
                        <li>Budget and resource constraints.</li>
                        <li>Utilization targets designed to maximize operational efficiency.</li>
                    </ul>

                    <h4 class="text-base font-semibold text-gray-900 mt-4">2.2 Team Composition and Driver Requirement
                    </h4>
                    <p class="text-sm leading-relaxed">
                        For mobility between client locations, each assigned team (pair or trio) must include at least
                        one employee registered in the system as having valid driving skills.
                    </p>

                    <h4 class="text-base font-semibold text-gray-900 mt-4">2.3 Task Prioritization</h4>
                    <p class="text-sm leading-relaxed">
                        Tasks labeled with an "Arrival Status" are assigned the highest scheduling priority. This
                        designation indicates immediate service needs due to a client or guest arrival and will take
                        precedence over other pending tasks.
                    </p>

                    <h4 class="text-base font-semibold text-gray-900 mt-4">2.4 Schedule Generation and Re-Optimization
                    </h4>
                    <ul class="list-disc list-inside text-sm space-y-1 ml-4">
                        <li>Schedules are generated automatically by the System.</li>
                        <li>If a new task is added before a daily schedule is officially saved and finalized, the System
                            will delete the unsaved version and generate a new optimized schedule that includes the new
                            task.</li>
                        <li>Re-optimization occurs independently within each client or company entity (e.g., Company A,
                            Company K).</li>
                    </ul>

                    <h4 class="text-base font-semibold text-gray-900 mt-4">2.5 Employee Utilization and Fair
                        Distribution</h4>
                    <p class="text-sm leading-relaxed">The System is designed to:</p>
                    <ul class="list-disc list-inside text-sm space-y-1 ml-4">
                        <li>Maximize employee utilization by assigning all available employees to at least one task
                            whenever possible.</li>
                        <li>Ensure equitable task distribution among available staff to maintain a balanced workload.
                        </li>
                    </ul>

                    <h4 class="text-base font-semibold text-gray-900 mt-4">2.6 Working Hours Compliance</h4>
                    <p class="text-sm leading-relaxed">
                        To comply with Finnish labor standards, the System enforces a maximum of 12 working hours per
                        day for any individual employee. This limit is applied automatically during scheduling and task
                        allocation.
                    </p>

                    <h4 class="text-base font-semibold text-gray-900 mt-4">2.7 Real-Time Task Additions</h4>
                    <p class="text-sm leading-relaxed">For the current operational day:</p>
                    <ul class="list-disc list-inside text-sm space-y-1 ml-4">
                        <li>Once a schedule is finalized, adding a new task will not modify existing team assignments.
                        </li>
                        <li>The System will only allocate the newly added task, assigning it to one of the existing
                            teams without altering prior allocations.</li>
                    </ul>

                    <h4 class="text-base font-semibold text-gray-900 mt-4">2.8 Team Stability and Contingency
                        ("What-If") Scenarios</h4>
                    <ul class="list-disc list-inside text-sm space-y-1 ml-4">
                        <li>Once teams are formed for a given day, their composition remains stable under normal
                            circumstances.</li>
                        <li>In the event of a "What-If" scenario (e.g., an employee's sudden absence or sickness), the
                            System will automatically reassign or supplement team members as needed.</li>
                        <li>All teams are required to consist of two (2) or three (3) members. No team will exceed three
                            members under any condition.</li>
                    </ul>

                    <h3 class="text-lg font-bold text-gray-900 mt-6">3. System Authority and Finality</h3>
                    <p class="text-sm leading-relaxed">
                        You acknowledge that all task assignments and schedules are the outcome of automated, rule-based
                        optimization performed by OptiCrew. The System's allocations are deemed final and binding for
                        operational purposes, except in cases involving verified technical errors or the invocation of a
                        "What-If" scenario.
                    </p>

                    <h3 class="text-lg font-bold text-gray-900 mt-6">4. Modifications to System Rules</h3>
                    <p class="text-sm leading-relaxed">
                        OptiCrew and its parent company, Fin-noys, reserve the right to modify or update these Terms and
                        operational rules at any time to reflect evolving business or regulatory requirements. Continued
                        use of the System following any such updates constitutes your acceptance of the revised Terms.
                    </p>

                    <h3 class="text-lg font-bold text-gray-900 mt-6">5. Contact Information</h3>
                    <p class="text-sm leading-relaxed">
                        For inquiries or clarifications regarding these Terms & Conditions, please contact:
                    </p>
                    <div class="bg-gray-50 p-4 rounded-lg mt-2">
                        <p class="text-sm font-semibold text-gray-900">OptiCrew Support</p>
                        <p class="text-sm text-gray-700 mt-1">📧 opticrewhelpcenter@gmail.com</p>
                        <p class="text-sm text-gray-700">📍 Philippines, Makati City</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal('termsModal')"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Privacy Policy Modal -->
    <div id="privacyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="text-2xl font-bold text-gray-900">Privacy Policy</h2>
                <button onclick="closeModal('privacyModal')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fa-solid fa-times text-2xl"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="space-y-4 text-gray-700">
                    <p class="text-sm text-gray-500">Last updated: January 2024</p>

                    <h3 class="text-lg font-semibold text-gray-900 mt-4">1. Information We Collect</h3>
                    <p class="text-sm leading-relaxed">
                        We collect information you provide directly to us, including your name, email address, phone
                        number, payment information, and service preferences. We also collect information about your use
                        of our services.
                    </p>

                    <h3 class="text-lg font-semibold text-gray-900 mt-4">2. How We Use Your Information</h3>
                    <p class="text-sm leading-relaxed">
                        We use the information we collect to provide, maintain, and improve our services, to process
                        your bookings, to communicate with you, and to personalize your experience.
                    </p>

                    <h3 class="text-lg font-semibold text-gray-900 mt-4">3. Information Sharing</h3>
                    <p class="text-sm leading-relaxed">
                        We do not sell or rent your personal information to third parties. We may share your information
                        with service providers who assist us in operating our platform and providing services to you.
                    </p>

                    <h3 class="text-lg font-semibold text-gray-900 mt-4">4. Data Security</h3>
                    <p class="text-sm leading-relaxed">
                        We implement appropriate technical and organizational measures to protect your personal
                        information against unauthorized access, alteration, disclosure, or destruction.
                    </p>

                    <h3 class="text-lg font-semibold text-gray-900 mt-4">5. Your Rights</h3>
                    <p class="text-sm leading-relaxed">
                        You have the right to access, update, or delete your personal information. You may also opt-out
                        of receiving promotional communications from us at any time.
                    </p>

                    <h3 class="text-lg font-semibold text-gray-900 mt-4">6. Cookies and Tracking</h3>
                    <p class="text-sm leading-relaxed">
                        We use cookies and similar tracking technologies to collect information about your browsing
                        activities and to improve our services. You can control cookies through your browser settings.
                    </p>

                    <h3 class="text-lg font-semibold text-gray-900 mt-4">7. Contact Us</h3>
                    <p class="text-sm leading-relaxed">
                        If you have any questions about this Privacy Policy, please contact us at privacy@finnoys.com.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal('privacyModal')"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputs = document.querySelectorAll('.input-group input');

            inputs.forEach(input => {
                // Handle pre-filled fields (e.g., autofill)
                if (input.value.trim() !== '') {
                    input.classList.add('not-empty');
                }

                // Toggle label floating based on content
                input.addEventListener('input', () => {
                    if (input.value.trim() !== '') {
                        input.classList.add('not-empty');
                    } else {
                        input.classList.remove('not-empty');
                    }
                });
            });

            const passwordInput = document.getElementById('input-password');
            const togglePassword = document.getElementById('togglePassword');
            const icon = togglePassword.querySelector('i');

            togglePassword.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        });

        // Modal Functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            document.body.style.overflow = 'auto'; // Re-enable scrolling
        }

        // Close modal when clicking outside of it
        window.onclick = function (event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    closeModal(modal.id);
                }
            });
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                const modals = document.querySelectorAll('.modal.active');
                modals.forEach(modal => {
                    closeModal(modal.id);
                });
            }
        });
    </script>
</body>

</html>