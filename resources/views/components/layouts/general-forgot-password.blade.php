@props([
    'title' => ''
])

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{$title}}</title>
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body {
            min-height: 100vh;
        }
        
        #header-1, #step-number, #step-number-1, #step-number-2, #step-number-3 {
            color: #0077FF;
        }
        
        #header-1-2 {
            color: #07185778;
        }
        
        #header-2 {
            color: #071957;
        }
        
        #header-3 {
            color: #07185778;
            text-align: justify;
        }
        
        #header-container {
            padding: 1em;
            margin-top: 2em;
        }
        
        /* Floating Label Specific Styles */
        .input-container {
            position: relative;
            margin-top: 0.3rem;
        }

        .input-container label {
            position: absolute;
            left: 3rem;
            top: 0.8rem;
            color: #07185778;
            pointer-events: none;
            transition: all 0.2s ease-out;
            font-size: small;
        }

        .input-container .input-field:focus + label,
        .input-container .input-field:not(:placeholder-shown) + label {
            top: -0.8rem;
            left: 1rem;
            font-size: 0.75rem;
            color: #0077FF;
            background-color: white;
            padding: 0 0.25rem;
        }

        .input-container .input-field {
            padding-left: 3rem;
            padding-top: 0.8rem;
            padding-bottom: 0.8rem;
        }

        .input-container .input-field:focus::placeholder {
            color: transparent;
        }
        
        .input-container .input-field:not(:placeholder-shown)::placeholder {
            color: transparent;
        }
        
        /* Container for OTP inputs */
        #otp-container {
            display: flex;
            justify-content: center;
            gap: 13px;            
            padding-top: 0.4rem;
            padding-bottom: 1rem;
        }

        /* OTP input slots */
        #otp-container input[type="text"] {
            width: 40px;
            height: 50px;
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
            border: 1px solid #d1d5db;
            background-color: #c2c6d94b;
            border-radius: 10px;
            outline: none;
            transition: all 0.2s ease;
        }

        /* Focused state */
        #otp-container input[type="text"]:focus {
            border-color: #0077FF;
            box-shadow: 0 0 0 3px rgba(0, 119, 255, 0.2);
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

<body class="flex flex-col justify-start items-center min-h-screen bg-[url('/images/backgrounds/login_bg.svg')] bg-cover bg-center bg-no-repeat bg-fixed gap-3 font-sans antialiased">
      <!-- 🔹 Header with logo + back button -->
    <header class="absolute top-0 left-0 w-full flex justify-between items-center px-12 py-8">
        <div class="flex items-center gap-2">
            <span class="text-[#0077FF]"><img src="/images/finnoys-text-logo-light.svg" alt="" class="h-20 w-auto"></span>
        </div>

        <button 
            onclick="history.back()" 
            class="flex items-center gap-2 text-[#0077FF] hover:text-blue-700 transition-colors duration-200 text-sm font-medium">
            <i class="fa-solid fa-arrow-left"></i>
        </button>
    </header>


