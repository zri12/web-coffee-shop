<?php

declare(strict_types=1);

define('LARAVEL_START', microtime(true));

/**
 * Vercel functions run on a read-only filesystem except /tmp.
 * Prepare writable paths for Laravel runtime caches/logs/sessions.
 */
function getOriginalRequestUri(): string
{
    // Vercel forwards the original path via these headers when using rewrites
    $candidates = [
        $_SERVER['HTTP_X_ORIGINAL_PATHNAME'] ?? null,
        $_SERVER['HTTP_X_ORIGINAL_URI'] ?? null,
        $_SERVER['HTTP_X_FORWARDED_URI'] ?? null,
        $_SERVER['REQUEST_URI'] ?? '/',
    ];

    foreach ($candidates as $uri) {
        if (!empty($uri)) {
            return (string) $uri;
        }
    }

    return '/';
}

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

// Force serverless-safe runtime defaults.
setRuntimeEnv('LOG_CHANNEL', 'stderr');
setRuntimeEnv('LOG_STACK', 'stderr');
setRuntimeEnv('CACHE_STORE', getenv('CACHE_STORE') ?: 'array');
setRuntimeEnv('SESSION_DRIVER', getenv('SESSION_DRIVER') ?: 'cookie');

$originalUri  = getOriginalRequestUri();
$originalPath = parse_url($originalUri, PHP_URL_PATH) ?: '/';

// When Vercel rewrites to /api/index.php the original path can appear after that segment.
if (str_starts_with($originalPath, '/api/index.php/')) {
    $originalPath = substr($originalPath, strlen('/api/index.php'));
    $originalUri  = $originalPath . (parse_url($originalUri, PHP_URL_QUERY) ? '?'.parse_url($originalUri, PHP_URL_QUERY) : '');
}

// Fallback: derive original path from Vercel's capture header when available
if (($originalPath === '/api/index.php' || $originalPath === '/api/index.php/') && isset($_SERVER['HTTP_X_NOW_ROUTE_MATCHES'])) {
    // Header format example: "1=/api/order/ABC123/status"
    $matchesHeader = str_replace(';', '&', $_SERVER['HTTP_X_NOW_ROUTE_MATCHES']);
    parse_str($matchesHeader, $matchParts);
    if (!empty($matchParts[1])) {
        $originalPath = '/'.ltrim($matchParts[1], '/');
        $originalUri  = $originalPath . (parse_url($originalUri, PHP_URL_QUERY) ? '?'.parse_url($originalUri, PHP_URL_QUERY) : '');
    }
}

// Ensure Laravel sees the real requested path instead of the rewritten one
$_SERVER['REQUEST_URI'] = $originalUri;
$_SERVER['PATH_INFO']   = $originalPath;

/**
 * Lightweight edge handler for frequently polled API endpoints.
 * This avoids booting the whole Laravel kernel for simple JSON responses
 * and ensures Vercel rewrites still return JSON instead of HTML 404 pages.
 */
if (str_starts_with($originalPath, '/api/')) {
    header('Content-Type: application/json; charset=UTF-8');

    // Health check
    if ($originalPath === '/api/ping') {
        require __DIR__.'/ping.php';
        exit;
    }

    // Order status polling
    if (preg_match('#^/api/order/([^/]+)/status$#', $originalPath, $matches)) {
        require_once __DIR__.'/db.php';
        try {
            $pdo = getPDO();
            $stmt = $pdo->prepare('SELECT order_number, status, payment_status, payment_method, updated_at FROM orders WHERE order_number = ? LIMIT 1');
            $stmt->execute([$matches[1]]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                http_response_code(404);
                echo json_encode(['error' => 'Order not found']);
                exit;
            }

            echo json_encode([
                'success' => true,
                'order' => [
                    'order_number'   => $order['order_number'],
                    'status'         => $order['status'],
                    'payment_status' => $order['payment_status'],
                    'payment_method' => $order['payment_method'],
                    'updated_at'     => $order['updated_at'],
                ],
            ]);
            exit;
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch order status', 'details' => $e->getMessage()]);
            exit;
        }
    }
}

try {
    require __DIR__.'/../vendor/autoload.php';

    $app = require_once __DIR__.'/../bootstrap/app.php';

    $app->handleRequest(Illuminate\Http\Request::capture());
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Internal Server Error\n";
}
