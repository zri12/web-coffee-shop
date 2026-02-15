<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- DEBUG START ---\n";

// Check User
try {
    $user = App\Models\User::where('email', 'manager@cafe.com')->first();
    if ($user) {
        echo "User Found: {$user->email}\n";
        echo "Role: {$user->role}\n";
    } else {
        echo "User 'manager@cafe.com' NOT FOUND in remote database.\n";
    }
} catch (\Exception $e) {
    echo "Error fetching user: " . $e->getMessage() . "\n";
}

echo "\n--- TABLE CHECK ---\n";

// Check Sessions Table
try {
    $exists = Illuminate\Support\Facades\Schema::hasTable('sessions');
    echo "Sessions table exists: " . ($exists ? 'YES' : 'NO') . "\n";
} catch (\Exception $e) {
    echo "Error checking table: " . $e->getMessage() . "\n";
}

echo "--- DEBUG END ---\n";
