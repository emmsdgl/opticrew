<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HolidayController extends Controller
{
    /**
     * Store a new holiday.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date|unique:holidays,date',
                'name' => 'required|string|max:255',
            ]);

            $holiday = Holiday::create([
                'date' => $validated['date'],
                'name' => $validated['name'],
                'created_by' => Auth::id(),
            ]);

            Log::info("Holiday created", [
                'holiday_id' => $holiday->id,
                'date' => $holiday->date,
                'name' => $holiday->name,
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Holiday added successfully!',
                'holiday' => $holiday,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'A holiday already exists on this date.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error("Failed to create holiday", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add holiday: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete an existing holiday.
     */
    public function destroy($id)
    {
        try {
            $holiday = Holiday::findOrFail($id);

            Log::info("Deleting holiday", [
                'holiday_id' => $holiday->id,
                'date' => $holiday->date,
                'name' => $holiday->name,
                'deleted_by' => Auth::id(),
            ]);

            $holiday->delete();

            return response()->json([
                'success' => true,
                'message' => 'Holiday deleted successfully!',
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to delete holiday", [
                'holiday_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete holiday: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get holiday for a specific date.
     */
    public function getByDate(Request $request)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
            ]);

            $holiday = Holiday::where('date', $validated['date'])->first();

            return response()->json([
                'success' => true,
                'holiday' => $holiday,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch holiday: ' . $e->getMessage(),
            ], 500);
        }
    }
}
