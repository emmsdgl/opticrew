<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    /**
     * Display a listing of all user accounts (excluding admin).
     */
    public function index(Request $request)
    {
        $accountType = $request->get('type', 'all');

        $query = User::with(['employee', 'client'])
            ->where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc');

        // Filter by account type
        if ($accountType === 'employees') {
            $query->where('role', 'employee');
        } elseif ($accountType === 'company') {
            $query->where('role', 'external_client')
                  ->whereHas('client', function($q) {
                      $q->where('client_type', 'company');
                  });
        } elseif ($accountType === 'personal') {
            $query->where('role', 'external_client')
                  ->whereHas('client', function($q) {
                      $q->where('client_type', 'personal');
                  });
        }

        $users = $query->paginate(15);

        // Get counts for each type
        $employeesCount = User::where('role', 'employee')->count();
        $companyCount = User::where('role', 'external_client')
            ->whereHas('client', function($q) {
                $q->where('client_type', 'company');
            })->count();
        $personalCount = User::where('role', 'external_client')
            ->whereHas('client', function($q) {
                $q->where('client_type', 'personal');
            })->count();

        return view('admin.accounts.index', compact('users', 'accountType', 'employeesCount', 'companyCount', 'personalCount'));
    }

    /**
     * Show the form for creating a new account.
     */
    public function create()
    {
        return view('admin.accounts.create');
    }

    /**
     * Store a newly created employee account in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'skills' => ['nullable', 'array'],
            'years_of_experience' => ['required', 'integer', 'min:0'],
            'salary_per_hour' => ['required', 'numeric', 'min:0'],
            'has_driving_license' => ['nullable', 'boolean'],
            'efficiency' => ['required', 'numeric', 'min:0.01', 'max:9.99'],
        ]);

        // Create user account
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'employee',
            'email_verified_at' => now(),
        ]);

        // Create employee record
        Employee::create([
            'user_id' => $user->id,
            'skills' => json_encode($request->skills ?? []),
            'is_active' => true,
            'is_day_off' => false,
            'is_busy' => false,
            'efficiency' => $request->efficiency,
            'has_driving_license' => $request->has_driving_license ?? false,
            'years_of_experience' => $request->years_of_experience,
            'salary_per_hour' => $request->salary_per_hour,
            'months_employed' => 0,
        ]);

        return redirect()->route('admin.accounts.index', ['type' => 'employees'])
            ->with('success', 'Employee account created successfully.');
    }

    /**
     * Display the specified account.
     */
    public function show($id)
    {
        $user = User::with(['employee', 'client'])->findOrFail($id);

        // Prevent viewing admin accounts
        if ($user->role === 'admin') {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.accounts.show', compact('user'));
    }

    /**
     * Show the form for editing the specified account.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        // Prevent editing admin accounts
        if ($user->role === 'admin') {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.accounts.edit', compact('user'));
    }

    /**
     * Update the specified account in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prevent updating admin accounts
        if ($user->role === 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('admin.accounts.index')
            ->with('success', 'Account updated successfully.');
    }

    /**
     * Remove the specified account from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting admin accounts
        if ($user->role === 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $user->delete();

        return redirect()->route('admin.accounts.index')
            ->with('success', 'Account deleted successfully.');
    }

    /**
     * Verify admin password for deletion confirmation.
     */
    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        $admin = Auth::user();

        // Verify the admin password
        if (Hash::check($request->password, $admin->password)) {
            return response()->json(['valid' => true]);
        }

        return response()->json(['valid' => false], 200);
    }

    /**
     * Display deleted (archived) employee accounts.
     */
    public function archived(Request $request)
    {
        $accountType = $request->get('type', 'all');

        $query = User::onlyTrashed()
            ->with(['employee', 'client'])
            ->where('role', '!=', 'admin')
            ->orderBy('deleted_at', 'desc');

        // Filter by account type
        if ($accountType === 'employees') {
            $query->where('role', 'employee');
        } elseif ($accountType === 'company') {
            $query->where('role', 'external_client')
                  ->whereHas('client', function($q) {
                      $q->where('client_type', 'company');
                  });
        } elseif ($accountType === 'personal') {
            $query->where('role', 'external_client')
                  ->whereHas('client', function($q) {
                      $q->where('client_type', 'personal');
                  });
        }

        $users = $query->paginate(15);

        // Get counts for each type
        $employeesCount = User::onlyTrashed()->where('role', 'employee')->count();
        $companyCount = User::onlyTrashed()->where('role', 'external_client')
            ->whereHas('client', function($q) {
                $q->where('client_type', 'company');
            })->count();
        $personalCount = User::onlyTrashed()->where('role', 'external_client')
            ->whereHas('client', function($q) {
                $q->where('client_type', 'personal');
            })->count();

        return view('admin.accounts.archived', compact('users', 'accountType', 'employeesCount', 'companyCount', 'personalCount'));
    }

    /**
     * Restore a soft-deleted account.
     */
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        // Prevent restoring admin accounts
        if ($user->role === 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $user->restore();

        // If it's an employee, also restore the employee record
        if ($user->role === 'employee' && $user->employee()->onlyTrashed()->exists()) {
            $user->employee()->restore();
        }

        // If it's a client, also restore the client record
        if ($user->role === 'external_client' && $user->client()->onlyTrashed()->exists()) {
            $user->client()->restore();
        }

        return redirect()->route('admin.accounts.archived')
            ->with('success', 'Account restored successfully.');
    }

    /**
     * Permanently delete a soft-deleted account.
     */
    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        // Prevent force deleting admin accounts
        if ($user->role === 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $user->forceDelete();

        return redirect()->route('admin.accounts.archived')
            ->with('success', 'Account permanently deleted.');
    }
}
