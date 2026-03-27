<?php
header('Content-Type: text/plain');
echo "Current directory: " . getcwd() . "\n";
echo "Base path (guessed): " . realpath(__DIR__ . '/..') . "\n";

function listDirRecursively($dir, $level = 0) {
    if (!is_dir($dir)) return;
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . '/' . $file;
        echo str_repeat('  ', $level) . $file . (is_dir($path) ? '/' : '') . "\n";
        if (is_dir($path) && $level < 3) {
            listDirRecursively($path, $level + 1);
        }
    }
}

echo "\n--- Root files ---\n";
listDirRecursively(realpath(__DIR__ . '/..'));

echo "\n--- storage/app/public ---\n";
listDirRecursively(realpath(__DIR__ . '/../storage/app/public'));
