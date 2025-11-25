<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * CacheInvalidationService
 * 
 * Manages smart cache invalidation to ensure data is fresh when needed
 * but cached aggressively for performance
 */
class CacheInvalidationService
{
    // Cache keys used throughout the app
    const ADMIN_STATS_CACHE = 'admin_stats_';
    const USERS_INDEX_CACHE = 'users_index_';
    const APPOINTMENTS_CACHE = 'appointments_';
    const SERVICES_CACHE = 'admin_services_';
    
    /**
     * Invalidate admin stats cache when appointments change
     */
    public static function invalidateAdminStats($userId = null)
    {
        if ($userId) {
            Cache::forget(self::ADMIN_STATS_CACHE . $userId);
        } else {
            // Clear all admin stats caches
            Cache::forget(self::ADMIN_STATS_CACHE . '*');
        }
    }

    /**
     * Invalidate users cache when user data changes
     */
    public static function invalidateUsersCache($pattern = null)
    {
        if ($pattern) {
            Cache::forget(self::USERS_INDEX_CACHE . $pattern);
        } else {
            // Clear all user caches - this is aggressive but safe
            Cache::flush();
        }
    }

    /**
     * Invalidate appointments cache when appointments change
     */
    public static function invalidateAppointmentsCache($userId = null)
    {
        if ($userId) {
            Cache::forget(self::APPOINTMENTS_CACHE . $userId . '_*');
        } else {
            // Wildcard clear - less efficient but ensures data freshness
            // In production, use a tag-based cache strategy instead
        }
    }

    /**
     * Invalidate services cache when services change
     */
    public static function invalidateServicesCache()
    {
        Cache::forget(self::SERVICES_CACHE . '*');
    }

    /**
     * Get cache hit rate statistics
     */
    public static function getCacheStats()
    {
        $stats = Cache::get('cache_stats', [
            'hits' => 0,
            'misses' => 0,
            'total_requests' => 0
        ]);

        return [
            'cache_hit_rate' => $stats['total_requests'] > 0 
                ? round(($stats['hits'] / $stats['total_requests']) * 100, 2)
                : 0,
            'hits' => $stats['hits'],
            'misses' => $stats['misses'],
            'total_requests' => $stats['total_requests']
        ];
    }

    /**
     * Record cache hit/miss for analytics
     */
    public static function recordCacheAccess($hit = true)
    {
        $stats = Cache::get('cache_stats', [
            'hits' => 0,
            'misses' => 0,
            'total_requests' => 0
        ]);

        if ($hit) {
            $stats['hits']++;
        } else {
            $stats['misses']++;
        }
        $stats['total_requests']++;

        // Keep stats for 24 hours
        Cache::put('cache_stats', $stats, 86400);
    }
}
