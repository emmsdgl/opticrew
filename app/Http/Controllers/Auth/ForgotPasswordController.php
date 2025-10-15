<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationOtp; // <-- UPDATED TO USE YOUR MAILABLE
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class ForgotPasswordController extends Controller
{
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password'); // Ensure you have this view file
    }

    public function getSecurityQuestions(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->where('role', 'external_client')->first();
        if (!$user) {
            return response()->json(['error' => 'No client account found with this email.'], 404);
        }
        $client = Client::where('user_id', $user->id)->first();
        if (!$client || !$client->security_question_1 || !$client->security_question_2) {
            return response()->json(['error' => 'No security questions are set up for this account.'], 422);
        }
        $questionMap = [
            'pet_name' => 'What is the name of your first pet?',
            'birth_city' => 'In what city were you born?',
            'best_friend' => 'What is the name of your best friend?',
            'teacher_name' => 'Who was your favorite teacher?',
        ];
        return response()->json([
            'questions' => [
                'q1' => ['key' => $client->security_question_1, 'text' => $questionMap[$client->security_question_1] ?? 'Unknown Question'],
                'q2' => ['key' => $client->security_question_2, 'text' => $questionMap[$client->security_question_2] ?? 'Unknown Question'],
            ]
        ]);
    }

    public function verifyAccountAndSendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'security_question' => 'required|string',
            'security_answer' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }
        $user = User::where('email', $request->email)->first();
        $client = Client::where('user_id', $user->id)->first();
        if (!$client) {
            return response()->json(['message' => 'Invalid account details.'], 401);
        }
        $correctAnswerHash = null;
        if ($request->security_question === $client->security_question_1) {
            $correctAnswerHash = $client->security_answer_1;
        } elseif ($request->security_question === $client->security_question_2) {
            $correctAnswerHash = $client->security_answer_2;
        }
        if (!$correctAnswerHash || !Hash::check($request->security_answer, $correctAnswerHash)) {
            return response()->json(['message' => 'The security answer is incorrect.'], 401);
        }

        $otp = random_int(100000, 999999);
        Session::put('otp', $otp);
        Session::put('reset_email', $user->email);
        Session::put('otp_generated_at', now());

        // --- Use your existing Mailable ---
        Mail::to($user->email)->send(new EmailVerificationOtp($otp)); // <-- UPDATED LINE

        return response()->json(['message' => 'Verification successful. An OTP has been sent to your email.']);
    }
    
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), ['otp' => 'required|numeric|digits:6']);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }
        $sessionOtp = Session::get('otp');
        $otpTimestamp = Session::get('otp_generated_at');
        if (!$sessionOtp || !$otpTimestamp || now()->diffInMinutes($otpTimestamp) > 5) {
            Session::forget(['otp', 'otp_generated_at']);
            return response()->json(['message' => 'OTP has expired. Please request a new one.'], 408);
        }
        if ($request->otp != $sessionOtp) {
            return response()->json(['message' => 'The OTP you entered is incorrect.'], 401);
        }
        Session::forget(['otp', 'otp_generated_at']);
        Session::put('password_reset_allowed', true);
        return response()->json(['message' => 'OTP verified successfully.']);
    }

    public function resetPassword(Request $request)
    {
        if (!Session::get('password_reset_allowed')) {
            return response()->json(['message' => 'Unauthorized. Please complete the verification process first.'], 403);
        }
        $validator = Validator::make($request->all(), ['password' => 'required|string|min:8|confirmed']);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }
        $email = Session::get('reset_email');
        if (!$email) {
            return response()->json(['message' => 'Your session has expired. Please start over.'], 408);
        }
        $user = User::where('email', $email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        Session::forget(['reset_email', 'password_reset_allowed']);
        return response()->json(['message' => 'Password has been reset successfully!', 'redirect_url' => route('login')]);
    }
}