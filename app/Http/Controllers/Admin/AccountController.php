<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\Client;
use App\Models\ContractedClient;
use App\Models\Location;
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

        $query = User::with(['employee', 'client', 'contractedClient'])
            ->where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc');

        // Filter by account type
        if ($accountType === 'employees') {
            $query->where('role', 'employee');
        } elseif ($accountType === 'contracted_company') {
            $query->where('role', 'company');
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
        $contractedCompanyCount = User::where('role', 'company')->count();
        $companyCount = User::where('role', 'external_client')
            ->whereHas('client', function($q) {
                $q->where('client_type', 'company');
            })->count();
        $personalCount = User::where('role', 'external_client')
            ->whereHas('client', function($q) {
                $q->where('client_type', 'personal');
            })->count();

        return view('admin.accounts.index', compact('users', 'accountType', 'employeesCount', 'contractedCompanyCount', 'companyCount', 'personalCount'));
    }

    /**
     * Show the form for creating a new account.
     */
    public function create(Request $request)
    {
        $accountType = $request->get('type', 'employee');
        $contractedClients = ContractedClient::all();
        return view('admin.accounts.create', compact('accountType', 'contractedClients'));
    }

    /**
     * Store a newly created account in storage.
     */
    public function store(Request $request)
    {
        $accountType = $request->input('account_type', 'employee');

        if ($accountType === 'company') {
            return $this->storeCompanyAccount($request);
        }

        // Employee account validation and creation
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
     * Store a newly created company account
     */
    protected function storeCompanyAccount(Request $request)
    {
        $request->validate([
            'is_existing' => ['required', 'in:0,1'],
            'existing_company_id' => ['required_if:is_existing,1', 'nullable', 'exists:contracted_clients,id'],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'business_id' => ['nullable', 'string', 'max:50'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if ($request->is_existing == '1' && $request->existing_company_id) {
            // Use existing contracted client and update it
            $contractedClient = ContractedClient::findOrFail($request->existing_company_id);

            // Update the contracted client with any new/updated info
            $contractedClient->update([
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'business_id' => $request->business_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);
        } else {
            // Create new contracted client
            $contractedClient = ContractedClient::create([
                'name' => $request->company_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'business_id' => $request->business_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);
        }

        // Create user account (username = company name)
        $user = User::create([
            'name' => $contractedClient->name,
            'username' => strtolower(str_replace(' ', '_', $contractedClient->name)),
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'company',
            'email_verified_at' => now(),
        ]);

        // Link contracted client to user
        $contractedClient->update(['user_id' => $user->id]);

        return redirect()->route('admin.accounts.index', ['type' => 'contracted_company'])
            ->with('success', 'Company account created successfully for ' . $contractedClient->name);
    }

    /**
     * Display the specified account.
     */
    public function show($id)
    {
        $user = User::with(['employee', 'client', 'contractedClient.locations'])->findOrFail($id);

        // Prevent viewing admin accounts
        if ($user->role === 'admin') {
            abort(403, 'Unauthorized action.');
        }

        // Group locations by location_type for cabin types management
        $cabinTypes = [];
        if ($user->contractedClient) {
            $cabinTypes = $user->contractedClient->locations()
                ->selectRaw('location_type, COUNT(*) as units,
                            MAX(base_cleaning_duration_minutes) as base_cleaning_duration_minutes,
                            MAX(normal_rate_per_hour) as normal_rate_per_hour,
                            MAX(sunday_holiday_rate) as sunday_holiday_rate,
                            MAX(deep_cleaning_rate) as deep_cleaning_rate,
                            MAX(light_deep_cleaning_rate) as light_deep_cleaning_rate,
                            MAX(student_rate) as student_rate,
                            MAX(student_sunday_holiday_rate) as student_sunday_holiday_rate')
                ->groupBy('location_type')
                ->get();
        }

        return view('admin.accounts.show', compact('user', 'cabinTypes'));
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

    /**
     * Add a new location/cabin for a contracted client company
     */
    public function addLocation(Request $request, $userId)
    {
        $user = User::with('contractedClient')->findOrFail($userId);

        if ($user->role !== 'company' || !$user->contractedClient) {
            return response()->json(['error' => 'Invalid company account'], 400);
        }

        $request->validate([
            'cabin_name' => ['required', 'string', 'max:255'],
            'units' => ['required', 'integer', 'min:1'],
            'normal_rate_per_hour' => ['required', 'numeric', 'min:0'],
            'student_rate' => ['nullable', 'numeric', 'min:0'],
            'sunday_holiday_rate' => ['required', 'numeric', 'min:0'],
            'student_sunday_holiday_rate' => ['nullable', 'numeric', 'min:0'],
            'estimated_cleaning_time' => ['required', 'integer', 'min:1'],
        ]);

        $location = Location::create([
            'contracted_client_id' => $user->contractedClient->id,
            'cabin_name' => $request->cabin_name,
            'units' => $request->units,
            'normal_rate_per_hour' => $request->normal_rate_per_hour,
            'student_rate' => $request->student_rate,
            'sunday_holiday_rate' => $request->sunday_holiday_rate,
            'student_sunday_holiday_rate' => $request->student_sunday_holiday_rate,
            'estimated_cleaning_time' => $request->estimated_cleaning_time,
            'latitude' => $user->contractedClient->latitude,
            'longitude' => $user->contractedClient->longitude,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location added successfully',
            'location' => $location
        ]);
    }

    /**
     * Update a location/cabin
     */
    public function updateLocation(Request $request, $userId, $locationId)
    {
        $user = User::with('contractedClient')->findOrFail($userId);

        if ($user->role !== 'company' || !$user->contractedClient) {
            return response()->json(['error' => 'Invalid company account'], 400);
        }

        $location = Location::where('contracted_client_id', $user->contractedClient->id)
            ->findOrFail($locationId);

        $request->validate([
            'cabin_name' => ['required', 'string', 'max:255'],
            'units' => ['required', 'integer', 'min:1'],
            'normal_rate_per_hour' => ['required', 'numeric', 'min:0'],
            'student_rate' => ['nullable', 'numeric', 'min:0'],
            'sunday_holiday_rate' => ['required', 'numeric', 'min:0'],
            'student_sunday_holiday_rate' => ['nullable', 'numeric', 'min:0'],
            'estimated_cleaning_time' => ['required', 'integer', 'min:1'],
        ]);

        $location->update([
            'cabin_name' => $request->cabin_name,
            'units' => $request->units,
            'normal_rate_per_hour' => $request->normal_rate_per_hour,
            'student_rate' => $request->student_rate,
            'sunday_holiday_rate' => $request->sunday_holiday_rate,
            'student_sunday_holiday_rate' => $request->student_sunday_holiday_rate,
            'estimated_cleaning_time' => $request->estimated_cleaning_time,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'location' => $location
        ]);
    }

    /**
     * Delete a location/cabin
     */
    public function deleteLocation($userId, $locationId)
    {
        $user = User::with('contractedClient')->findOrFail($userId);

        if ($user->role !== 'company' || !$user->contractedClient) {
            return response()->json(['error' => 'Invalid company account'], 400);
        }

        $location = Location::where('contracted_client_id', $user->contractedClient->id)
            ->findOrFail($locationId);

        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'Location deleted successfully'
        ]);
    }

    /**
     * Add a new cabin type (creates multiple location records)
     */
    public function addCabinType(Request $request, $userId)
    {
        $user = User::with('contractedClient')->findOrFail($userId);

        if ($user->role !== 'company' || !$user->contractedClient) {
            return response()->json(['error' => 'Invalid company account'], 400);
        }

        $request->validate([
            'location_type' => ['required', 'string', 'max:255'],
            'units' => ['required', 'integer', 'min:1'],
            'normal_rate_per_hour' => ['required', 'numeric', 'min:0'],
            'student_rate' => ['nullable', 'numeric', 'min:0'],
            'sunday_holiday_rate' => ['required', 'numeric', 'min:0'],
            'student_sunday_holiday_rate' => ['nullable', 'numeric', 'min:0'],
            'deep_cleaning_rate' => ['nullable', 'numeric', 'min:0'],
            'light_deep_cleaning_rate' => ['nullable', 'numeric', 'min:0'],
            'base_cleaning_duration_minutes' => ['required', 'integer', 'min:1'],
        ]);

        // Check if cabin type already exists
        $existingCount = Location::where('contracted_client_id', $user->contractedClient->id)
            ->where('location_type', $request->location_type)
            ->count();

        if ($existingCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cabin type already exists'
            ], 422);
        }

        // Create multiple location records based on units count
        $units = $request->units;
        for ($i = 1; $i <= $units; $i++) {
            $locationName = $units == 1 ? $request->location_type : $request->location_type . ' #' . $i;

            Location::create([
                'contracted_client_id' => $user->contractedClient->id,
                'location_name' => $locationName,
                'location_type' => $request->location_type,
                'base_cleaning_duration_minutes' => $request->base_cleaning_duration_minutes,
                'normal_rate_per_hour' => $request->normal_rate_per_hour,
                'student_rate' => $request->student_rate,
                'sunday_holiday_rate' => $request->sunday_holiday_rate,
                'student_sunday_holiday_rate' => $request->student_sunday_holiday_rate,
                'deep_cleaning_rate' => $request->deep_cleaning_rate,
                'light_deep_cleaning_rate' => $request->light_deep_cleaning_rate,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Cabin type '{$request->location_type}' with {$units} units added successfully"
        ]);
    }

    /**
     * Update a cabin type (updates all locations of that type)
     */
    public function updateCabinType(Request $request, $userId, $locationType)
    {
        $user = User::with('contractedClient')->findOrFail($userId);

        if ($user->role !== 'company' || !$user->contractedClient) {
            return response()->json(['error' => 'Invalid company account'], 400);
        }

        $request->validate([
            'units' => ['required', 'integer', 'min:1'],
            'normal_rate_per_hour' => ['required', 'numeric', 'min:0'],
            'student_rate' => ['nullable', 'numeric', 'min:0'],
            'sunday_holiday_rate' => ['required', 'numeric', 'min:0'],
            'student_sunday_holiday_rate' => ['nullable', 'numeric', 'min:0'],
            'deep_cleaning_rate' => ['nullable', 'numeric', 'min:0'],
            'light_deep_cleaning_rate' => ['nullable', 'numeric', 'min:0'],
            'base_cleaning_duration_minutes' => ['required', 'integer', 'min:1'],
        ]);

        // Get all existing locations of this type
        $existingLocations = Location::where('contracted_client_id', $user->contractedClient->id)
            ->where('location_type', $locationType)
            ->get();

        $currentUnits = $existingLocations->count();
        $newUnits = $request->units;

        // Update all existing locations with new rates
        foreach ($existingLocations as $location) {
            $location->update([
                'base_cleaning_duration_minutes' => $request->base_cleaning_duration_minutes,
                'normal_rate_per_hour' => $request->normal_rate_per_hour,
                'student_rate' => $request->student_rate,
                'sunday_holiday_rate' => $request->sunday_holiday_rate,
                'student_sunday_holiday_rate' => $request->student_sunday_holiday_rate,
                'deep_cleaning_rate' => $request->deep_cleaning_rate,
                'light_deep_cleaning_rate' => $request->light_deep_cleaning_rate,
            ]);
        }

        // Handle unit count changes
        if ($newUnits > $currentUnits) {
            // Add more units
            for ($i = $currentUnits + 1; $i <= $newUnits; $i++) {
                $locationName = $newUnits == 1 ? $locationType : $locationType . ' #' . $i;

                Location::create([
                    'contracted_client_id' => $user->contractedClient->id,
                    'location_name' => $locationName,
                    'location_type' => $locationType,
                    'base_cleaning_duration_minutes' => $request->base_cleaning_duration_minutes,
                    'normal_rate_per_hour' => $request->normal_rate_per_hour,
                    'student_rate' => $request->student_rate,
                    'sunday_holiday_rate' => $request->sunday_holiday_rate,
                    'student_sunday_holiday_rate' => $request->student_sunday_holiday_rate,
                    'deep_cleaning_rate' => $request->deep_cleaning_rate,
                    'light_deep_cleaning_rate' => $request->light_deep_cleaning_rate,
                ]);
            }
        } elseif ($newUnits < $currentUnits) {
            // Soft delete excess units (keep the first N units)
            $locationsToDelete = Location::where('contracted_client_id', $user->contractedClient->id)
                ->where('location_type', $locationType)
                ->orderBy('id', 'desc')
                ->take($currentUnits - $newUnits)
                ->get();

            foreach ($locationsToDelete as $location) {
                $location->delete(); // Soft delete
            }
        }

        // If units changed from multiple to 1, or vice versa, update naming
        if ($newUnits == 1) {
            $location = Location::where('contracted_client_id', $user->contractedClient->id)
                ->where('location_type', $locationType)
                ->first();
            if ($location) {
                $location->update(['location_name' => $locationType]);
            }
        } elseif ($currentUnits == 1 && $newUnits > 1) {
            // Rename from "Small Cabin" to "Small Cabin #1"
            $location = Location::where('contracted_client_id', $user->contractedClient->id)
                ->where('location_type', $locationType)
                ->first();
            if ($location) {
                $location->update(['location_name' => $locationType . ' #1']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Cabin type '{$locationType}' updated successfully"
        ]);
    }

    /**
     * Delete a cabin type (soft deletes all locations of that type)
     */
    public function deleteCabinType($userId, $locationType)
    {
        $user = User::with('contractedClient')->findOrFail($userId);

        if ($user->role !== 'company' || !$user->contractedClient) {
            return response()->json(['error' => 'Invalid company account'], 400);
        }

        $locations = Location::where('contracted_client_id', $user->contractedClient->id)
            ->where('location_type', $locationType)
            ->get();

        if ($locations->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cabin type not found'
            ], 404);
        }

        $count = $locations->count();

        // Soft delete all locations of this type
        foreach ($locations as $location) {
            $location->delete();
        }

        return response()->json([
            'success' => true,
            'message' => "Cabin type '{$locationType}' ({$count} units) deleted successfully"
        ]);
    }

    /**
     * Update contracted client details (latitude, longitude, etc.)
     */
    public function updateContractedClient(Request $request, $userId)
    {
        $user = User::with('contractedClient')->findOrFail($userId);

        if ($user->role !== 'company' || !$user->contractedClient) {
            return redirect()->back()->with('error', 'Invalid company account');
        }

        $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:500'],
            'business_id' => ['nullable', 'string', 'max:50'],
        ]);

        $user->contractedClient->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'business_id' => $request->business_id,
        ]);

        return redirect()->back()->with('success', 'Company details updated successfully');
    }
}