<div id="pasres-main-container" class="w-full gap-8 max-w-sm md:max-w-lg lg:max-w-screen md:p-10 flex flex-col items-center text-center min-h-screen justify-center">
        
        <!-- STEP 1 Content -->
        <div id="step1" class="step-content w-full">
            {{ $slot1 }}
        </div>

        <!-- STEP 2 Content -->
        <div id="step2" class="step-content w-full hidden">
            {{ $slot2 }}
        </div>

        <!-- STEP 3 Content -->
        <div id="step3" class="step-content w-full hidden">
            {{ $slot3 }}
        </div>
        
        <!-- Progress Bar -->
        <div id="stepctr-container-2" class="w-full mt-8">
            <div id="progress-container" class="w-full flex justify-center space-x-2">
                <div class="progress-bar h-1.5 w-32 rounded-full bg-blue-600"></div>
                <div class="progress-bar h-1.5 w-32 rounded-full bg-gray-300"></div>
                <div class="progress-bar h-1.5 w-32 rounded-full bg-gray-300"></div>
            </div>
        </div>
    </div>

    <!-- @push('scripts') -->
    <script>
    (function() {
        'use strict';
        
        console.log("🚀 SCRIPT STARTED - Forgot Password Flow");

        // Wait for DOM to be fully loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeForgotPassword);
        } else {
            initializeForgotPassword();
        }

        function initializeForgotPassword() {
            console.log("✅ DOM Ready - Initializing...");

            // Get all elements
            const emailInput = document.getElementById('email');
            const securityDropdown = document.getElementById('dropdown-security-questions');
            const securityAnswer = document.getElementById('security_answer');
            const next1Btn = document.getElementById('next1-btn');
            const next2Btn = document.getElementById('next2-btn');
            const back1Btn = document.getElementById('back1-btn');
            const back2Btn = document.getElementById('back2-btn');
            const back3Btn = document.getElementById('back3-btn');
            const step3Form = document.getElementById('step3-form');
            const resendLink = document.getElementById('resend-otp-link');

            // Verify elements exist
            console.log("📝 Elements found:", {
                emailInput: !!emailInput,
                securityDropdown: !!securityDropdown,
                next1Btn: !!next1Btn
            });

            if (!emailInput || !securityDropdown) {
                console.error("❌ CRITICAL: Required elements not found!");
                return;
            }

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                console.error("❌ CSRF token not found!");
                alert("Configuration error: CSRF token missing. Please refresh the page.");
                return;
            }
            console.log("🔐 CSRF Token found:", csrfToken.substring(0, 10) + "...");

            let currentStep = 1;
            const steps = document.querySelectorAll('.step-content');

            // ========== UTILITY FUNCTIONS ==========
            function updateStepView() {
                console.log("📍 Updating view to step:", currentStep);
                steps.forEach((step, index) => {
                    step.classList.toggle('hidden', index + 1 !== currentStep);
                });
                
                const bars = document.querySelectorAll(".progress-bar");
                bars.forEach((bar, index) => {
                    if (index + 1 <= currentStep) {
                        bar.classList.remove("bg-gray-300");
                        bar.classList.add("bg-blue-600");
                    } else {
                        bar.classList.remove("bg-blue-600");
                        bar.classList.add("bg-gray-300");
                    }
                });
            }

            function showError(message) {
                alert(message);
                console.error("❌ Error:", message);
            }

            function showSuccess(message) {
                alert(message);
                console.log("✅ Success:", message);
            }

            // ========== STEP 1: LOAD SECURITY QUESTIONS ==========
            let questionsLoaded = false;

            emailInput.addEventListener('blur', async function() {
                const email = this.value.trim();
                console.log("📧 Email blur event - Value:", email);
                
                if (!email) {
                    console.log("⚠️ Email is empty, skipping fetch");
                    securityDropdown.innerHTML = '<option value="" disabled selected>Enter your email first...</option>';
                    questionsLoaded = false;
                    return;
                }

                console.log("🔄 Fetching security questions for:", email);
                securityDropdown.innerHTML = '<option value="" disabled selected>Loading questions...</option>';
                securityDropdown.disabled = true;

                try {
                    const response = await fetch("{{ route('password.getQuestions') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ email: email })
                    });

                    console.log("📡 Response status:", response.status);
                    const data = await response.json();
                    console.log("📦 Response data:", data);

                    if (!response.ok) {
                        throw new Error(data.error || 'Could not fetch security questions');
                    }

                    // Populate dropdown
                    securityDropdown.innerHTML = '<option value="" disabled selected>Select a Security Question</option>';
                    securityDropdown.innerHTML += `<option value="${data.questions.q1.key}">${data.questions.q1.text}</option>`;
                    securityDropdown.innerHTML += `<option value="${data.questions.q2.key}">${data.questions.q2.text}</option>`;
                    securityDropdown.disabled = false;
                    questionsLoaded = true;
                    
                    console.log("✅ Security questions loaded successfully");

                } catch (error) {
                    console.error("❌ Fetch error:", error);
                    securityDropdown.innerHTML = `<option value="" disabled selected>${error.message}</option>`;
                    securityDropdown.disabled = true;
                    questionsLoaded = false;
                }
            });

            // Add change event for debugging
            emailInput.addEventListener('change', function() {
                console.log("📧 Email changed to:", this.value);
            });

            // ========== STEP 1: VERIFY & SEND OTP ==========
            next1Btn.addEventListener('click', async function(e) {
                e.preventDefault();
                console.log("🔘 Submit button clicked");

                const email = emailInput.value.trim();
                const question = securityDropdown.value;
                const answer = securityAnswer.value.trim();

                console.log("📋 Form values:", { email, question, answer: answer ? "***" : "" });

                if (!email) {
                    showError('Please enter your email address.');
                    emailInput.focus();
                    return;
                }

                if (!questionsLoaded || !question) {
                    showError('Please select a security question.');
                    securityDropdown.focus();
                    return;
                }

                if (!answer) {
                    showError('Please enter your security answer.');
                    securityAnswer.focus();
                    return;
                }

                const button = this;
                button.disabled = true;
                button.textContent = 'Verifying...';
                console.log("🔄 Sending verification request...");

                const formData = new FormData();
                formData.append('email', email);
                formData.append('security_question', question);
                formData.append('security_answer', answer);

                try {
                    const response = await fetch("{{ route('password.verifyAccount') }}", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: formData
                    });

                    const data = await response.json();
                    console.log("📡 Verify response:", response.status, data);

                    if (!response.ok) {
                        throw new Error(data.message || 'Verification failed');
                    }

                    showSuccess(data.message);
                    currentStep = 2;
                    updateStepView();
                    startResendTimer();

                } catch (error) {
                    showError(error.message);
                } finally {
                    button.disabled = false;
                    button.textContent = 'Submit';
                }
            });

            back1Btn.addEventListener('click', () => {
                window.location.href = "{{ route('login') }}";
            });

            // ========== STEP 2: OTP HANDLING ==========
            const otpInputs = document.querySelectorAll('.otp-input');
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    if (e.target.value.length === 1 && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                });
                input.addEventListener('keydown', (e) => {
                    if (e.key === "Backspace" && e.target.value.length === 0 && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });
            });

            next2Btn.addEventListener('click', async function() {
                let otp = '';
                otpInputs.forEach(input => otp += input.value);

                if (otp.length !== 6) {
                    showError('Please enter the complete 6-digit OTP.');
                    return;
                }

                const button = this;
                button.disabled = true;
                button.textContent = 'Verifying...';

                try {
                    const response = await fetch("{{ route('password.verifyOtp') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ otp: otp })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'OTP verification failed');
                    }

                    showSuccess(data.message);
                    currentStep = 3;
                    updateStepView();

                } catch (error) {
                    showError(error.message);
                } finally {
                    button.disabled = false;
                    button.textContent = 'Verify OTP';
                }
            });

            back2Btn.addEventListener('click', () => {
                currentStep = 1;
                updateStepView();
            });

            // Resend OTP
            resendLink.addEventListener('click', function(e) {
                e.preventDefault();
                if (this.style.pointerEvents === 'none') return;
                next1Btn.click();
            });

            // ========== STEP 3: RESET PASSWORD ==========
            step3Form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const button = document.getElementById('submit-reset-btn');
                button.disabled = true;
                button.textContent = 'Resetting...';

                const formData = new FormData(this);

                try {
                    const response = await fetch("{{ route('password.reset.submit') }}", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Password reset failed');
                    }

                    showSuccess(data.message);
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1000);

                } catch (error) {
                    showError(error.message);
                } finally {
                    button.disabled = false;
                    button.textContent = 'Reset Password';
                }
            });

            back3Btn.addEventListener('click', () => {
                currentStep = 2;
                updateStepView();
            });

            // Password toggles
            function setupPasswordToggle(toggleId, passwordId) {
                const toggle = document.getElementById(toggleId);
                const password = document.getElementById(passwordId);

                if (toggle && password) {
                    toggle.addEventListener('click', function() {
                        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                        password.setAttribute('type', type);
                        this.classList.toggle('fa-eye');
                        this.classList.toggle('fa-eye-slash');
                    });
                }
            }

            setupPasswordToggle('togglePassword', 'input-new-password');
            setupPasswordToggle('toggleConfirmPassword', 'input-confirm-password');

            // Resend timer
            let timerInterval;
            function startResendTimer() {
                let seconds = 30;
                const timerElement = document.getElementById('timer');
                resendLink.style.pointerEvents = 'none';
                resendLink.style.color = '#9ca3af';

                timerElement.textContent = ` in ${seconds}s`;

                clearInterval(timerInterval);
                timerInterval = setInterval(() => {
                    seconds--;
                    timerElement.textContent = ` in ${seconds}s`;
                    if (seconds <= 0) {
                        clearInterval(timerInterval);
                        timerElement.textContent = '';
                        resendLink.style.pointerEvents = 'auto';
                        resendLink.style.color = '';
                    }
                }, 1000);
            }

            // Initialize
            updateStepView();
            console.log("✅ Initialization complete");
        }
    })();
    </script>
    <!-- @endpush -->
    @stack('scripts')
</body>
</html>