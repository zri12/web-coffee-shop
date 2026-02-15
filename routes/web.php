<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\MenuController as DashboardMenuController;
use App\Http\Controllers\Dashboard\OrderController as DashboardOrderController;
use App\Http\Controllers\Dashboard\CategoryController as DashboardCategoryController;
use App\Http\Controllers\Dashboard\UserController as DashboardUserController;

/*
|--------------------------------------------------------------------------
| Web Routes - Cafe Web Ordering System  
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/ping', fn () => 'Laravel OK');
Route::get('/menu-ai-image/{menu}', function ($menu, Request $request) {
    $name = trim((string) $request->query('name', 'Cafe Menu'));
    $hint = trim((string) $request->query('hint', 'food photography'));
    $safeName = htmlspecialchars(Str::limit($name, 28), ENT_QUOTES, 'UTF-8');

    // Real AI image endpoint for menu cards.
    if (!str_starts_with((string) $menu, 'placeholder-')) {
        $prompt = rawurlencode("{$hint}, {$name}, realistic, studio lighting, no text, high detail");
        $seed = abs(crc32((string) $menu));
        $url = "https://image.pollinations.ai/prompt/{$prompt}?width=1024&height=1024&seed={$seed}&model=flux";

        return redirect()->away($url);
    }

    $hash = abs(crc32((string) $menu));
    $palettes = [
        ['#4A2E1F', '#C98A4A', '#F8F2E8'],
        ['#2F3E46', '#84A98C', '#F4F1DE'],
        ['#3D2C2E', '#B56B45', '#F5E6CC'],
        ['#1F2A44', '#CFA75E', '#EFE6D5'],
    ];
    $palette = $palettes[$hash % count($palettes)];

    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1024" height="1024" viewBox="0 0 1024 1024">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="{$palette[0]}"/>
      <stop offset="100%" stop-color="{$palette[1]}"/>
    </linearGradient>
    <radialGradient id="shine" cx="30%" cy="20%" r="60%">
      <stop offset="0%" stop-color="rgba(255,255,255,0.35)"/>
      <stop offset="100%" stop-color="rgba(255,255,255,0)"/>
    </radialGradient>
  </defs>
  <rect width="1024" height="1024" fill="url(#bg)"/>
  <rect width="1024" height="1024" fill="url(#shine)"/>
  <circle cx="220" cy="210" r="140" fill="rgba(255,255,255,0.13)"/>
  <circle cx="840" cy="820" r="190" fill="rgba(255,255,255,0.10)"/>
  <rect x="232" y="278" width="560" height="430" rx="48" fill="rgba(255,255,255,0.14)" stroke="rgba(255,255,255,0.3)" stroke-width="6"/>
  <text x="512" y="520" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="96" font-weight="700" fill="{$palette[2]}">{$safeName}</text>
  <text x="512" y="600" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="36" fill="rgba(255,255,255,0.9)">Bean &amp; Brew</text>
</svg>
SVG;

    return response($svg, 200)
        ->header('Content-Type', 'image/svg+xml')
        ->header('Cache-Control', 'public, max-age=31536000, immutable');
})->name('menu.ai-image');

Route::get('/db-check', function () {
    try {
        $dbName = DB::selectOne('select database() as db')->db ?? null;

        $tables = [
            'categories' => Schema::hasTable('categories'),
            'menus' => Schema::hasTable('menus'),
            'users' => Schema::hasTable('users'),
        ];

        $counts = [
            'categories' => $tables['categories'] ? DB::table('categories')->count() : null,
            'menus' => $tables['menus'] ? DB::table('menus')->count() : null,
            'users' => $tables['users'] ? DB::table('users')->count() : null,
        ];

        return response()->json([
            'ok' => true,
            'db_database_env' => env('DB_DATABASE'),
            'connected_database' => $dbName,
            'tables' => $tables,
            'counts' => $counts,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'ok' => false,
            'db_database_env' => env('DB_DATABASE'),
            'error' => $e->getMessage(),
        ], 500);
    }
});
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{slug}', [MenuController::class, 'show'])->name('menu.show');

// Cart & Checkout
Route::get('/cart', [OrderController::class, 'cart'])->name('cart');
Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::get('/order/{orderNumber}/waiting', [OrderController::class, 'waiting'])->name('order.waiting');
Route::get('/order/{orderNumber}/success', [OrderController::class, 'success'])->name('order.success');

// Payment Processing
Route::prefix('payment')->name('payment.')->group(function () {
    Route::post('/{order}/process', [App\Http\Controllers\PaymentController::class, 'processPayment'])->name('process');
    Route::get('/success', [App\Http\Controllers\PaymentController::class, 'handleSuccess'])->name('success');
    Route::get('/error', [App\Http\Controllers\PaymentController::class, 'handleError'])->name('error');
    Route::get('/pending', [App\Http\Controllers\PaymentController::class, 'handlePending'])->name('pending');
    Route::get('/{order}/status', [App\Http\Controllers\PaymentController::class, 'getStatus'])->name('status');
});

// Midtrans Webhook
Route::post('/midtrans/webhook', [App\Http\Controllers\PaymentController::class, 'webhook'])->name('midtrans.webhook');

// Order Tracking
Route::get('/track', [OrderController::class, 'track'])->name('track');
Route::post('/track', [OrderController::class, 'trackOrder'])->name('track.search');

// Order Status API (for polling)
Route::get('/api/order/{orderNumber}/status', [OrderController::class, 'getOrderStatus'])->name('order.status');

// QR Entry / Customer Routes
Route::prefix('order')->name('customer.')->group(function () {
    Route::get('/{tableNumber}', [App\Http\Controllers\CustomerController::class, 'index'])->name('index');
    Route::get('/{tableNumber}/menu', [App\Http\Controllers\CustomerController::class, 'menu'])->name('menu');
    Route::post('/{tableNumber}/cart', [App\Http\Controllers\CustomerController::class, 'addToCart'])->name('cart.add');
    Route::get('/{tableNumber}/cart', [App\Http\Controllers\CustomerController::class, 'getCart'])->name('cart.get');
    Route::delete('/{tableNumber}/cart/item', [App\Http\Controllers\CustomerController::class, 'removeFromCart'])->name('cart.remove');
    Route::patch('/{tableNumber}/cart/item', [App\Http\Controllers\CustomerController::class, 'updateCartItem'])->name('cart.update');
});

// Legacy QR redirect for backward compatibility
Route::get('/table/{number}', function ($number) {
    return redirect()->route('customer.index', ['tableNumber' => $number]);
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'webLogin']);
});

Route::post('/logout', [AuthController::class, 'webLogout'])->name('logout')->middleware('auth');

// Admin Routes (Protected)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Menu Management
    Route::get('/menus', [DashboardController::class, 'menus'])->name('menus');
    Route::get('/menus/{menu}/edit', [DashboardController::class, 'editMenu'])->name('menus.edit');
    Route::post('/menus', [DashboardController::class, 'storeMenu'])->name('menus.store');
    Route::put('/menus/{menu}', [DashboardController::class, 'updateMenu'])->name('menus.update');
    Route::delete('/menus/{menu}', [DashboardController::class, 'destroyMenu'])->name('menus.destroy');
    
    // Category Management
    Route::get('/categories', [DashboardController::class, 'categories'])->name('categories');
    Route::post('/categories', [DashboardController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [DashboardController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [DashboardController::class, 'destroyCategory'])->name('categories.destroy');
    
    // Orders
    Route::get('/orders', [DashboardController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [DashboardController::class, 'orderDetail'])->name('orders.detail');
    
    // Table Management
    Route::get('/tables', [App\Http\Controllers\Manager\ManagerController::class, 'tables'])->name('tables');
    Route::post('/tables', [App\Http\Controllers\Manager\ManagerController::class, 'storeTable'])->name('tables.store');
    Route::put('/tables/{table}', [App\Http\Controllers\Manager\ManagerController::class, 'updateTable'])->name('tables.update');
    Route::delete('/tables/{table}', [App\Http\Controllers\Manager\ManagerController::class, 'destroyTable'])->name('tables.destroy');
    Route::patch('/tables/{table}/status', [App\Http\Controllers\Manager\ManagerController::class, 'updateTableStatus'])->name('tables.status');
    
    // User Management
    Route::get('/users', [DashboardController::class, 'users'])->name('users');
    Route::post('/users', [DashboardController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [DashboardController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [DashboardController::class, 'destroyUser'])->name('users.destroy');
    
    // Payment Settings
    Route::get('/payment-settings', [DashboardController::class, 'paymentSettings'])->name('payment-settings');
    Route::post('/payment-settings', [DashboardController::class, 'updatePaymentSettings'])->name('payment-settings.update');
    
    // System Settings
    Route::get('/system-settings', [DashboardController::class, 'systemSettings'])->name('system-settings');
    Route::post('/system-settings', [DashboardController::class, 'updateSystemSettings'])->name('system-settings.update');
    
    // Logs & Audit
    Route::get('/logs', [DashboardController::class, 'logs'])->name('logs');
    Route::get('/logs/login', [DashboardController::class, 'loginLogs'])->name('logs.login');
    Route::get('/logs/transactions', [DashboardController::class, 'transactionLogs'])->name('logs.transactions');
    Route::get('/logs/changes', [DashboardController::class, 'changeLogs'])->name('logs.changes');
    
    
    // Inventory Management (Admin + Manager)
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/ingredients', [App\Http\Controllers\Admin\IngredientController::class, 'index'])->name('ingredients.index');
        Route::post('/ingredients', [App\Http\Controllers\Admin\IngredientController::class, 'store'])->name('ingredients.store');
        Route::put('/ingredients/{ingredient}', [App\Http\Controllers\Admin\IngredientController::class, 'update'])->name('ingredients.update');
        Route::delete('/ingredients/{ingredient}', [App\Http\Controllers\Admin\IngredientController::class, 'destroy'])->name('ingredients.destroy');
        Route::get('/ingredients/{ingredient}/history', [App\Http\Controllers\Admin\IngredientController::class, 'history'])->name('ingredients.history');
        Route::post('/ingredients/{ingredient}/restock', [App\Http\Controllers\Admin\IngredientController::class, 'restock'])->name('ingredients.restock');
        
        // Recipes
        Route::post('/recipes', [App\Http\Controllers\Admin\RecipeController::class, 'store'])->name('recipes.store');
        Route::put('/recipes/{recipe}', [App\Http\Controllers\Admin\RecipeController::class, 'update'])->name('recipes.update');
        Route::delete('/recipes/{recipe}', [App\Http\Controllers\Admin\RecipeController::class, 'destroy'])->name('recipes.destroy');
        Route::get('/api/ingredients', [App\Http\Controllers\Admin\RecipeController::class, 'getIngredients'])->name('api.ingredients');
        
        // Analytics
        Route::get('/analytics/inventory', [App\Http\Controllers\Admin\InventoryAnalyticsController::class, 'index'])->name('analytics.inventory');
    });
    
    // Profile
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [DashboardController::class, 'updatePassword'])->name('profile.password');
});

// Legacy Dashboard Routes (keep for compatibility) - Outside admin group
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('');
    Route::get('/menus', [DashboardController::class, 'menus'])->name('menus');
    Route::get('/orders', [DashboardOrderController::class, 'index'])->name('orders');
    Route::get('/orders/create', [DashboardOrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [DashboardOrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [DashboardOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [DashboardOrderController::class, 'updateStatus'])->name('orders.status');
    Route::get('/categories', [DashboardController::class, 'categories'])->name('categories');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
    Route::get('/security', [DashboardController::class, 'security'])->name('.security');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('.settings');
});


// Cashier Routes (Protected)
Route::middleware(['auth', 'role:cashier'])->prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/', [App\Http\Controllers\Cashier\CashierController::class, 'dashboard'])->name('dashboard');
    Route::get('/incoming-orders', [App\Http\Controllers\Cashier\CashierController::class, 'incomingOrders'])->name('incoming-orders');
    Route::get('/orders', [App\Http\Controllers\Cashier\CashierController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [App\Http\Controllers\Cashier\CashierController::class, 'showOrder'])->name('orders.show');
    Route::get('/orders/{order}/print-kitchen', [App\Http\Controllers\Cashier\CashierController::class, 'printKitchen'])->name('orders.print-kitchen');
    Route::get('/orders/{order}/print-bill', [App\Http\Controllers\Cashier\CashierController::class, 'printBill'])->name('orders.print-bill');
    Route::patch('/orders/{order}/status', [App\Http\Controllers\Cashier\CashierController::class, 'updateOrderStatus'])->name('orders.updateStatus');
    Route::post('/orders/{order}/confirm-payment', [App\Http\Controllers\Cashier\CashierController::class, 'confirmPayment'])->name('orders.confirmPayment');
    Route::get('/manual-order', [App\Http\Controllers\Cashier\CashierController::class, 'manualOrder'])->name('manual-order');
    Route::post('/manual-order', [App\Http\Controllers\Cashier\CashierController::class, 'storeManualOrder'])->name('manual-order.store');
    Route::get('/payments', [App\Http\Controllers\Cashier\CashierController::class, 'payments'])->name('payments');
    Route::post('/payments/{order}/process', [App\Http\Controllers\Cashier\CashierController::class, 'processPayment'])->name('payments.process');
    Route::get('/history', [App\Http\Controllers\Cashier\CashierController::class, 'history'])->name('history');
    Route::get('/history/{order}/details', [App\Http\Controllers\Cashier\CashierController::class, 'getOrderDetails'])->name('history.details');
    Route::get('/profile', [App\Http\Controllers\Cashier\CashierController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [App\Http\Controllers\Cashier\CashierController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [App\Http\Controllers\Cashier\CashierController::class, 'updatePassword'])->name('profile.password');
});

// Manager Routes (Protected) - Admin can also access
Route::middleware(['auth', 'role:admin,manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/', [App\Http\Controllers\Manager\ManagerController::class, 'index'])->name('dashboard');
    Route::get('/menus', [App\Http\Controllers\Manager\ManagerController::class, 'menus'])->name('menus');
    Route::post('/menus', [App\Http\Controllers\Manager\ManagerController::class, 'storeMenu'])->name('menus.store');
    Route::get('/menus/{menu}/edit', [App\Http\Controllers\Manager\ManagerController::class, 'editMenu'])->name('menus.edit');
    Route::put('/menus/{menu}', [App\Http\Controllers\Manager\ManagerController::class, 'updateMenu'])->name('menus.update');
    Route::delete('/menus/{menu}', [App\Http\Controllers\Manager\ManagerController::class, 'destroyMenu'])->name('menus.destroy');
    Route::patch('/menus/{menu}/toggle', [App\Http\Controllers\Manager\ManagerController::class, 'toggleMenu'])->name('menus.toggle');
    Route::get('/orders', [App\Http\Controllers\Manager\ManagerController::class, 'orders'])->name('orders');
    Route::get('/reports', [App\Http\Controllers\Manager\ManagerController::class, 'reports'])->name('reports');
    Route::get('/tables', [App\Http\Controllers\Manager\ManagerController::class, 'tables'])->name('tables');
    Route::post('/tables', [App\Http\Controllers\Manager\ManagerController::class, 'storeTable'])->name('tables.store');
    Route::put('/tables/{table}', [App\Http\Controllers\Manager\ManagerController::class, 'updateTable'])->name('tables.update');
    Route::delete('/tables/{table}', [App\Http\Controllers\Manager\ManagerController::class, 'destroyTable'])->name('tables.destroy');
    Route::patch('/tables/{table}/status', [App\Http\Controllers\Manager\ManagerController::class, 'updateTableStatus'])->name('tables.status');
    Route::get('/staff', [App\Http\Controllers\Manager\ManagerController::class, 'staff'])->name('staff');
    Route::put('/staff/{id}', [App\Http\Controllers\Manager\ManagerController::class, 'updateStaff'])->name('staff.update');
    Route::delete('/staff/{id}', [App\Http\Controllers\Manager\ManagerController::class, 'deleteStaff'])->name('staff.delete');
    Route::get('/profile', [App\Http\Controllers\Manager\ManagerController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [App\Http\Controllers\Manager\ManagerController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [App\Http\Controllers\Manager\ManagerController::class, 'updatePassword'])->name('profile.password');
});
