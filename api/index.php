<?php

declare(strict_types=1);

define('LARAVEL_START', microtime(true));

/**
 * Vercel functions run on a read-only filesystem except /tmp.
 * Prepare writable paths for Laravel runtime caches/logs/sessions.
 */
function setRuntimeEnv(string $key, string $value): void
{
    putenv($key.'='.$value);
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

function ensureDir(string $path): void
{
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

if (getenv('VERCEL') !== false || getenv('VERCEL_ENV') !== false) {
    $tmpRoot = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'web-coffee-shop';
    $storagePath = $tmpRoot.DIRECTORY_SEPARATOR.'storage';
    $bootstrapCachePath = $tmpRoot.DIRECTORY_SEPARATOR.'bootstrap'.DIRECTORY_SEPARATOR.'cache';

    ensureDir($storagePath);
    ensureDir($storagePath.DIRECTORY_SEPARATOR.'framework');
    ensureDir($storagePath.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'cache');
    ensureDir($storagePath.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'data');
    ensureDir($storagePath.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'sessions');
    ensureDir($storagePath.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'views');
    ensureDir($storagePath.DIRECTORY_SEPARATOR.'logs');
    ensureDir($bootstrapCachePath);

    setRuntimeEnv('APP_STORAGE_PATH', $storagePath);
    setRuntimeEnv('APP_SERVICES_CACHE', $bootstrapCachePath.DIRECTORY_SEPARATOR.'services.php');
    setRuntimeEnv('APP_PACKAGES_CACHE', $bootstrapCachePath.DIRECTORY_SEPARATOR.'packages.php');
    setRuntimeEnv('APP_CONFIG_CACHE', $bootstrapCachePath.DIRECTORY_SEPARATOR.'config.php');
    setRuntimeEnv('APP_ROUTES_CACHE', $bootstrapCachePath.DIRECTORY_SEPARATOR.'routes.php');
    setRuntimeEnv('APP_EVENTS_CACHE', $bootstrapCachePath.DIRECTORY_SEPARATOR.'events.php');
    setRuntimeEnv('VIEW_COMPILED_PATH', $storagePath.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'views');

    // Force serverless-safe defaults if env vars are missing in Vercel project settings.
    if (!getenv('LOG_CHANNEL')) {
        setRuntimeEnv('LOG_CHANNEL', 'stderr');
    }
    if (!getenv('LOG_STACK')) {
        setRuntimeEnv('LOG_STACK', 'stderr');
    }
    if (!getenv('CACHE_STORE')) {
        setRuntimeEnv('CACHE_STORE', 'array');
    }
    if (!getenv('SESSION_DRIVER')) {
        setRuntimeEnv('SESSION_DRIVER', 'cookie');
    }
}

try {
    require __DIR__.'/../vendor/autoload.php';

    $app = require_once __DIR__.'/../bootstrap/app.php';

    $app->handleRequest(Illuminate\Http\Request::capture());
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');

    echo "Bootstrap error\n";
    echo $e->getMessage()."\n";
    echo $e->getFile().':'.$e->getLine()."\n";
}
