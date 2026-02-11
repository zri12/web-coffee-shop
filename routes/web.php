<?php

use Illuminate\Support\Facades\Route;
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
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{menu:slug}', [MenuController::class, 'show'])->name('menu.show');

// Cart & Checkout
Route::get('/cart', [OrderController::class, 'cart'])->name('cart');
Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
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
    Route::patch('/orders/{order}/status', [App\Http\Controllers\Cashier\CashierController::class, 'updateOrderStatus'])->name('orders.updateStatus');
    Route::get('/manual-order', [App\Http\Controllers\Cashier\CashierController::class, 'manualOrder'])->name('manual-order');
    Route::post('/manual-order', [App\Http\Controllers\Cashier\CashierController::class, 'storeManualOrder'])->name('manual-order.store');
    Route::get('/payments', [App\Http\Controllers\Cashier\CashierController::class, 'payments'])->name('payments');
    Route::post('/payments/{order}/process', [App\Http\Controllers\Cashier\CashierController::class, 'processPayment'])->name('payments.process');
    Route::get('/history', [App\Http\Controllers\Cashier\CashierController::class, 'history'])->name('history');
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
