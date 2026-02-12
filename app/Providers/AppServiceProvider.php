<?php

namespace App\Providers;

use App\Services\SystemSettingsService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
    public function boot(SystemSettingsService $settingsService): void
    {
        if (env('VERCEL') || env('VERCEL_ENV') || $this->app->environment('production')) {
            URL::forceScheme('https');

            if ($vercelUrl = env('VERCEL_URL')) {
                URL::forceRootUrl('https://' . $vercelUrl);
                config(['app.url' => 'https://' . $vercelUrl]);
            }

            // Avoid mixed-content form submission and keep CSRF/session stable on HTTPS.
            config(['session.secure' => true]);
        }

        $settings = $settingsService->all();

        // Keep app.name synced with cafe name for mail, notifications, etc.
        config(['app.name' => $settings['cafe_name'] ?? config('app.name')]);

        // Share settings to all views so layouts and partials stay in sync.
        View::share('systemSettings', $settings);
    }
}
