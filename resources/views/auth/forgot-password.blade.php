<x-layouts.general-forgot-password>

    @slot('slot1')
            <div class="header-container flex flex-col w-full mb-3">
                <h1 id="header-1-0" class="text-sm font-bold mt-6 mb-6 text-[#081032]">Step <span
                        id="step-number-1">1</span> of 3</h1>
                <x-forgot-password-header title="Step 1: Verifying Account" subHeader1="Can't remember your password?"
                    header1="Verifying Account"
                    subHeader2="You're one step closer. Fill in the following details to reset your password." />
            </div>

            <form id="step1-form" class="flex flex-col w-full space-y-6">
                @csrf
                
                <!-- Email Input -->
                <div class="input-container w-full relative">
                    <i class="fa-solid fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500 z-10"></i>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder=" "
                        required
                        class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl border border-transparent
                               focus:outline-none focus:border-blue-500 text-gray-700 pl-12"
                    />
                    <label for="email" class="input-label">Email Address</label>
                </div>
                
                <!-- Security Question Dropdown -->
                <div class="input-container">
                    <i class="fas fa-question-circle absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500 z-10"></i>
                    <select id="dropdown-security-questions" name="security_question" required
                        class="input-field w-full pl-12 pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700">
                        <option value="" disabled selected>Enter your email first...</option>
                    </select>
                </div>

                <!-- Security Answer Input -->
                <div class="input-container w-full relative">
                    <i class="fa-solid fa-comment absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500 z-10"></i>
                    <input
                        type="text"
                        id="security_answer"
                        name="security_answer"
                        placeholder=" "
                        required
                        class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl border border-transparent
                               focus:outline-none focus:border-blue-500 text-gray-700 pl-12"
                    />
                    <label for="security_answer" class="input-label">Security Question Answer</label>
                </div>
            </form>

            <div class="flex flex-row w-full justify-center items-center mt-8 gap-4">
                <button id="back1-btn" type="button"
                    class="w-full sm:w-auto px-10 py-4 text-blue-500 border border-blue-500 hover:bg-blue-500 hover:text-white focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm transition-all">Back to Login</button>
                <button id="next1-btn" type="button"
                    class="w-full sm:w-auto px-10 py-4 text-white text-sm bg-blue-500 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-center transition-all">Submit</button>
            </div>
    @endslot

    @slot('slot2')
            <div class="header-container flex flex-col w-full mb-3">
                <h1 id="header-2-0" class="text-sm font-bold mb-6 text-[#081032]">Step <span
                        id="step-number-2">2</span> of 3</h1>
                <x-forgot-password-header title="Step 2: OTP Verification" subHeader1="Confirm your email address"
                    header1="OTP Verification"
                    subHeader2="We sent an OTP code to your provided email address. Enter the code below to proceed." />
            </div>

            <div id="form-container">
                <div class="w-full max-w-xs mx-auto space-y-6">
                    <div id="otp-container" class="flex justify-center gap-2">
                        @for ($i = 1; $i <= 6; $i++)
                            <input type="text" id="otp{{$i}}" maxlength="1"
                                class="otp-input w-12 h-12 sm:w-10 sm:h-10 md:w-12 md:h-12 text-center rounded-lg border border-gray-300 font-bold focus:outline-none focus:ring-4 focus:ring-blue-200">
                        @endfor
                    </div>

                    <div class="flex flex-row w-full justify-center items-center mt-8 gap-4">
                        <button id="back2-btn" type="button"
                            class="w-full sm:w-auto px-10 py-4 text-blue-500 border border-blue-500 hover:bg-blue-500 hover:text-white focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm transition-all">Back</button>
                        <button id="next2-btn" type="button"
                            class="w-full sm:w-auto px-10 py-4 text-white text-sm bg-blue-500 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-center transition-all">Verify OTP</button>
                    </div>

                    <p id="resend-label" class="text-sm text-center">
                        Didn't receive an OTP?
                        <span><a href="#" id="resend-otp-link" class="text-blue-600 font-bold">Resend Code</a></span>
                        <span id="timer"></span>
                    </p>
                </div>
            </div>
    @endslot

    @slot('slot3')
            <div class="header-container flex flex-col w-full mb-3">
                <h1 id="header-3-0" class="text-sm font-bold mb-6 text-[#081032]">Step <span
                        id="step-number-3">3</span> of 3</h1>
                <x-forgot-password-header title="Step 3: Resetting Password" subHeader1="Make a new password"
                    header1="Reset Password"
                    subHeader2="Yay! You are now setting a new password. Make sure to take note of this new password." />
            </div>

            <div id="form-container">
                <form id="step3-form" class="w-full space-y-4">
                    @csrf
                    <div class="input-container w-full max-w-md mx-auto relative">
                        <i class="fas fa-key absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                        <input type="password" id="input-new-password" placeholder=" "
                            class="input-field w-full pr-12 pl-12 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                            name="password" required>
                        <label for="input-new-password">New Password</label>
                        <i class="fas fa-eye absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 cursor-pointer"
                            id="togglePassword"></i>
                    </div>
                    <div class="input-container w-full max-w-md mx-auto relative">
                        <i class="fas fa-square-check absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                        <input type="password" id="input-confirm-password" placeholder=" "
                            class="input-field w-full pr-12 pl-12 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                            name="password_confirmation" required>
                        <label for="input-confirm-password">Confirm New Password</label>
                        <i class="fas fa-eye absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 cursor-pointer"
                            id="toggleConfirmPassword"></i>
                    </div>

                    <div id="buttons-container" class="flex flex-col sm:flex-row justify-center gap-4 pt-4">
                        <button id="back3-btn" type="button"
                            class="w-full sm:w-auto px-10 py-4 text-blue-500 border border-blue-500 hover:bg-blue-500 hover:text-white focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm">Back</button>
                        <button id="submit-reset-btn" type="submit"
                            class="w-full sm:w-auto px-10 py-4 text-white text-sm bg-blue-500 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-center">Reset
                            Password</button>
                    </div>
                </form>
            </div>
    @endslot
</x-layouts.general-forgot-password>