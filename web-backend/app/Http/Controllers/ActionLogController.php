<?php

namespace App\Http\Controllers;

use App\Models\ActionLog;
use Illuminate\Http\Request;

class ActionLogController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = ActionLog::with('user');

            // If user is not admin, only show their own logs
            if (!$request->user()->isAdmin()) {
                $query->where('user_id', $request->user()->id);
            }

            // Apply filters
            if ($request->has('action')) {
                $query->where('action', $request->action);
            }

            if ($request->has('model_type')) {
                $query->where('model_type', $request->model_type);
            }

            if ($request->has('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->has('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            $logs = $query->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 10));

            return response()->json($logs);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch action logs',
                'error' => $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    public function userLogs(Request $request)
    {
        try {
            $logs = ActionLog::where('user_id', $request->user()->id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 10));

            return response()->json([
                'data' => $logs->items(),
                'pagination' => [
                    'total' => $logs->total(),
                    'per_page' => $logs->perPage(),
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage()
                ],
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch your action logs',
                'error' => $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    public function adminLogs(Request $request)
    {
        try {
            $query = ActionLog::with('user');

            // Filter by user if specified
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Filter by action if specified
            if ($request->has('action')) {
                $query->where('action', $request->action);
            }

            $logs = $query->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 10));

            return response()->json([
                'data' => $logs->items(),
                'pagination' => [
                    'total' => $logs->total(),
                    'per_page' => $logs->perPage(),
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage()
                ],
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch admin logs',
                'error' => $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    public function getStats(Request $request)
    {
        try {
            $query = ActionLog::query();

            if (!$request->user()->isAdmin()) {
                $query->where('user_id', $request->user()->id);
            }

            $today = now()->startOfDay();
            $thisMonth = now()->startOfMonth();

            $stats = [
                'total_actions' => $query->count(),
                'today_actions' => $query->whereDate('created_at', $today)->count(),
                'this_month_actions' => $query->whereDate('created_at', '>=', $thisMonth)->count(),
                'by_action' => $query->selectRaw('action, COUNT(*) as count')
                    ->groupBy('action')
                    ->get()
                    ->pluck('count', 'action')
                    ->toArray()
            ];

            return response()->json([
                'data' => $stats,
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch action log statistics',
                'error' => $e->getMessage(),
                'success' => false
            ], 500);
        }
    }
}
