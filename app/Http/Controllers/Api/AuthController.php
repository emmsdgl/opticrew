<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Client;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Resolve a stored path (relative or absolute URL) to a full URL.
     * The website stores cover_photo as a relative path (e.g. "uploads/cover_photos/file.jpg").
     * The mobile app needs a full URL to render the image.
     */
    private function resolveUrl(?string $path): ?string
    {
        if (!$path) return null;
        if (str_starts_with($path, 'http')) return $path;
        return url($path);
    }

    /**
     * Login user and create token
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required',
        ]);

        $loginInput = $request->input('login');
        $password = $request->input('password');
        $user = null;

        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            // Try primary email
            $user = User::where('email', $loginInput)->first();

            // Try alternative_email (employees may have Gmail stored here)
            if (!$user) {
                $user = User::where('alternative_email', $loginInput)->first();
            }
        } else {
            // Try username
            $user = User::where('username', $loginInput)->first();

            // Try name as fallback
            if (!$user) {
                $user = User::where('name', $loginInput)->first();
            }
        }

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Block applicants and external clients — mobile is only for admin, employee, company
        if (in_array($user->role, ['applicant', 'external_client'])) {
            return response()->json([
                'message' => 'This account type can only access the website. Please log in at finnoys.com.'
            ], 403);
        }

        // Check if account is active
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Your account has been deactivated. Please contact support to reactivate.'
            ], 403);
        }

        // Create token
        $token = $user->createToken('mobile-app')->plainTextToken;

        // Load employee relationship if exists
        $user->load('employee');

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
                'profile_picture' => $user->profile_picture,
                'cover_photo' => $this->resolveUrl($user->cover_photo),
                'employee_id' => $user->employee?->id,
                'google_linked' => !empty($user->google_id),
            ],
        ]);
    }

    /**
     * Login via Google ID token (Mobile App)
     */
    public function googleLogin(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        // Verify the Google ID token via Google's tokeninfo endpoint
        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $request->id_token,
        ]);

        if ($response->failed()) {
            return response()->json([
                'message' => 'Invalid Google token'
            ], 401);
        }

        $googleUser = $response->json();
        $googleClientId = config('services.google.client_id');

        // Verify the token was issued for our app
        if ($googleUser['aud'] !== $googleClientId) {
            return response()->json([
                'message' => 'Google token was not issued for this application'
            ], 401);
        }

        $googleId = $googleUser['sub'];
        $email = $googleUser['email'];
        $name = $googleUser['name'] ?? $email;
        $avatar = $googleUser['picture'] ?? null;

        // Find user: by google_id, then email, then alternative_email
        $user = User::where('google_id', $googleId)->first();

        if (!$user) {
            $user = User::where('email', $email)->first();

            if ($user) {
                $user->update(['google_id' => $googleId]);
            } else {
                $user = User::where('alternative_email', $email)->first();

                if ($user) {
                    $user->update(['google_id' => $googleId]);
                } else {
                    // Create new external_client account
                    $user = DB::transaction(function () use ($googleId, $email, $name, $avatar) {
                        $nameParts = explode(' ', $name, 2);

                        $user = User::create([
                            'name' => $name,
                            'email' => $email,
                            'google_id' => $googleId,
                            'profile_picture' => $avatar,
                            'email_verified_at' => now(),
                            'role' => 'external_client',
                            'terms_accepted_at' => now(),
                        ]);

                        Client::create([
                            'user_id' => $user->id,
                            'client_type' => 'personal',
                            'first_name' => $nameParts[0],
                            'last_name' => $nameParts[1] ?? '',
                            'is_active' => true,
                        ]);

                        return $user;
                    });
                }
            }
        }

        // Block banned users
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Your account has been deactivated. Please contact support to reactivate.'
            ], 403);
        }

        // Update avatar if changed
        if ($avatar && $user->profile_picture !== $avatar) {
            $user->update(['profile_picture' => $avatar]);
        }

        // Create Sanctum token
        $token = $user->createToken('mobile-app')->plainTextToken;
        $user->load('employee');

        UserActivityLog::log($user->id, UserActivityLog::TYPE_LOGIN, 'Logged in via Google (mobile)', null, $request->ip());

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
                'profile_picture' => $user->profile_picture,
                'cover_photo' => $this->resolveUrl($user->cover_photo),
                'employee_id' => $user->employee?->id,
                'google_linked' => !empty($user->google_id),
            ],
        ]);
    }

    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request)
    {
        // Revoke all tokens for the user
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $user->load('employee');

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
                'location' => $user->location,
                'profile_picture' => $user->profile_picture,
                'cover_photo' => $this->resolveUrl($user->cover_photo),
                'employee_id' => $user->employee?->id,
                'google_linked' => !empty($user->google_id),
            ],
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $user->update($request->only(['name', 'phone', 'location']));

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
                'location' => $user->location,
                'profile_picture' => $user->profile_picture,
                'cover_photo' => $this->resolveUrl($user->cover_photo),
            ],
        ]);
    }

    /**
     * Upload or update profile picture (mobile)
     */
    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $user = $request->user();

        // Delete old picture if it's a locally stored file (not a Google/external URL)
        if ($user->profile_picture && !str_starts_with($user->profile_picture, 'http')) {
            $oldPath = public_path($user->profile_picture);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // Create uploads directory if it doesn't exist
        $uploadDir = public_path('uploads/profile_pictures');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename and save the file
        $file = $request->file('profile_picture');
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadDir, $filename);

        // Store the full public URL so the mobile app can display it directly
        $fullUrl = url('uploads/profile_pictures/' . $filename);
        $user->profile_picture = $fullUrl;
        $user->save();

        return response()->json([
            'message' => 'Profile picture updated successfully.',
            'profile_picture' => $fullUrl,
        ]);
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|different:current_password',
            'confirm_password' => 'required|string|same:new_password',
        ]);

        $user = $request->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 422);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    }

    /**
     * Deactivate user account
     */
    public function deactivateAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();

        // Verify password before deactivation
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Password is incorrect'
            ], 422);
        }

        // Deactivate account
        $user->is_active = false;
        $user->save();

        // Revoke all tokens
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Account deactivated successfully'
        ]);
    }
}
