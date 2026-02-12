<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

$status = 'ok';
$db_status = 'disconnected';

try {
    $pdo = getPDO();
    $pdo->query('SELECT 1');
    $db_status = 'connected';
} catch (Exception $e) {
    $db_status = 'error';
}

echo json_encode([
    'status' => $status,
    'db' => $db_status
]);