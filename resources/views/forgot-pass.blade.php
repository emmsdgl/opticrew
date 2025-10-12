<x-layouts.general-forgot-password>

    @slot('slot1')

    <x-forgot-password-header title="Step 1: Verifying Account" subHeader1="Can't remember your password?"
        header1="Verifying Account" subHeader2=" Fill in the following details to reset your password." />

    <section id="reset-password-field-container" class="flex flex-col gap-6">
        <div class="flex flex-col w-full justify-center align-items-center">
            @php
                $securityQuestions = ['This Day', 'This Week', 'This Month'];
            @endphp

            <x-inputfield label="Email Address" inputId="email" inputName="email" inputType="text" icon="fa-envelope" />
            <x-dropdown :options="$securityQuestions" default="Select a Security Question"
                id="dropdown-security-questions" />
            <x-inputfield label="Security Question 1 Answer" inputId="seques1-ans" inputName="seques1-ans"
                inputType="text" icon="fa-comment" />
        </div>

        <div class="flex flex-row w-full justify-center align-items-center mt-6 gap-4">
            {{-- Assuming these buttons will be managed by JavaScript in general-forgot-password.blade.php --}}
            <button id="back1-btn" 
                class="w-full sm:w-auto px-10 py-4 text-blue-500 border border-blue-500 hover:bg-blue-500 hover:text-white focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm transition-all">Back</button>
            <button id="next1-btn" type="button"
                class="w-full sm:w-auto px-10 py-4 text-white text-sm bg-blue-500 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-center transition-all">Submit</button>
        </div>
    </section>
    @endslot

    @slot('slot2')
    <section id="reset-password-field-container" class="flex flex-col gap-6">
        <div id="step2">
            <div id="passres-container-2">
                <div id="header-container">
                    <div class="mb-2">
                        <h1 id="header-1-2">Step <span id="step-number-2">2</span> of 3</h1>
                    </div>
                    <p id="header-1" class="text-base md:text-base lg:text-base">Confirm Your Email Address</p>
                    <h1 id="header-2" class="text-4xl md:text-4xl lg:text-4xl font-bold mb-5 mt-5">OTP Verification</h1>
                    <p id="header-3" class="text-sm md:text-sm lg:text-sm">
                        We sent an OTP code to your provided email address
                        <span id="email_address"
                            class="text-sm md:text-sm lg:text-sm">lcsanbuenaventura.2002@gmail.com</span>, corresponding
                        to an existing account. Enter the code below.
                    </p>
                </div>

                <div id="form-container">
                    <form class="w-full max-w-xs mx-auto space-y-6">
                        <div id="otp-container" class="flex justify-center gap-2">
                            @for ($i = 0; $i < 6; $i++)
                                <input type="text" maxlength="1"
                                    class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 text-center rounded-lg border border-gray-300 font-bold focus:outline-none focus:ring-4 focus:ring-blue-200">
                            @endfor
                        </div>

                        <div id="buttons-container" class="flex flex-col sm:flex-row justify-center gap-4 mt-6">
                            {{-- Assuming 'back' button is not needed for OTP or is on a separate div --}}
                            <button id="next2-btn" type="button"
                                class="w-full sm:w-auto px-12 py-3 sm:px-32 sm:py-4 text-white text-sm bg-blue-500 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-center">
                                Verify OTP
                            </button>
                        </div>

                        <p id="resend-label" class="text-sm">
                            Didn't receive an OTP?
                            <span><a href="#" class="text-blue-600 font-bold">Resend Code</a></span>
                            <span id="timer">in 28 seconds</span>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>
    @endslot

    @slot('slot3')
    <div id="step3">
        <div id="passres-container-3">
            <div id="header-container">
                <div class="mb-4">
                    <h1 id="header-1-2">Step <span id="step-number-3">3</span> of 3</h1>
                </div>
                <p id="header-1" class="text-base md:text-base lg:text-base">Set a New Password</p>
                <h1 id="header-2" class="text-4xl md:text-4xl lg:text-4xl font-bold mb-5 mt-5">Reset Your Password</h1>
                <p id="header-3" class="text-sm md:text-sm lg:text-sm">You are now setting a new password. Make sure to
                    take note of this new password.</p>
            </div>

            <div id="form-container">
                <form class="w-full space-y-6">
                    <div class="input-container w-full max-w-md mx-auto relative">
                        <i class="fas fa-key absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                        <input type="password" id="input-new-password" placeholder=" "
                            class="input-field w-full pr-12 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                            name="password" required>
                        <label for="input-new-password">New Password</label>
                        <i class="fas fa-eye absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 cursor-pointer"
                            id="togglePassword"></i>
                    </div>
                    <div class="input-container w-full max-w-md mx-auto relative">
                        <i
                            class="fas fa-square-check absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                        <input type="password" id="input-confirm-password" placeholder=" "
                            class="input-field w-full pr-12 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                            name="confirm_password" required>
                        <label for="input-confirm-password">Confirm New Password</label>
                        <i class="fas fa-eye absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 cursor-pointer"
                            id="toggleConfirmPassword"></i>
                    </div>

                    <div id="buttons-container" class="flex flex-col sm:flex-row justify-center gap-4 mt-6">
                        <button id="back3-btn" type="button"
                            class="w-full sm:w-auto px-10 py-4 text-blue-500 border border-blue-500 hover:bg-blue-500 hover:text-white focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm">Back</button>
                        <button type="submit"
                            class="w-full sm:w-auto px-10 py-4 text-white text-sm bg-blue-500 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-center">Reset
                            Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endslot

</x-layouts.general-forgot-password>
@stack('scripts')