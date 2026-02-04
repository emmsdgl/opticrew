<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeRequestsController extends Controller
{
    public function create()
    {
        $employee = Auth::user()->employee;
        $currentStep = 1;
        
        return view('employee.requests.create', compact('employee', 'currentStep'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'absence_type' => 'required|string|max:255',
            'absence_date' => 'required|date|after_or_equal:today',
            'time_range' => 'required|string|max:255',
            'from_time' => 'nullable|required_if:time_range,Custom Hours',
            'to_time' => 'nullable|required_if:time_range,Custom Hours',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:350',
            'proof_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240', // 10MB max
        ]);

        $employee = Auth::user()->employee;

        // Handle file upload if present
        $proofPath = null;
        if ($request->hasFile('proof_document')) {
            $proofPath = $request->file('proof_document')->store('employee-requests', 'public');
        }

        // Create the request in your database
        // Adjust this based on your actual database schema
        $employeeRequest = \App\Models\EmployeeRequest::create([
            'employee_id' => $employee->id,
            'absence_type' => $validated['absence_type'],
            'absence_date' => $validated['absence_date'],
            'time_range' => $validated['time_range'],
            'from_time' => $validated['from_time'] ?? null,
            'to_time' => $validated['to_time'] ?? null,
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'proof_document' => $proofPath,
            'status' => 'Pending', // Default status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your absence request has been submitted successfully!',
            'redirect_url' => route('employee.dashboard')
        ]);
    }

    /**
     * Cancel an employee request (only pending requests)
     */
    public function cancel($id)
    {
        $employee = Auth::user()->employee;

        $employeeRequest = \App\Models\EmployeeRequest::where('id', $id)
            ->where('employee_id', $employee->id)
            ->first();

        if (!$employeeRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found'
            ], 404);
        }

        if ($employeeRequest->status !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending requests can be cancelled'
            ], 400);
        }

        $employeeRequest->update([
            'status' => 'Cancelled'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request cancelled successfully'
        ]);
    }
}