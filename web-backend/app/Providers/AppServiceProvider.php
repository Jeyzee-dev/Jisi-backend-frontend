<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure rate limiting
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Sync default appointment types to services on app boot
        $this->syncDefaultServices();
    }

    /**
     * Sync default appointment types from Appointment model to Services table
     */
    private function syncDefaultServices(): void
    {
        try {
            // Only run in CLI or if not already synced
            if (php_sapi_name() === 'cli') {
                return; // Skip in CLI (migrations, commands, etc)
            }

            $appointmentTypes = \App\Models\Appointment::getTypes();
            
            foreach ($appointmentTypes as $key => $label) {
                // Check if service already exists (by exact label name)
                if (!\App\Models\Service::where('name', $label)->exists()) {
                    \App\Models\Service::create([
                        'name' => $label,
                        'description' => 'Predefined appointment type',
                        'is_active' => true
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Silently fail to not break the app
            \Illuminate\Support\Facades\Log::debug('Service sync failed: ' . $e->getMessage());
        }
    }
}