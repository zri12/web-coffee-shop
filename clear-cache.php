<?php

/**
 * Cache clearing script for Vercel deployment
 * Run this after deployment to ensure fresh routes and config
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

echo "Clearing Laravel caches...\n\n";

// Clear route cache
echo "1. Clearing route cache...\n";
Illuminate\Support\Facades\Artisan::call('route:clear');
echo "   ✓ Route cache cleared\n\n";

// Clear config cache
echo "2. Clearing config cache...\n";
Illuminate\Support\Facades\Artisan::call('config:clear');
echo "   ✓ Config cache cleared\n\n";

// Clear view cache
echo "3. Clearing view cache...\n";
Illuminate\Support\Facades\Artisan::call('view:clear');
echo "   ✓ View cache cleared\n\n";

// Clear application cache
echo "4. Clearing application cache...\n";
Illuminate\Support\Facades\Artisan::call('cache:clear');
echo "   ✓ Application cache cleared\n\n";

echo "✅ All caches cleared successfully!\n";
echo "You can now test the application with fresh configuration.\n";
