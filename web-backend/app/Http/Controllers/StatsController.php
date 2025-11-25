<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    /**
     * Get critical stats only (fast endpoint)
     * Cached for 2 minutes
     */
    public function summary()
    {
        $cacheKey = 'admin_stats_summary';
        
        $stats = Cache::remember($cacheKey, 120, function () {
            return [
                'totalUsers' => User::where('role', 'client')->count(),
                'totalAppointments' => Appointment::count(),
                'pendingAppointments' => Appointment::where('status', 'pending')->count(),
                'completedAppointments' => Appointment::where('status', 'completed')->count(),
            ];
        });

        return response()->json(['data' => $stats]);
    }

    /**
     * Get detailed statistics
     * This is the main stats endpoint called by admin dashboard
     */
    public function index()
    {
        $cacheKey = 'admin_stats_detailed';
        
        $stats = Cache::remember($cacheKey, 120, function () {
            // Use raw queries where possible for performance
            $totalUsers = DB::table('users')
                ->where('role', 'client')
                ->count();
            
            $totalStaff = DB::table('users')
                ->where('role', 'staff')
                ->count();
            
            $totalAppointments = DB::table('appointments')->count();
            
            $appointmentStats = DB::table('appointments')
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
                ->toArray();

            $revenue = DB::table('appointments')
                ->where('status', 'completed')
                ->count() * 0; // Placeholder - adjust based on your pricing

            return [
                'totalUsers' => $totalUsers,
                'totalStaff' => $totalStaff,
                'totalAppointments' => $totalAppointments,
                'pendingAppointments' => $appointmentStats['pending'] ?? 0,
                'approvedAppointments' => $appointmentStats['approved'] ?? 0,
                'completedAppointments' => $appointmentStats['completed'] ?? 0,
                'cancelledAppointments' => $appointmentStats['cancelled'] ?? 0,
                'revenue' => $revenue,
                'appointmentsByStatus' => [
                    ['label' => 'Pending', 'value' => $appointmentStats['pending'] ?? 0, 'color' => '#f59e0b'],
                    ['label' => 'Approved', 'value' => $appointmentStats['approved'] ?? 0, 'color' => '#3b82f6'],
                    ['label' => 'Completed', 'value' => $appointmentStats['completed'] ?? 0, 'color' => '#10b981'],
                    ['label' => 'Cancelled', 'value' => $appointmentStats['cancelled'] ?? 0, 'color' => '#ef4444'],
                ],
                'appointmentsByMonth' => $this->getAppointmentsByMonth(),
                'userGrowth' => $this->getUserGrowth(),
            ];
        });

        return response()->json(['data' => $stats]);
    }

    /**
     * Get appointments data grouped by month
     */
    private function getAppointmentsByMonth()
    {
        $appointments = DB::table('appointments')
            ->select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse();

        return $appointments->map(function ($item) {
            return [
                'label' => date('M Y', strtotime($item->month . '-01')),
                'value' => $item->count,
            ];
        })->values()->toArray();
    }

    /**
     * Get user growth data
     */
    private function getUserGrowth()
    {
        $users = DB::table('users')
            ->where('role', 'client')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse();

        return $users->map(function ($item) {
            return [
                'label' => date('M Y', strtotime($item->month . '-01')),
                'value' => $item->count,
            ];
        })->values()->toArray();
    }
}
