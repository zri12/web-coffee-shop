<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    // Dashboard - Ringkasan hari ini, Order masuk, Status pesanan
    public function dashboard()
    {
        // Stats
        $stats = [
            'pending' => \App\Models\Order::where('status', 'pending')->count(),
            'preparing' => \App\Models\Order::where('status', 'processing')->count(),
            'served' => \App\Models\Order::where('status', 'completed')
                ->whereDate('created_at', today())
                ->count(),
            'today_revenue' => \App\Models\Order::where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('total_amount'),
        ];

        // Active Orders (Pending & Processing)
        $activeOrders = \App\Models\Order::with('items.menu')
            ->whereIn('status', ['pending', 'processing'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('cashier.dashboard', compact('stats', 'activeOrders'));
    }
    
    // Incoming Orders - Real-time order managements
    public function incomingOrders()
    {
        // Stats for header
        $stats = [
            'pending' => \App\Models\Order::where('status', 'pending')->count(),
            'preparing' => \App\Models\Order::where('status', 'processing')->count(),
            'completed_today' => \App\Models\Order::where('status', 'completed')
                ->whereDate('created_at', today())
                ->count(),
        ];
        
        // Get active orders (pending and processing)
        $pendingOrders = \App\Models\Order::with(['items.menu', 'payment'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();
            
        $preparingOrders = \App\Models\Order::with(['items.menu', 'payment'])
            ->where('status', 'processing')
            ->orderBy('created_at', 'asc')
            ->get();
            
        $readyOrders = \App\Models\Order::with(['items.menu', 'payment'])
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
        
        return view('cashier.incoming-orders', compact('stats', 'pendingOrders', 'preparingOrders', 'readyOrders'));
    }

    // New Orders - Order dari QR meja & online dengan status
    public function orders(Request $request)
    {
        $query = \App\Models\Order::with(['items.menu', 'payment'])
            ->withCount('items');
        
        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $orders = $query->latest()->paginate(20);
            
        return view('cashier.orders.index', compact('orders'));
    }
    
    // Show Order Detail
    public function showOrder($id)
    {
        $order = \App\Models\Order::with(['items.menu', 'payment'])
            ->findOrFail($id);
            
        return view('cashier.orders.show', compact('order'));
    }

    // Manual Order - Input order langsung untuk walk-in
    public function manualOrder()
    {
        $categories = \App\Models\Category::with(['menus' => function($query) {
            $query->where('is_available', true);
        }])->get();
        
        $tables = \App\Models\Table::where('status', 'available')->get();
        
        return view('cashier.manual-order', compact('categories', 'tables'));
    }

    // Store Manual Order
    public function storeManualOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'table_number' => 'required',
                'customer_name' => 'nullable|string|max:255',
                'items' => 'required|array|min:1',
                'items.*.menu_id' => 'required|exists:menus,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'nullable|numeric|min:0',
                'items.*.notes' => 'nullable|string',
                'payment_method' => 'required|in:cash,card,qris',
            ]);

            // Create order
            $order = \App\Models\Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'table_number' => $validated['table_number'],
                'customer_name' => $validated['customer_name'] ?? 'Guest',
                'order_type' => 'dine_in',
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'unpaid',
                'total_amount' => 0,
            ]);

        // Add order items and calculate total
        $total = 0;
        foreach ($validated['items'] as $item) {
            $menu = \App\Models\Menu::find($item['menu_id']);
            
            // Use custom price if provided (includes add-ons), otherwise use menu base price
            $itemPrice = isset($item['price']) ? $item['price'] : $menu->price;
            $subtotal = $itemPrice * $item['quantity'];
            
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['menu_id'],
                'menu_name' => $menu->name,
                'quantity' => $item['quantity'],
                'price' => $itemPrice,
                'unit_price' => $itemPrice,
                'subtotal' => $subtotal,
                'notes' => $item['notes'] ?? null,
            ]);
            
            $total += $subtotal;
        }

        // Add tax (5%)
        $tax = $total * 0.05;
        $totalWithTax = $total + $tax;

        // Update order total
        $order->update(['total_amount' => $totalWithTax]);

            // Create payment record
            \App\Models\Payment::create([
                'order_id' => $order->id,
                'amount' => $totalWithTax,
                'method' => $validated['payment_method'],
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'order_id' => $order->id
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Failed to create manual order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    // Payments - Manage payments (Cash, QRIS)
    public function payments()
    {
        $orders = \App\Models\Order::with('items.menu')
            ->where('payment_status', 'unpaid')
            ->latest()
            ->paginate(20);
            
        return view('cashier.payments', compact('orders'));
    }

    // Order History - Riwayat transaksi dengan filter
    public function history(Request $request)
    {
        $query = \App\Models\Order::with('items.menu');
        
        // Filter by date if provided
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $orders = $query->latest()->paginate(20);
            
        return view('cashier.history', compact('orders'));
    }

    // Profile - Data akun kasir & ganti password
    public function profile()
    {
        return view('cashier.profile');
    }

    // Update Profile
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

    // Update Password
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

    // Update Order Status
    public function updateOrderStatus(Request $request, $id)
    {
        $order = \App\Models\Order::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);
        
        $order->update(['status' => $validated['status']]);
        
        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully'
        ]);
    }

    // Process Payment
    public function processPayment(Request $request, $id)
    {
        $order = \App\Models\Order::findOrFail($id);
        
        $order->update(['payment_status' => 'paid']);
        
        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully'
        ]);
    }

    // Legacy methods (keep for compatibility)
    public function index()
    {
        return $this->dashboard();
    }

    public function createOrder()
    {
        return $this->manualOrder();
    }

    public function create()
    {
        return $this->manualOrder();
    }

    public function store(Request $request)
    {
        return $this->storeManualOrder($request);
    }
}
