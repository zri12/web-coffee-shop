<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use App\Services\SystemSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(private readonly SystemSettingsService $settingsService)
    {
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->isCashier()) {
            return redirect()->route('cashier.incoming-orders');
        }

        if ($user->isManager()) {
            return redirect()->route('manager.dashboard');
        }
        
        // Stats
        $stats = [
            'total_orders_today' => Order::whereDate('created_at', today())->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'preparing_orders' => Order::where('status', 'processing')->count(), // 'processing' maps to 'Preparing'
            'completed_orders_today' => Order::whereDate('created_at', today())->where('status', 'completed')->count(),
            'total_revenue_today' => Order::whereDate('created_at', today())
                ->where('status', 'completed')
                ->sum('total_amount'),
            'total_menus' => Menu::count(),
        ];
        
        // Recent orders
        $recentOrders = Order::with('items')
            ->latest()
            ->limit(10)
            ->get();
        
        // Today's orders by status
        $ordersByStatus = Order::whereDate('created_at', today())
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        // Best seller (all time or today? Reference implies general best seller. Let's do all time for now or today's if preferred. Reference says "Most Popular". Let's do general)
        $bestSeller = DB::table('order_items')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->select('menus.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('menus.id', 'menus.name')
            ->orderByDesc('total_sold')
            ->first();

        return view('dashboard.index', compact('stats', 'recentOrders', 'ordersByStatus', 'user', 'bestSeller'));
    }

    public function reports()
    {
        // Sales by date (last 7 days)
        $salesByDate = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Top selling menus
        $topMenus = DB::table('order_items')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->join('categories', 'menus.category_id', '=', 'categories.id')
            ->select('menus.name', 'menus.image', 'categories.name as category_name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('menus.id', 'menus.name', 'menus.image', 'categories.id', 'categories.name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();
        
        // Orders by type
        $ordersByType = Order::select('order_type', DB::raw('count(*) as count'))
            ->groupBy('order_type')
            ->pluck('count', 'order_type')
            ->toArray();
        
        return view('dashboard.reports', compact('salesByDate', 'topMenus', 'ordersByType'));
    }
    public function security()
    {
        return view('dashboard.security');
    }

    public function settings()
    {
        return view('dashboard.settings');
    }

    // Menu Management
    public function menus(Request $request)
    {
        $query = Menu::with('category');

        // Search by name
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by category
        if ($request->has('category') && $request->category != 'all') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $menus = $query->latest()->paginate(10)->appends($request->query());
        $categories = Category::where('is_active', true)->get();
        
        return view('admin.menus', compact('menus', 'categories'));
    }

    public function storeMenu(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menus', 'public');
        }

        Menu::create($validated);

        return redirect()->back()->with('success', 'Menu berhasil ditambahkan');
    }

    public function editMenu(Menu $menu)
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.menus-edit', compact('menu', 'categories'));
    }

    public function updateMenu(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menus', 'public');
        }

        $menu->update($validated);

        return redirect()->back()->with('success', 'Menu berhasil diupdate');
    }

    public function destroyMenu(Menu $menu)
    {
        $menu->delete();
        return redirect()->back()->with('success', 'Menu berhasil dihapus');
    }

    // Category Management
    public function categories()
    {
        $categories = Category::withCount('menus')->orderBy('sort_order')->get();
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Category::create($validated);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $category->update($validated);

        return redirect()->back()->with('success', 'Kategori berhasil diupdate');
    }

    public function destroyCategory(Category $category)
    {
        $category->delete();
        return redirect()->back()->with('success', 'Kategori berhasil dihapus');
    }

    // Orders Management
    public function orders(Request $request)
    {
        $query = Order::with(['items.menu', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.orders', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        $order->load(['items.menu', 'user']);
        return view('admin.order-detail', compact('order'));
    }

    // User Management
    public function users()
    {
        $users = User::latest()->get();
        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,manager,cashier',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        User::create($validated);

        return redirect()->back()->with('success', 'User berhasil ditambahkan');
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,manager,cashier',
            'password' => 'nullable|min:8',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'User berhasil diupdate');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus');
    }

    // Payment Settings
    public function paymentSettings()
    {
        // Get current Midtrans settings from env or database
        $midtransMerchantId = env('MIDTRANS_MERCHANT_ID', '');
        $midtransServerKey = env('MIDTRANS_SERVER_KEY', '');
        $midtransClientKey = env('MIDTRANS_CLIENT_KEY', '');
        $midtransProduction = env('MIDTRANS_IS_PRODUCTION', false);

        return view('admin.payment-settings', compact(
            'midtransMerchantId',
            'midtransServerKey',
            'midtransClientKey',
            'midtransProduction'
        ));
    }

    public function updatePaymentSettings(Request $request)
    {
        $validated = $request->validate([
            'midtrans_merchant_id' => 'nullable|string',
            'midtrans_server_key' => 'nullable|string',
            'midtrans_client_key' => 'nullable|string',
            'midtrans_production' => 'nullable|boolean',
        ]);

        // Store in .env file
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        // Helper function to update env value
        $updateEnvValue = function(&$content, $key, $value) {
            $pattern = '/^' . preg_quote($key) . '=.*/m';
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $key . '=' . $value, $content);
            } else {
                $content .= "\n" . $key . '=' . $value;
            }
        };

        // Update all Midtrans settings
        $updateEnvValue($envContent, 'MIDTRANS_MERCHANT_ID', $request->midtrans_merchant_id ?? '');
        $updateEnvValue($envContent, 'MIDTRANS_SERVER_KEY', $request->midtrans_server_key ?? '');
        $updateEnvValue($envContent, 'MIDTRANS_CLIENT_KEY', $request->midtrans_client_key ?? '');
        $updateEnvValue($envContent, 'MIDTRANS_IS_PRODUCTION', $request->has('midtrans_production') ? 'true' : 'false');

        // Write to .env file
        if (file_put_contents($envPath, $envContent) === false) {
            return redirect()->back()->with('error', 'Gagal menyimpan file .env. Pastikan file memiliki permission untuk ditulis.');
        }

        // Clear config cache
        \Artisan::call('config:cache');

        return redirect()->back()->with('success', 'Pengaturan pembayaran Midtrans berhasil disimpan!');
    }

    // System Settings
    public function systemSettings()
    {
        $settings = $this->settingsService->all();

        return view('admin.system-settings', [
            'settings' => $settings,
        ]);
    }

    public function updateSystemSettings(Request $request)
    {
        $validated = $request->validate([
            'cafe_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i',
            'closed_days' => 'nullable|array',
            'instagram' => 'nullable|string',
            'facebook' => 'nullable|string',
            'whatsapp' => 'nullable|string',
        ]);

        $payload = [
            'cafe_name' => $validated['cafe_name'],
            'address' => $validated['address'] ?? '',
            'phone' => $validated['phone'] ?? '',
            'opening_time' => $validated['opening_time'],
            'closing_time' => $validated['closing_time'],
            'closed_days' => $validated['closed_days'] ?? [],
            'instagram' => $validated['instagram'] ?? '',
            'facebook' => $validated['facebook'] ?? '',
            'whatsapp' => $validated['whatsapp'] ?? '',
        ];

        if ($request->hasFile('logo')) {
            $payload['logo_path'] = $this->settingsService->storeLogo($request->file('logo'));
        }

        try {
            $this->settingsService->update($payload);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Pengaturan sistem berhasil disimpan dan disinkronkan!');
    }

    // Logs & Audit
    public function logs(Request $request)
    {
        $tab = $request->get('tab', 'transactions');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $transactionLogs = [];
        $loginLogs = [];
        $changeLogs = [];

        // Transaction Logs
        if ($tab === 'transactions' || $request->ajax()) {
            $query = Order::with('user', 'items')
                ->latest();

            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            $transactionLogs = $query->limit(100)->get();
        }

        // Login Logs
        if ($tab === 'login') {
            // TODO: Implement login logs tracking in auth controller
            $loginLogs = [];
        }

        // Change Logs
        if ($tab === 'changes') {
            // TODO: Implement audit logging for data changes
            $changeLogs = [];
        }

        return view('admin.logs', compact(
            'transactionLogs',
            'loginLogs',
            'changeLogs',
            'tab',
            'startDate',
            'endDate'
        ));
    }

    public function loginLogs()
    {
        // Fetch login logs
        return response()->json([]);
    }

    public function transactionLogs()
    {
        $logs = Order::with('user')->latest()->limit(100)->get();
        return response()->json($logs);
    }

    public function changeLogs()
    {
        // Fetch change logs
        return response()->json([]);
    }

    // Profile
    public function profile()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', 'Profil berhasil diupdate');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!password_verify($validated['current_password'], auth()->user()->password)) {
            return redirect()->back()->with('error', 'Password lama tidak sesuai');
        }

        auth()->user()->update([
            'password' => bcrypt($validated['password'])
        ]);

        return redirect()->back()->with('success', 'Password berhasil diubah');
    }
}
