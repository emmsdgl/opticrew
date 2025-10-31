<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the edit profile page based on user role
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $role = $user->role;

        if ($role === 'admin') {
            return view('admin.profile-edit', compact('user'));
        } elseif ($role === 'employee') {
            return view('employee.profile-edit', compact('user'));
        } else {
            return view('client.profile-edit', compact('user'));
        }
    }

    /**
     * Update the user's profile information
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Redirect back to profile page based on role
        $role = $user->role;
        if ($role === 'admin') {
            return Redirect::route('admin.profile')->with('success', 'Profile updated successfully!');
        } elseif ($role === 'employee') {
            return Redirect::route('employee.profile')->with('success', 'Profile updated successfully!');
        } else {
            return Redirect::route('client.profile')->with('success', 'Profile updated successfully!');
        }
    }

    /**
     * Upload or update profile picture
     */
    public function uploadPicture(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_picture' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = $request->user();

        // Delete old profile picture if exists
        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Store new profile picture
        $path = $request->file('profile_picture')->store('profile_pictures', 'public');

        // Update user record
        $user->profile_picture = $path;
        $user->save();

        // Redirect back to profile page based on role
        $role = $user->role;
        if ($role === 'admin') {
            return Redirect::route('admin.profile')->with('success', 'Profile picture updated successfully!');
        } elseif ($role === 'employee') {
            return Redirect::route('employee.profile')->with('success', 'Profile picture updated successfully!');
        } else {
            return Redirect::route('client.profile')->with('success', 'Profile picture updated successfully!');
        }
    }

    /**
     * Show settings page based on user role
     */
    public function settings(Request $request): View
    {
        $user = $request->user();
        $role = $user->role;

        if ($role === 'admin') {
            return view('admin.settings', compact('user'));
        } elseif ($role === 'employee') {
            return view('employee.settings', compact('user'));
        } else {
            return view('client.settings', compact('user'));
        }
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = $request->user();
        $user->password = bcrypt($request->password);
        $user->save();

        // Redirect back to settings page based on role
        $role = $user->role;
        if ($role === 'admin') {
            return Redirect::route('admin.settings')->with('success', 'Password updated successfully!');
        } elseif ($role === 'employee') {
            return Redirect::route('employee.settings')->with('success', 'Password updated successfully!');
        } else {
            return Redirect::route('client.settings')->with('success', 'Password updated successfully!');
        }
    }

    /**
     * Delete the user's account
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        // Delete profile picture if exists
        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
