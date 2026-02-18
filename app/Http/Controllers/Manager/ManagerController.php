<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
    public function index()
    {
        // Dates
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();

        // 1) Revenue (paid only, exclude cancelled)
        $todayRevenue = \App\Models\Order::whereDate('created_at', $today)
            ->where('payment_status', 'paid')
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $yesterdayRevenue = \App\Models\Order::whereDate('created_at', $yesterday)
            ->where('payment_status', 'paid')
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        // 2) Orders (all except cancelled)
        $todayOrders = \App\Models\Order::whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->count();

        $yesterdayOrders = \App\Models\Order::whereDate('created_at', $yesterday)
            ->where('status', '!=', 'cancelled')
            ->count();

        // 3) AOV
        $avgOrderValue = $todayOrders > 0 ? $todayRevenue / $todayOrders : 0;
        $yesterdayAvg = $yesterdayOrders > 0 ? $yesterdayRevenue / $yesterdayOrders : 0;

        // 4) Table occupancy (distinct tables with active orders)
        $totalTables = \App\Models\Table::count();
        $occupiedTables = \App\Models\Order::whereIn('status', ['pending', 'processing', 'waiting_payment', 'waiting_cashier_confirmation', 'paid', 'preparing'])
            ->whereNotNull('table_number')
            ->distinct('table_number')
            ->count('table_number');
        $tableOccupancy = $totalTables > 0 ? round(($occupiedTables / $totalTables) * 100) : 0;

        // Helper for percent change
        $percentChange = function($todayVal, $yesterdayVal) {
            if ($yesterdayVal == 0) {
                return $todayVal > 0 ? 100 : 0;
            }
            return round((($todayVal - $yesterdayVal) / $yesterdayVal) * 100, 1);
        };

        $stats = [
            'revenue' => $todayRevenue,
            'revenue_change' => $percentChange($todayRevenue, $yesterdayRevenue),
            'orders' => $todayOrders,
            'orders_change' => $percentChange($todayOrders, $yesterdayOrders),
            'avg_order_value' => $avgOrderValue,
            'aov_change' => $percentChange($avgOrderValue, $yesterdayAvg),
            'occupancy' => $tableOccupancy,
        ];

        // 5) Charts data - last 7 days
        $ordersPerDayLabels = [];
        $ordersPerDayCounts = [];
        $revenuePerDay = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $label = $date->format('d M');

            $count = \App\Models\Order::whereDate('created_at', $date)
                ->where('status', '!=', 'cancelled')
                ->count();

            $revenueDay = \App\Models\Order::whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount');

            $ordersPerDayLabels[] = $label;
            $ordersPerDayCounts[] = $count;
            $revenuePerDay[] = round($revenueDay, 2);
        }

        // Payment method distribution (today, exclude cancelled)
        $paymentSummaryRaw = \App\Models\Order::whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->selectRaw('payment_method, COUNT(*) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        $paymentSummary = [
            'cash' => $paymentSummaryRaw['cash'] ?? 0,
            'card' => $paymentSummaryRaw['card'] ?? 0,
            'qris' => $paymentSummaryRaw['qris'] ?? 0,
        ];

        // 6) Recent Orders
        $recentOrders = \App\Models\Order::with('items')
            ->latest()
            ->take(5)
            ->get();

        return view('manager.index', [
            'stats' => $stats,
            'ordersPerDayLabels' => $ordersPerDayLabels,
            'ordersPerDayCounts' => $ordersPerDayCounts,
            'revenuePerDay' => $revenuePerDay,
            'paymentSummary' => $paymentSummary,
            'recentOrders' => $recentOrders,
        ]);
    }

    public function menus(Request $request)
    {
        $query = \App\Models\Menu::with('category');

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category') && $request->category != 'all') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $menus = $query->paginate(10);
        $categories = \App\Models\Category::all();

        return view('manager.menus.index', compact('menus', 'categories'));
    }
    
    public function storeMenu(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'addons' => 'array',
            'addons.*.name' => 'nullable|string|max:120',
            'addons.*.price' => 'nullable|numeric|min:0',
        ];

        $request->validate($rules);
        
        $data = $request->only(['name', 'category_id', 'price', 'description']);
        
        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('menu-images', 'public');
        }

        $data['addons'] = \App\Models\Menu::normalizeAddons($request->input('addons', []));
        
        \App\Models\Menu::create($data);
        
        return redirect()->route('manager.menus')->with('success', 'Menu item created successfully');
    }
    
    public function editMenu($id)
    {
        $menu = \App\Models\Menu::findOrFail($id);
        $categories = \App\Models\Category::all();

        return view('manager.menus.edit', compact('menu', 'categories'));
    }
    
    public function updateMenu(Request $request, $id)
    {
        $menu = \App\Models\Menu::findOrFail($id);
        
        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'addons' => 'array',
            'addons.*.name' => 'nullable|string|max:120',
            'addons.*.price' => 'nullable|numeric|min:0',
        ];

        $request->validate($rules);
        
        $data = $request->only(['name', 'category_id', 'price', 'description']);
        
        if ($request->hasFile('image')) {
            if ($menu->image_url) {
                \Storage::disk('public')->delete($menu->image_url);
            }
            $data['image_url'] = $request->file('image')->store('menu-images', 'public');
        }

        $data['addons'] = \App\Models\Menu::normalizeAddons($request->input('addons', []));
        
        $menu->update($data);
        
        return redirect()->route('manager.menus')->with('success', 'Menu item updated successfully');
    }
    
    public function destroyMenu($id)
    {
        $menu = \App\Models\Menu::findOrFail($id);
        
        // Check if menu has any orders (not just active ones)
        $hasOrders = \App\Models\OrderItem::where('menu_id', $menu->id)->exists();
        
        if ($hasOrders) {
            // Instead of delete, just disable the menu
            $menu->update(['is_available' => false]);
            return response()->json([
                'success' => true, 
                'message' => 'Menu tidak dapat dihapus karena sudah pernah dipesan. Menu telah dinonaktifkan.',
                'action' => 'disabled'
            ]);
        }
        
        // If menu never been ordered, allow hard delete
        if ($menu->image_url) {
            \Storage::disk('public')->delete($menu->image_url);
        }
        
        $menu->delete();
        
        return response()->json([
            'success' => true, 
            'message' => 'Menu berhasil dihapus',
            'action' => 'deleted'
        ]);
    }
    
    public function toggleMenu($id)
    {
        $menu = \App\Models\Menu::findOrFail($id);
        $menu->update(['is_available' => !$menu->is_available]);
        
        $status = $menu->is_available ? 'diaktifkan' : 'dinonaktifkan';
        return response()->json([
            'success' => true,
            'message' => "Menu berhasil {$status}",
            'is_available' => $menu->is_available
        ]);
    }
    
    public function orders(Request $request)
    {
        // Build query with filters
        $query = \App\Models\Order::with(['items', 'payment']);

        // Apply status filter (supports new + legacy)
        if ($request->has('status') && $request->status !== 'all') {
            $statusMap = [
                'waiting_payment' => ['waiting_payment', 'waiting_cashier_confirmation', 'pending'],
                'paid' => ['paid'],
                'preparing' => ['preparing', 'processing'],
                'completed' => ['completed'],
                'cancelled' => ['cancelled'],
                'pending' => ['pending'],
                'processing' => ['processing'],
            ];

            $status = $request->status;
            if (isset($statusMap[$status])) {
                $query->whereIn('status', $statusMap[$status]);
            }
        }

        // Apply payment method filter
        if ($request->has('payment_method') && $request->payment_method !== 'all') {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('method', $request->payment_method);
            });
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('table_number', 'like', "%{$search}%");
            });
        }

        // Apply date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Handle export
        if ($request->has('export')) {
            $orders = $query->latest()->get();

            if ($request->export === 'pdf') {
                return view('manager.orders.pdf', compact('orders'));
            } elseif ($request->export === 'excel') {
                return $this->exportExcel($orders);
            }
        }

        // Get order statistics
        $totalOrders = \App\Models\Order::count();
        $pendingOrders = \App\Models\Order::whereIn('status', ['waiting_payment', 'waiting_cashier_confirmation', 'pending'])->count();
        $processingOrders = \App\Models\Order::whereIn('status', ['preparing', 'processing'])->count();
        $completedOrders = \App\Models\Order::where('status', 'completed')->count();

        // Get filtered orders
        $orders = $query->latest()->take(10)->get();

        $filters = [
            'status' => $request->status ?? 'all',
            'payment_method' => $request->payment_method ?? 'all',
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'search' => $request->search,
        ];

        return view('manager.orders.index', compact(
            'totalOrders',
            'pendingOrders', 
            'processingOrders',
            'completedOrders',
            'orders',
            'filters'
        ));
    }
    
    private function exportExcel($orders)
    {
        $filename = 'orders-' . date('Y-m-d') . '.xls';
        
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta name=ProgId content=Excel.Sheet></head>';
        echo '<body>';
        echo '<table border="1">';
        echo '<thead>';
        echo '<tr style="background-color: #C8A17D; color: white; font-weight: bold;">';
        echo '<th>Order ID</th>';
        echo '<th>Customer</th>';
        echo '<th>Table</th>';
        echo '<th>Total Amount</th>';
        echo '<th>Status</th>';
        echo '<th>Payment Method</th>';
        echo '<th>Payment Status</th>';
        echo '<th>Date</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        $totalAmount = 0;
        foreach ($orders as $order) {
            $table = 'Takeaway';
            if ($order->table_number && $order->order_type === 'dine_in') {
                $table = 'Table ' . $order->table_number;
            }
            
            echo '<tr>';
            echo '<td>' . $order->order_number . '</td>';
            echo '<td>' . $order->customer_name . '</td>';
            echo '<td>' . $table . '</td>';
            echo '<td style="text-align: right;">Rp ' . number_format($order->total_amount, 0, ',', '.') . '</td>';
            echo '<td>' . ucfirst($order->status) . '</td>';
            echo '<td>' . ($order->payment ? ucfirst($order->payment->method) : '-') . '</td>';
            echo '<td>' . ($order->payment ? ucfirst($order->payment->status) : '-') . '</td>';
            echo '<td>' . $order->created_at->format('Y-m-d H:i:s') . '</td>';
            echo '</tr>';
            
            $totalAmount += $order->total_amount;
        }
        
        echo '<tr style="background-color: #f0f0f0; font-weight: bold;">';
        echo '<td colspan="3">TOTAL</td>';
        echo '<td style="text-align: right;">Rp ' . number_format($totalAmount, 0, ',', '.') . '</td>';
        echo '<td colspan="4"></td>';
        echo '</tr>';
        
        echo '</tbody>';
        echo '</table>';
        echo '</body>';
        echo '</html>';
        exit;
    }
    
    private function exportCSV($orders)
    {
        $filename = 'orders-' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $handle = fopen('php://output', 'w');
        
        // Add UTF-8 BOM for Excel compatibility
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Add CSV headers
        fputcsv($handle, [
            'Order ID',
            'Customer',
            'Table',
            'Total Amount',
            'Status',
            'Payment Method',
            'Payment Status',
            'Date'
        ]);
        
        // Add data rows
        foreach ($orders as $order) {
            $table = 'Takeaway';
            if ($order->table_number && $order->order_type === 'dine_in') {
                $table = 'Table ' . $order->table_number;
            }
            
            fputcsv($handle, [
                $order->order_number,
                $order->customer_name,
                $table,
                number_format($order->total_amount, 0, '.', ''),
                ucfirst($order->status),
                $order->payment ? ucfirst($order->payment->method) : '-',
                $order->payment ? ucfirst($order->payment->status) : '-',
                $order->created_at->format('Y-m-d H:i:s'),
            ]);
        }
        
        fclose($handle);
        exit;
    }

    public function categories()
    {
        return view('manager.categories.index');
    }

    public function reports(Request $request)
    {
        $period = $request->get('period', 'daily');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $paymentMethod = $request->get('payment_method', 'all');
        $orderType = $request->get('order_type', 'all');

        $baseQuery = \App\Models\Order::query()->with(['items', 'payment']);

        if ($dateFrom) {
            $baseQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $baseQuery->whereDate('created_at', '<=', $dateTo);
        }
        if ($paymentMethod !== 'all') {
            $baseQuery->whereHas('payment', function ($q) use ($paymentMethod) {
                $q->where('method', $paymentMethod);
            });
        }
        if ($orderType !== 'all') {
            $baseQuery->where('order_type', $orderType);
        }

        // Revenue overview & trend
        $revenueSeries = [];
        $labels = [];
        $previousTotal = 0;
        $currentTotal = 0;

        if ($period === 'weekly') {
            for ($i = 7; $i >= 0; $i--) {
                $start = now()->subWeeks($i)->startOfWeek();
                $end = now()->subWeeks($i)->endOfWeek();
                $revenue = (clone $baseQuery)->whereBetween('created_at', [$start, $end])
                    ->where('status', '!=', 'cancelled')
                    ->where('payment_status', 'paid')
                    ->sum('total_amount');
                $labels[] = 'W' . $start->weekOfYear;
                $revenueSeries[] = $revenue;
                if ($i === 0) {
                    $currentTotal = $revenue;
                } elseif ($i === 1) {
                    $previousTotal = $revenue;
                }
            }
        } elseif ($period === 'monthly') {
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $revenue = (clone $baseQuery)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('status', '!=', 'cancelled')
                    ->where('payment_status', 'paid')
                    ->sum('total_amount');
                $labels[] = $date->format('M');
                $revenueSeries[] = $revenue;
                if ($i === 0) {
                    $currentTotal = $revenue;
                } elseif ($i === 1) {
                    $previousTotal = $revenue;
                }
            }
        } else {
            // daily (last 7 days)
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->startOfDay();
                $revenue = (clone $baseQuery)
                    ->whereDate('created_at', $date)
                    ->where('status', '!=', 'cancelled')
                    ->where('payment_status', 'paid')
                    ->sum('total_amount');
                $labels[] = $date->format('D');
                $revenueSeries[] = $revenue;
                if ($i === 0) {
                    $currentTotal = $revenue;
                } elseif ($i === 1) {
                    $previousTotal = $revenue;
                }
            }
        }

        $revenueChange = $previousTotal > 0 ? round((($currentTotal - $previousTotal) / $previousTotal) * 100, 1) : ($currentTotal > 0 ? 100 : 0);

        // Order & transaction insights
        $totalOrders = (clone $baseQuery)->count();
        $pendingOrders = (clone $baseQuery)->where('status', 'pending')->count();
        $processingOrders = (clone $baseQuery)->where('status', 'processing')->count();
        $completedOrders = (clone $baseQuery)->where('status', 'completed')->count();
        $cancelledOrders = (clone $baseQuery)->where('status', 'cancelled')->count();

        // Payment analytics
        $paymentMethodTotals = (clone $baseQuery)
            ->where('payment_status', 'paid')
            ->selectRaw('payment_method, SUM(total_amount) as amount, COUNT(*) as total')
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        $paymentChart = [
            'labels' => ['Cash', 'Card', 'QRIS'],
            'data' => [
                (float) ($paymentMethodTotals['cash']->amount ?? 0),
                (float) ($paymentMethodTotals['card']->amount ?? 0),
                (float) ($paymentMethodTotals['qris']->amount ?? 0),
            ],
        ];

        // Best selling items (top 5 by quantity)
        $bestItems = \App\Models\OrderItem::query()
            ->selectRaw('menu_name as name, SUM(quantity) as qty, SUM(subtotal) as revenue')
            ->whereHas('order', function ($q) use ($dateFrom, $dateTo, $paymentMethod, $orderType) {
                $q->where('status', 'completed');
                if ($dateFrom) $q->whereDate('created_at', '>=', $dateFrom);
                if ($dateTo) $q->whereDate('created_at', '<=', $dateTo);
                if ($paymentMethod !== 'all') $q->where('payment_method', $paymentMethod);
                if ($orderType !== 'all') $q->where('order_type', $orderType);
            })
            ->groupBy('menu_name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        // Category contribution
        $categoryStats = \App\Models\Category::select('categories.name')
            ->selectRaw('SUM(order_items.quantity) as qty')
            ->leftJoin('menus', 'menus.category_id', '=', 'categories.id')
            ->leftJoin('order_items', 'order_items.menu_id', '=', 'menus.id')
            ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
            ->when($dateFrom, fn($q) => $q->whereDate('orders.created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('orders.created_at', '<=', $dateTo))
            ->when($paymentMethod !== 'all', fn($q) => $q->where('orders.payment_method', $paymentMethod))
            ->when($orderType !== 'all', fn($q) => $q->where('orders.order_type', $orderType))
            ->where('orders.status', 'completed')
            ->groupBy('categories.name')
            ->get();

        $totalCategoryQty = max(1, $categoryStats->sum('qty'));
        $categoryPerformance = $categoryStats->map(function ($row) use ($totalCategoryQty) {
            $percent = $totalCategoryQty > 0 ? round(($row->qty / $totalCategoryQty) * 100, 1) : 0;
            return [
                'name' => $row->name ?? 'Uncategorized',
                'percentage' => $percent,
                'qty' => (int) $row->qty,
            ];
        })->sortByDesc('percentage')->values();

        // Peak hour (07-22)
        $peakHours = [];
        for ($h = 7; $h <= 22; $h++) {
            $count = (clone $baseQuery)
                ->whereRaw('HOUR(created_at) = ?', [$h])
                ->count();
            $peakHours[] = ['label' => sprintf('%02d:00', $h), 'value' => $count];
        }

        // Dine-in vs takeaway and occupancy
        $dineIn = (clone $baseQuery)->where('order_type', 'dine_in')->count();
        $takeAway = (clone $baseQuery)->where('order_type', 'takeaway')->count();
        $totalTables = \App\Models\Table::count();
        $occupiedTables = \App\Models\Order::whereIn('status', ['pending', 'processing'])
            ->whereNotNull('table_number')
            ->distinct('table_number')
            ->count('table_number');
        $occupancyRate = $totalTables > 0 ? round(($occupiedTables / $totalTables) * 100) : 0;

        // Export minimal CSV for filtered orders
        if ($request->get('export') === 'csv') {
            $orders = (clone $baseQuery)->latest()->get();
            return $this->exportCSV($orders);
        }

        $filters = [
            'period' => $period,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'payment_method' => $paymentMethod,
            'order_type' => $orderType,
        ];

        return view('manager.reports.index', [
            'period' => $period,
            'revenueLabels' => $labels,
            'revenueSeries' => $revenueSeries,
            'revenueChange' => $revenueChange,
            'currentRevenue' => array_sum($revenueSeries),
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'processingOrders' => $processingOrders,
            'completedOrders' => $completedOrders,
            'cancelledOrders' => $cancelledOrders,
            'paymentChart' => $paymentChart,
            'bestItems' => $bestItems,
            'categoryPerformance' => $categoryPerformance,
            'peakHours' => $peakHours,
            'dineIn' => $dineIn,
            'takeAway' => $takeAway,
            'occupancyRate' => $occupancyRate,
            'filters' => $filters,
        ]);
    }

    public function tables(Request $request)
    {
        // Get all tables with real-time status updates
        $tables = \App\Models\Table::orderBy('table_number')->get();
        
        // Update table status based on active orders
        foreach ($tables as $table) {
            $activeOrders = \App\Models\Order::where('table_number', $table->table_number)
                ->whereIn('status', ['pending', 'processing'])
                ->count();
            
            // Auto-update status if not manually reserved
            if ($activeOrders > 0 && $table->status !== 'reserved') {
                $table->status = 'occupied';
                $table->save();
            } elseif ($activeOrders === 0 && $table->status === 'occupied') {
                $table->status = 'available';
                $table->save();
            }
            
            // Add active orders count to table object
            $table->active_orders_count = $activeOrders;
        }
        
        // Apply filters if requested
        if ($request->has('status') && $request->status !== 'all') {
            $tables = $tables->where('status', $request->status);
        }
        
        if ($request->has('search') && $request->search) {
            $tables = $tables->filter(function($table) use ($request) {
                return stripos($table->table_number, $request->search) !== false;
            });
        }
        
        return view('manager.tables.index', compact('tables'));
    }

    public function storeTable(Request $request)
    {
        $validated = $request->validate([
            'table_number' => 'required|unique:tables,table_number|max:20',
            'capacity' => 'required|integer|min:1|max:20',
            'status' => 'nullable|in:available,occupied,reserved'
        ]);

        $validated['status'] = $validated['status'] ?? 'available';
        
        $table = \App\Models\Table::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Table created successfully',
            'table' => $table
        ]);
    }

    public function updateTable(Request $request, $id)
    {
        $table = \App\Models\Table::findOrFail($id);
        
        $validated = $request->validate([
            'table_number' => 'required|max:20|unique:tables,table_number,' . $id,
            'capacity' => 'required|integer|min:1|max:20',
        ]);
        
        $table->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Table updated successfully',
            'table' => $table
        ]);
    }

    public function destroyTable($id)
    {
        $table = \App\Models\Table::findOrFail($id);
        
        // Check for active orders
        $activeOrders = \App\Models\Order::where('table_number', $table->table_number)
            ->whereIn('status', ['pending', 'processing'])
            ->count();
        
        if ($activeOrders > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete table with active orders'
            ], 422);
        }
        
        $table->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Table deleted successfully'
        ]);
    }

    public function updateTableStatus(Request $request, $id)
    {
        $table = \App\Models\Table::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,reserved'
        ]);
        
        $table->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Table status updated successfully',
            'table' => $table
        ]);
    }

    public function staff()
    {
        $staff = \App\Models\User::whereIn('role', ['cashier', 'manager'])->get();
        return view('manager.staff.index', compact('staff'));
    }

    public function deleteStaff($id)
    {
        try {
            $staff = \App\Models\User::findOrFail($id);
            
            // Prevent deleting the current user
            if ($staff->id === auth()->id()) {
                return response()->json(['message' => 'Cannot delete your own account'], 403);
            }
            
            $staff->delete();
            return response()->json(['message' => 'Staff member deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting staff member'], 500);
        }
    }

    public function updateStaff($id, Request $request)
    {
        try {
            $staff = \App\Models\User::findOrFail($id);
            
            // Validate input
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'role' => 'required|in:manager,cashier',
                'password' => 'nullable|string|min:6'
            ]);
            
            // Update basic fields
            $staff->name = $validated['name'];
            $staff->email = $validated['email'];
            $staff->role = $validated['role'];
            
            // Update password if provided
            if (!empty($validated['password'])) {
                $staff->password = bcrypt($validated['password']);
            }
            
            $staff->save();
            
            return response()->json(['message' => 'Staff member updated successfully'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating staff member'], 500);
        }
    }

    public function profile()
    {
        return view('manager.profile.index');
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = auth()->user();
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
            ]);

            $user->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $user = auth()->user();
            
            $validated = $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ]);

            // Verify current password
            if (!\Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }

            // Update password
            $user->update([
                'password' => \Hash::make($validated['new_password'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update password: ' . $e->getMessage()
            ], 500);
        }
    }

}
