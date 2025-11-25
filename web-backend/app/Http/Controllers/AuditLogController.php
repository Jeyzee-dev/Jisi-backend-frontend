<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Only admins can view audit logs
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $action = $request->get('action');
        $entityType = $request->get('entity_type');
        $userId = $request->get('user_id');
        $status = $request->get('status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $perPage = $request->get('per_page', 50);

        $query = AuditLog::with('user');

        if ($action) {
            $query->where('action', $action);
        }

        if ($entityType) {
            $query->where('entity_type', $entityType);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $logs = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $log = AuditLog::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $log
        ]);
    }

    public function getUserActivityReport($userId, Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = AuditLog::where('user_id', $userId);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $report = [
            'total_actions' => $query->count(),
            'actions_by_type' => $query->groupBy('action')->selectRaw('action, count(*) as count')->get(),
            'actions_by_entity' => $query->groupBy('entity_type')->selectRaw('entity_type, count(*) as count')->get(),
            'failed_actions' => $query->where('status', 'failed')->count(),
            'recent_activity' => $query->latest()->limit(100)->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    public function securityReport(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());

        $report = [
            'total_events' => AuditLog::whereBetween('created_at', [$startDate, $endDate])->count(),
            'failed_actions' => AuditLog::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'failed')
                ->count(),
            'unauthorized_attempts' => AuditLog::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'unauthorized')
                ->count(),
            'events_by_action' => AuditLog::whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('action')
                ->selectRaw('action, count(*) as count')
                ->get(),
            'top_users' => AuditLog::whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('user_id')
                ->selectRaw('user_id, count(*) as count')
                ->orderByDesc('count')
                ->limit(10)
                ->with('user')
                ->get(),
            'recent_failures' => AuditLog::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', '!=', 'success')
                ->latest()
                ->limit(20)
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }
}
