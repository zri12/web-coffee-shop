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
        // Get ALL orders that are not completed or cancelled
        $allActiveOrders = \App\Models\Order::with(['items.menu', 'payment'])
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Categorize orders
        $waitingPaymentOrders = $allActiveOrders->filter(function($order) {
            return in_array($order->status, ['waiting_payment', 'pending']) && $order->payment_status === 'unpaid';
        });
        
        $paidOrders = $allActiveOrders->filter(function($order) {
            return ($order->status === 'paid') || 
                   ($order->status === 'pending' && $order->payment_status === 'paid');
        });
        
        $preparingOrders = $allActiveOrders->filter(function($order) {
            return in_array($order->status, ['preparing', 'processing']);
        });
        
        // Get completed orders for today
        $readyOrders = \App\Models\Order::with(['items.menu', 'payment'])
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();
        
        // Stats for header
        $stats = [
            'waiting_payment' => $waitingPaymentOrders->count(),
            'preparing' => $preparingOrders->count(),
            'completed_today' => $readyOrders->count(),
        ];
        
        return view('cashier.incoming-orders', compact('stats', 'waitingPaymentOrders', 'paidOrders', 'preparingOrders', 'readyOrders'));
    }

    // New Orders - Order dari QR meja & online dengan status
    public function orders(Request $request)
    {
        $query = \App\Models\Order::with(['items.menu', 'payment'])
            ->withCount('items');
        
        // Filter by status if provided
        if ($request->has('status') && $request->status !== '') {
            $filterStatus = $request->status;
            
            // Handle special filter for unpaid orders
            if ($filterStatus === 'unpaid') {
                $query->where('payment_status', 'unpaid');
            } elseif ($filterStatus === 'waiting_payment') {
                $query->where('status', 'waiting_payment');
            } elseif ($filterStatus === 'paid') {
                $query->where('status', 'paid');
            } elseif ($filterStatus === 'preparing') {
                $query->where('status', 'preparing');
            } elseif ($filterStatus === 'completed') {
                $query->where('status', 'completed');
            } elseif ($filterStatus === 'cancelled') {
                $query->where('status', 'cancelled');
            } else {
                $query->where('status', $filterStatus);
            }
        }
        
        // Order by newest first (created_at DESC)
        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
            
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
                'table_number' => 'required|string',
                'customer_name' => 'nullable|string|max:255',
                'items' => 'required|array|min:1',
                'items.*.menu_id' => 'required|exists:menus,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'nullable|numeric|min:0',
                'items.*.notes' => 'nullable|string',
                'payment_method' => 'required|in:cash,card,qris',
            ]);

            // Parse table_number: try to find table ID from tables table
            $tableLabel = $validated['table_number']; // e.g., "Outdoor-1"
            $tableId = null;
            
            // Try to find table by table_number (exact match)
            $table = \App\Models\Table::where('table_number', $tableLabel)->first();
            if ($table) {
                $tableId = $table->id;
            }

            // =====================================================================
            // MANUAL ORDER PAYMENT LOGIC
            // =====================================================================
            // ALL manual orders (walk-in) are PAID immediately at the cashier
            // Cashier collects payment BEFORE creating the order
            // - CASH: Customer pays cash at counter
            // - CARD: Customer pays via EDC machine (DEBIT/CREDIT)
            // - QRIS: Customer pays via merchant QRIS scanner (NOT Midtrans)
            // 
            // Therefore: ALL payment_method → payment_status = 'paid'
            // =====================================================================
            $paymentStatus = 'paid'; // ALWAYS paid for walk-in orders
            $orderStatus = 'pending'; // Ready to be prepared by kitchen

            // Create order with correct structure
            $order = \App\Models\Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'user_id' => auth()->id(), // Cashier who created the order
                'table_number' => $tableId, // Numeric ID or NULL
                'table_label' => $tableLabel, // Display name (Outdoor-1, VIP-2, etc.)
                'customer_name' => $validated['customer_name'] ?? 'Guest',
                'order_type' => 'dine_in', // Walk-in order
                'status' => $orderStatus,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $paymentStatus,
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
            $paymentRecordStatus = ($paymentStatus === 'paid') ? 'paid' : 'pending';
            \App\Models\Payment::create([
                'order_id' => $order->id,
                'amount' => $totalWithTax,
                'method' => $validated['payment_method'],
                'status' => $paymentRecordStatus,
                'paid_at' => ($paymentStatus === 'paid') ? now() : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'order_id' => $order->id,
                'payment_status' => $paymentStatus
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Failed to create manual order: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
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

    // Get Order Details for Modal
    public function getOrderDetails($id)
    {
        try {
            $order = \App\Models\Order::with(['items.menu', 'user', 'payment'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'table_number' => $order->table_number,
                    'order_type' => $order->order_type ?? 'Walk-in',
                    'status' => $order->status,
                    'payment_method' => $order->payment_method,
                    'payment_status' => $order->payment_status,
                    'total_amount' => $order->total_amount,
                    'notes' => $order->notes,
                    'created_at' => $order->created_at->format('d M Y, H:i'),
                    'cashier_name' => $order->user ? $order->user->name : 'System',
                    'items' => $order->items->map(function($item) {
                        return [
                            'menu_name' => $item->menu_name ?: ($item->menu ? $item->menu->name : 'Unknown Item'),
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price ?: $item->price,
                            'subtotal' => $item->subtotal ?: ($item->quantity * ($item->unit_price ?: $item->price)),
                            'notes' => $item->notes,
                            'options' => $item->options,
                        ];
                    }),
                    // Calculate tax
                    'tax_rate' => 0.05, // 5%
                    'tax_amount' => $order->total_amount * 0.05 / 1.05, // reverse calculate
                    'subtotal' => $order->total_amount / 1.05 // subtotal before tax
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
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
        
        // Valid status constants: Support both old and new status values
        // OLD: pending, processing, completed, cancelled
        // NEW: waiting_payment, paid, preparing, completed, cancelled
        $validStatuses = ['waiting_payment', 'paid', 'preparing', 'completed', 'cancelled', 'pending', 'processing'];
        
        $validated = $request->validate([
            'status' => 'required|in:waiting_payment,paid,preparing,completed,cancelled,pending,processing'
        ]);
        
        // Ensure status is clean and valid
        $newStatus = strtolower(trim($validated['status']));
        
        if (!in_array($newStatus, $validStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status value'
            ], 400);
        }
        
        // BUSINESS RULE: Cannot start preparing unpaid orders
        if (in_array($newStatus, ['preparing', 'processing']) && $order->payment_status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot start preparing: Payment not yet confirmed. Please confirm payment first.'
            ], 400);
        }
        
        // Log status change
        \Log::info("Order {$order->order_number} status changed from {$order->status} to {$newStatus} by " . auth()->user()->name);
        
        // Update with validated clean value
        $order->update(['status' => $newStatus]);
        
        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully'
        ]);
    }

    // Process Payment
    public function processPayment(Request $request, $id)
    {
        $order = \App\Models\Order::with('payment')->findOrFail($id);
        
        // Update order payment status
        $order->update(['payment_status' => 'paid']);
        
        // Update payment record status
        if ($order->payment) {
            $order->payment->update(['status' => 'paid']);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully'
        ]);
    }

    // Confirm Cash Payment
    public function confirmPayment($id)
    {
        try {
            $order = \App\Models\Order::with('payment')->findOrFail($id);
            
            // Validate it's a cash order
            if ($order->payment_method !== 'cash') {
                return response()->json([
                    'success' => false,
                    'message' => 'This is not a cash order'
                ], 400);
            }
            
            // Check if already paid
            if ($order->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment already confirmed'
                ], 400);
            }
            
            // CRITICAL: Update both payment_status AND status
            // After cash payment confirmed: waiting_payment/pending -> paid
            $order->update([
                'payment_status' => 'paid',
                'status' => 'paid' // Status berubah ke 'paid' setelah konfirmasi (NEW flow)
            ]);
            
            // Update payment record if exists
            if ($order->payment) {
                $order->payment->update([
                    'status' => 'paid',
                    'paid_at' => now()
                ]);
            }
            
            \Log::info("Cash payment confirmed for order {$order->order_number} by " . auth()->user()->name);
            
            return response()->json([
                'success' => true,
                'message' => 'Cash payment confirmed successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to confirm cash payment: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm payment: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Print Kitchen Order
    public function printKitchen($id)
    {
        $order = \App\Models\Order::with(['items.menu'])->findOrFail($id);
        
        // Only allow printing if order is paid
        if ($order->payment_status !== 'paid') {
            abort(403, 'Cannot print kitchen order: Payment not confirmed');
        }
        
        return view('cashier.print-kitchen', compact('order'));
    }

    public function printBill($id)
    {
        $order = \App\Models\Order::with(['items.menu'])->findOrFail($id);
        
        // Only allow printing if order is paid
        if ($order->payment_status !== 'paid') {
            abort(403, 'Cannot print bill: Payment not confirmed');
        }
        
        return view('cashier.print-bill', compact('order'));
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
