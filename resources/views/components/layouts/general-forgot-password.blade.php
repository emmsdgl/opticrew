<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome CDN for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background-color: url(/public/images/backgrounds/login_bg.svg); /* A simple, modern background color */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        #header-1, #step-number {
            font-family: 'fam-bold';
            color: #0077FF;
        }
        #header-1-2 {
            font-family: 'fam-bold';
            color: #07185778;
        }
        #header-2 {
            font-family: 'fam-bold';
            color: #071957;
        }
        #header-3 {
            font-family: 'fam-regular';
            color: #07185778;
            text-align: justify;
        }
        #header-container {
            padding: 1em;
            margin-top: 2em;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        #pasres-main-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 85vh;
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

        /* Correct Style for the select dropdown floating label */
        .input-container .select-field:focus + label,
        .input-container .select-field:not([value=""]) + label {
            top: -0.8rem;
            left: 1rem;
            font-size: 0.75rem;
            color: #0077FF;
            background-color: white;
            padding: 0 0.25rem;
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
        
        /* Responsive OTP inputs */
    </style>
</head>
@props([
    'title' => ''
])

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$title}}</title>
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/datepicker.min.js"></script>
    <script src="https://unpkg.com/flowbite@2.5.1/dist/flowbite.min.js"></script>
    <script src="https://unpkg.com/flowbite@latest/dist/flowbite.min.js"></script>
</head>

<body class="flex flex-col justify-start items-center p-4 md:p-8">
    <div id="pasres-main-container" class="w-full max-w-sm md:max-w-lg lg:max-w-xl p-6 md:p-10 flex flex-col items-center text-center">
            {{ $slot1 }}
        </div>
            <!-- STEP 2 Content -->
        <div id="step2" class="step-content hidden">
            {{ $slot2 }}
        </div>

            <!-- STEP 3 Content -->
        <div id="step3" class="step-content hidden">
            {{ $slot3 }}
        </div>

        <!-- Progress Bar (outside of step content container) -->
        <div id="stepctr-container-2" class="mt-12 ">
            <div id="progress-container" class="w-full flex justify-center space-x-2 p-6">
                <div class="h-1.5 w-32 rounded-full bg-blue-600"></div>
                <div class="h-1.5 w-32 rounded-full bg-gray-300"></div>
                <div class="h-1.5 w-32 rounded-full bg-gray-300"></div>
            </div>
        </div>

    </div>
@push('scripts')

    <script>
        // Centralized step management
        let currentStep = 1;
        const totalSteps = 3;

        // Function to update the UI based on the current step
        function updateUI() {
            // Update progress bar
            const bars = document.querySelectorAll("#progress-container div");
            bars.forEach((bar, index) => {
                if (index + 1 <= currentStep) {
                    bar.classList.remove("bg-gray-300");
                    bar.classList.add("bg-blue-600");
                } else {
                    bar.classList.remove("bg-blue-600");
                    bar.classList.add("bg-gray-300");
                }
            });
            
            // Update step number text
            document.querySelector('#step-number-1').innerText = currentStep;
            document.querySelector('#step-number-2').innerText = currentStep;
            document.querySelector('#step-number-3').innerText = currentStep;

            // Show/hide content for each step
            const stepContents = document.querySelectorAll('.step-content');
            stepContents.forEach((content, index) => {
                if (index + 1 === currentStep) {
                    content.classList.remove('hidden');
                } else {
                    content.classList.add('hidden');
                }
            });
        }

        // Functions to navigate between steps
        function nextStep() {
            if (currentStep < totalSteps) {
                currentStep++;
                updateUI();
            }
        }
        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                updateUI();
            }
        }
        
        // Initial UI update
        updateUI();

        // Attach event listeners for navigation buttons
        document.getElementById('next1-btn').addEventListener('click', nextStep);
        document.getElementById('next2-btn').addEventListener('click', nextStep);
        document.getElementById('back1-btn').addEventListener('click', prevStep);
        // document.getElementById('back2-btn').addEventListener('click', prevStep);
        document.getElementById('back3-btn').addEventListener('click', prevStep);

        // --- Step 1 Specific Functionality (Dropdown) ---
        const dropdownButton = document.getElementById('security-question-button');
        const dropdownMenu = document.getElementById('security-question-dropdown');
        const menuItems = dropdownMenu.querySelectorAll('a');
        
        // Hidden input to store the selected value
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'security_question';
        // Append the hidden input to the form
        dropdownButton.parentNode.appendChild(hiddenInput);

        // Toggle dropdown visibility on button click
        dropdownButton.addEventListener('click', () => {
            dropdownMenu.classList.toggle('hidden');
        });

        // Handle selection of a dropdown item
        menuItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const selectedValue = item.getAttribute('data-value');
                const selectedText = item.textContent;

                // Update the button text with the selected question
                dropdownButton.textContent = selectedText;
                // Hide the dropdown menu
                dropdownMenu.classList.add('hidden');
                // Update the hidden input's value for form submission
                hiddenInput.value = selectedValue;
            });
        });
        
        // Close the dropdown if the user clicks outside
        document.addEventListener('click', (e) => {
            if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
        
        // --- Step 2 Specific Functionality (OTP) ---
        const otpInputs = document.querySelectorAll('#otp-container input');
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                // Move focus to the next input
                if (e.target.value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });
            input.addEventListener('keydown', (e) => {
                // Handle backspace to move to previous input
                if (e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });
        });

        // --- Step 3 Specific Functionality (Password Toggles) ---
        function setupPasswordToggle(toggleButtonId, passwordInputId) {
            const toggleButton = document.getElementById(toggleButtonId);
            const passwordInput = document.getElementById(passwordInputId);

            if (toggleButton && passwordInput) {
                toggleButton.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }
        }
        setupPasswordToggle('togglePassword', 'input-new-password');
        setupPasswordToggle('toggleConfirmPassword', 'input-confirm-password');
        </script>
@endpush
</body>

</html></html>
