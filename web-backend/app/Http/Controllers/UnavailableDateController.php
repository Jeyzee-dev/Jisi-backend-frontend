<?php

namespace App\Http\Controllers;

use App\Models\UnavailableDate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UnavailableDateController extends Controller
{
    public function index()
    {
        try {
            Log::info('Fetching unavailable dates');
            $dates = UnavailableDate::orderBy('date', 'desc')->get();
            
            Log::info('Found ' . $dates->count() . ' unavailable dates');
            return response()->json([
                'data' => $dates,
                'success' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch unavailable dates: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch unavailable dates',
                'error' => $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    public function store(Request $request)
    {
        Log::info('Creating unavailable date', $request->all());

        $request->validate([
            'date' => 'required|date|after:today',
            'reason' => 'nullable|string|max:255',
            'all_day' => 'boolean',
            'start_time' => 'required_if:all_day,false|nullable|date_format:H:i',
            'end_time' => 'required_if:all_day,false|nullable|date_format:H:i|after:start_time',
        ]);

        try {
            // Check if date already exists
            $existingDate = UnavailableDate::where('date', $request->date)->first();
            if ($existingDate) {
                Log::warning('Date already exists: ' . $request->date);
                return response()->json([
                    'message' => 'This date is already marked as unavailable',
                    'success' => false
                ], 409);
            }

            Log::info('Creating new unavailable date');
            $unavailableDate = UnavailableDate::create([
                'date' => $request->date,
                'reason' => $request->reason,
                'all_day' => $request->all_day ?? true,
                'start_time' => $request->all_day ? null : $request->start_time,
                'end_time' => $request->all_day ? null : $request->end_time,
                // REMOVED: 'created_by' => Auth::id(),
            ]);

            Log::info('Unavailable date created successfully with ID: ' . $unavailableDate->id);
            return response()->json([
                'data' => $unavailableDate,
                'message' => 'Unavailable date added successfully',
                'success' => true
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create unavailable date: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'message' => 'Failed to create unavailable date',
                'error' => $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            Log::info('Deleting unavailable date with ID: ' . $id);
            $date = UnavailableDate::findOrFail($id);
            $date->delete();

            Log::info('Unavailable date deleted successfully');
            return response()->json([
                'message' => 'Unavailable date deleted successfully',
                'success' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete unavailable date: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete unavailable date',
                'error' => $e->getMessage(),
                'success' => false
            ], 500);
        }
    }
}