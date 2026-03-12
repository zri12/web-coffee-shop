<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Create application
$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Respect X-Forwarded-* headers from Vercel/edge proxies.
        $middleware->trustProxies(at: '*');

        // API middleware
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Alias for role middleware
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // Auto-cancel pesanan yang sudah > 24 jam dan masih berstatus aktif.
        // Dijalankan pada semua request web yang terautentikasi (semua role).
        $middleware->appendToGroup('web', \App\Http\Middleware\AutoCancelStaleOrders::class);

        // CORS handling
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

if (($storagePath = env('APP_STORAGE_PATH')) && is_string($storagePath)) {
    $app->useStoragePath($storagePath);
}

$publicPath = env('APP_PUBLIC_PATH');

if (is_string($publicPath) && $publicPath !== '') {
    $app->usePublicPath($publicPath);
} elseif (is_dir($app->basePath('public-laravel'))) {
    $app->usePublicPath($app->basePath('public-laravel'));
} else {
    $app->usePublicPath($app->basePath('public'));
}

return $app;
