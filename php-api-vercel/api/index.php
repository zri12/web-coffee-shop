<?php
header('Content-Type: application/json');

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = preg_replace('#^/api#', '', $path);

if ($path === '/ping') {
    require __DIR__ . '/ping.php';
    exit;
}

if ($path === '/menus') {
    require_once __DIR__ . '/db.php';
    try {
        $pdo = getPDO();
        $stmt = $pdo->query('SELECT * FROM menus');
        $menus = $stmt->fetchAll();
        echo json_encode(['menus' => $menus]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch menus']);
    }
    exit;
}

// 404 fallback
http_response_code(404);
echo json_encode(['error' => 'Not found']);
