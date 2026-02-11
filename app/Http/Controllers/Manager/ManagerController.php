<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function index()
    {
        // 1. Stats Cards - Today's Data
        $todayRevenue = \App\Models\Order::whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('total_amount');
        
        $todayOrders = \App\Models\Order::whereDate('created_at', today())->count();
        
        $avgOrderValue = $todayOrders > 0 ? $todayRevenue / $todayOrders : 0;
        
        // Real Table Occupancy based on occupied tables
        $totalTables = \App\Models\Table::count();
        $occupiedTables = \App\Models\Table::where('status', 'occupied')->count();
        $tableOccupancy = $totalTables > 0 ? round(($occupiedTables / $totalTables) * 100) : 0;

        $stats = [
            'revenue' => $todayRevenue,
            'orders' => $todayOrders,
            'avg_order_value' => $avgOrderValue,
            'occupancy' => $tableOccupancy
        ];

        // 2. Daily Order Data for Chart (Last 7 days)
        $dailyOrders = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $orderCount = \App\Models\Order::whereDate('created_at', $date->toDateString())->count();
            
            $dailyOrders[] = [
                'date' => $date->format('M d'),
                'day' => $date->format('D'),
                'count' => $orderCount
            ];
        }

        // 3. Recent Orders
        $recentOrders = \App\Models\Order::with('items') 
            ->latest()
            ->take(5)
            ->get();

        return view('manager.index', compact('stats', 'dailyOrders', 'recentOrders'));
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
    
    public function createMenu()
    {
        $categories = \App\Models\Category::all();
        return view('manager.menus.create', compact('categories'));
    }
    
    public function storeMenu(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048'
        ]);
        
        $data = $request->only(['name', 'category_id', 'price', 'description']);
        
        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('menu-images', 'public');
        }
        
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
        
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048'
        ]);
        
        $data = $request->only(['name', 'category_id', 'price', 'description']);
        
        if ($request->hasFile('image')) {
            if ($menu->image_url) {
                \Storage::disk('public')->delete($menu->image_url);
            }
            $data['image_url'] = $request->file('image')->store('menu-images', 'public');
        }
        
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
        
        // Apply status filter
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
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
        $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
        $processingOrders = \App\Models\Order::where('status', 'processing')->count();
        $completedOrders = \App\Models\Order::where('status', 'completed')->count();
        
        // Get filtered orders
        $orders = $query->latest()->take(10)->get();
        
        return view('manager.orders.index', compact(
            'totalOrders',
            'pendingOrders', 
            'processingOrders',
            'completedOrders',
            'orders'
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
        
        // Revenue Overview based on period
        $dailyRevenue = [];
        $maxRevenue = 0;
        
        if ($period === 'daily') {
            // Last 7 days
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $revenue = \App\Models\Order::whereDate('created_at', $date->toDateString())
                    ->where('status', 'completed')
                    ->sum('total_amount');
                
                if ($revenue > $maxRevenue) {
                    $maxRevenue = $revenue;
                }
                
                $dailyRevenue[] = [
                    'day' => $date->format('D'),
                    'revenue' => $revenue,
                    'date' => $date->format('Y-m-d')
                ];
            }
        } elseif ($period === 'weekly') {
            // Last 7 weeks
            for ($i = 6; $i >= 0; $i--) {
                $startDate = now()->subWeeks($i)->startOfWeek();
                $endDate = now()->subWeeks($i)->endOfWeek();
                $revenue = \App\Models\Order::whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'completed')
                    ->sum('total_amount');
                
                if ($revenue > $maxRevenue) {
                    $maxRevenue = $revenue;
                }
                
                $dailyRevenue[] = [
                    'day' => 'W' . $startDate->week,
                    'revenue' => $revenue,
                    'date' => $startDate->format('M d')
                ];
            }
        } else { // monthly
            // Last 7 months
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $revenue = \App\Models\Order::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('status', 'completed')
                    ->sum('total_amount');
                
                if ($revenue > $maxRevenue) {
                    $maxRevenue = $revenue;
                }
                
                $dailyRevenue[] = [
                    'day' => $date->format('M'),
                    'revenue' => $revenue,
                    'date' => $date->format('Y-m')
                ];
            }
        }
        
        // Category Performance
        $categoryStats = \App\Models\Category::withCount(['menus as total_sold' => function($query) {
                $query->join('order_items', 'menus.id', '=', 'order_items.menu_id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->where('orders.status', 'completed');
            }])
            ->get();
        
        $totalSold = $categoryStats->sum('total_sold');
        
        $categoryPerformance = $categoryStats->map(function($category) use ($totalSold) {
            $percentage = $totalSold > 0 ? round(($category->total_sold / $totalSold) * 100) : 0;
            return [
                'name' => $category->name,
                'percentage' => $percentage,
                'total_sold' => $category->total_sold
            ];
        })->sortByDesc('percentage')->values();
        
        return view('manager.reports.index', compact('dailyRevenue', 'maxRevenue', 'categoryPerformance', 'period'));
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
