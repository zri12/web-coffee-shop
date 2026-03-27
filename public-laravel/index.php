<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

function isLaravelAppRoot(string $path): bool
{
    return is_file($path.DIRECTORY_SEPARATOR.'bootstrap'.DIRECTORY_SEPARATOR.'app.php')
        && is_file($path.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php');
}

$appBasePath = null;

$envBasePath = getenv('LARAVEL_APP_BASE_PATH') ?: ($_SERVER['LARAVEL_APP_BASE_PATH'] ?? null);
if (is_string($envBasePath) && $envBasePath !== '' && isLaravelAppRoot($envBasePath)) {
    $appBasePath = $envBasePath;
}

if (!is_string($appBasePath)) {
    $searchRoots = [];
    for ($levels = 1; $levels <= 7; $levels++) {
        $searchRoots[] = dirname(__DIR__, $levels);
    }

    foreach ($searchRoots as $root) {
        $rootCandidates = [
            $root,
            $root.DIRECTORY_SEPARATOR.'laravel-app',
            $root.DIRECTORY_SEPARATOR.'WEB CAFFEE',
            $root.DIRECTORY_SEPARATOR.'web',
        ];

        foreach ($rootCandidates as $candidate) {
            if (isLaravelAppRoot($candidate)) {
                $appBasePath = $candidate;
                break 2;
            }
        }
    }
}

if (!is_string($appBasePath)) {
    $searchRoots = [];
    for ($levels = 1; $levels <= 7; $levels++) {
        $searchRoots[] = dirname(__DIR__, $levels);
    }

    foreach ($searchRoots as $root) {
        if (!is_dir($root)) {
            continue;
        }

        $checked = 0;
        foreach (new DirectoryIterator($root) as $entry) {
            if ($entry->isDot() || !$entry->isDir()) {
                continue;
            }

            $checked++;
            if ($checked > 80) {
                break;
            }

            $candidate = $entry->getPathname();
            if (isLaravelAppRoot($candidate)) {
                $appBasePath = $candidate;
                break 2;
            }
        }
    }
}

if (!is_string($appBasePath)) {
    $appBasePath = dirname(__DIR__);
}

putenv('APP_PUBLIC_PATH='.__DIR__);
$_ENV['APP_PUBLIC_PATH'] = __DIR__;
$_SERVER['APP_PUBLIC_PATH'] = __DIR__;

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $appBasePath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $appBasePath.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $appBasePath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
