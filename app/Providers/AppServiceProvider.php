<?php

namespace App\Providers;

use App\Services\SystemSettingsService;
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
        $settings = $settingsService->all();

        // Keep app.name synced with cafe name for mail, notifications, etc.
        config(['app.name' => $settings['cafe_name'] ?? config('app.name')]);

        // Share settings to all views so layouts and partials stay in sync.
        View::share('systemSettings', $settings);
    }
}
