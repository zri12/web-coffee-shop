@extends('layouts.dashboard')

@section('title', 'Dashboard Overview')

@section('content')
<div class="max-w-[1200px] mx-auto flex flex-col gap-8 p-8">
    <!-- Highlight Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Orders Today -->
        <div class="bg-white dark:bg-[#1a1612] p-6 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-primary/10 rounded-lg text-primary">
                    <span class="material-symbols-outlined">shopping_cart</span>
                </div>
                <span class="flex items-center text-[#d47311] bg-orange-50 px-2 py-1 rounded text-xs font-medium border border-orange-100">
                    Active: {{ $stats['pending_orders'] + $stats['preparing_orders'] }}
                </span>
            </div>
            <p class="text-[#897561] text-sm font-medium">Orders Today</p>
            <h3 class="text-[#181411] dark:text-white text-3xl font-bold mt-1">{{ $stats['total_orders_today'] }}</h3>
            <p class="text-xs text-[#897561] mt-2">Completed: {{ $stats['completed_orders_today'] }} • Preparing: {{ $stats['preparing_orders'] }} • Pending: {{ $stats['pending_orders'] }}</p>
        </div>

        <!-- Kitchen Load -->
        <div class="bg-white dark:bg-[#1a1612] p-6 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-[#0ea5e9]/10 rounded-lg text-[#0ea5e9]">
                    <span class="material-symbols-outlined">restaurant</span>
                </div>
                <span class="text-xs text-[#0ea5e9] font-semibold">Kitchen Pulse</span>
            </div>
            <p class="text-[#897561] text-sm font-medium">Orders in Progress</p>
            <h3 class="text-[#181411] dark:text-white text-3xl font-bold mt-1">{{ $stats['preparing_orders'] }}</h3>
            @php
                $activeTotal = max(1, $stats['pending_orders'] + $stats['preparing_orders']);
                $loadPct = min(100, ($stats['preparing_orders'] / $activeTotal) * 100);
            @endphp
            <div class="w-full bg-[#f4f2f0] dark:bg-[#2c241b] h-2 rounded-full mt-4 overflow-hidden">
                <div class="bg-[#0ea5e9] h-full rounded-full transition-all" style="width: {{ $loadPct }}%"></div>
            </div>
            <p class="text-xs text-[#897561] mt-2">Pending queue: {{ $stats['pending_orders'] }} menunggu dapur</p>
        </div>

        <!-- Stock Health -->
        <div class="bg-white dark:bg-[#1a1612] p-6 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-[#16a34a]/10 rounded-lg text-[#16a34a]">
                    <span class="material-symbols-outlined">inventory_2</span>
                </div>
                <span class="text-xs text-[#897561]">Storage</span>
            </div>
            <p class="text-[#897561] text-sm font-medium">Stock Health</p>
            <h3 class="text-[#181411] dark:text-white text-3xl font-bold mt-1">{{ $stats['available_menus'] }}/{{ $stats['total_menus'] }} menu ready</h3>
            <p class="text-xs text-[#897561] mt-2">Low stock: {{ $stats['low_stock'] }} • Out: {{ $stats['out_stock'] }}</p>
        </div>
    </div>

    <!-- Status / Inventory Snapshot -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] p-6 shadow-sm lg:col-span-2">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-[#181411] dark:text-white text-lg font-bold">Order Pipeline Today</h3>
                    <p class="text-[#897561] text-sm">Ringkasan status pesanan</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary">Live</span>
            </div>
            @php
                $tot = max(1, array_sum($ordersByStatus));
                $bar = [
                    'completed' => $ordersByStatus['completed'] ?? 0,
                    'processing' => ($ordersByStatus['processing'] ?? 0) + ($ordersByStatus['preparing'] ?? 0),
                    'pending' => ($ordersByStatus['pending'] ?? 0) + ($ordersByStatus['waiting_payment'] ?? 0) + ($ordersByStatus['waiting_cashier_confirmation'] ?? 0),
                    'cancelled' => $ordersByStatus['cancelled'] ?? 0,
                ];
            @endphp
            <div class="w-full bg-[#f4f2f0] dark:bg-[#2c241b] h-3 rounded-full overflow-hidden flex">
                <div class="bg-[#16a34a] h-full" style="width: {{ ($bar['completed'] / $tot) * 100 }}%"></div>
                <div class="bg-[#0ea5e9] h-full" style="width: {{ ($bar['processing'] / $tot) * 100 }}%"></div>
                <div class="bg-[#f59e0b] h-full" style="width: {{ ($bar['pending'] / $tot) * 100 }}%"></div>
                <div class="bg-[#ef4444] h-full" style="width: {{ ($bar['cancelled'] / $tot) * 100 }}%"></div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4 text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#16a34a]"></span>
                    <div>
                        <p class="text-[#897561] text-xs">Completed</p>
                        <p class="text-[#181411] dark:text-white font-semibold">{{ $bar['completed'] }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#0ea5e9]"></span>
                    <div>
                        <p class="text-[#897561] text-xs">Preparing</p>
                        <p class="text-[#181411] dark:text-white font-semibold">{{ $bar['processing'] }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#f59e0b]"></span>
                    <div>
                        <p class="text-[#897561] text-xs">Waiting</p>
                        <p class="text-[#181411] dark:text-white font-semibold">{{ $bar['pending'] }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#ef4444]"></span>
                    <div>
                        <p class="text-[#897561] text-xs">Cancelled</p>
                        <p class="text-[#181411] dark:text-white font-semibold">{{ $bar['cancelled'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] p-6 shadow-sm space-y-4">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-[#181411] dark:text-white text-lg font-bold">Menu & Stock</h3>
                    <p class="text-[#897561] text-sm">Ketersediaan produk</p>
                </div>
                <span class="material-symbols-outlined text-primary">bar_chart</span>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-[#897561]">Menu available</p>
                    <p class="text-2xl font-bold text-[#181411] dark:text-white">{{ $stats['available_menus'] }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-[#897561]">Total menus</p>
                    <p class="text-2xl font-bold text-[#181411] dark:text-white">{{ $stats['total_menus'] }}</p>
                </div>
            </div>
            @php
                $availPct = $stats['total_menus'] > 0 ? ($stats['available_menus'] / $stats['total_menus']) * 100 : 0;
            @endphp
            <div class="w-full bg-[#f4f2f0] dark:bg-[#2c241b] h-2 rounded-full overflow-hidden">
                <div class="bg-primary h-full rounded-full" style="width: {{ $availPct }}%"></div>
            </div>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div class="bg-[#f4f2f0] dark:bg-[#2c241b] p-3 rounded-lg">
                    <p class="text-xs text-[#897561]">Low stock</p>
                    <p class="text-lg font-semibold text-[#181411] dark:text-white">{{ $stats['low_stock'] }}</p>
                </div>
                <div class="bg-[#fef2f2] dark:bg-[#2a1717] p-3 rounded-lg">
                    <p class="text-xs text-[#b91c1c]">Out of stock</p>
                    <p class="text-lg font-semibold text-[#b91c1c]">{{ $stats['out_stock'] }}</p>
                </div>
            </div>
            <div class="bg-[#f4f2f0] dark:bg-[#2c241b] p-3 rounded-lg">
                <p class="text-xs text-[#897561] mb-1">Most Popular</p>
                <p class="text-sm font-semibold text-[#181411] dark:text-white truncate">{{ $bestSeller->name ?? 'N/A' }}</p>
                <p class="text-xs text-[#897561]">{{ $bestSeller->total_sold ?? 0 }} sold all time</p>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <h3 class="text-[#181411] dark:text-white text-lg font-bold">Recent Orders</h3>
            <a class="text-sm font-medium text-primary hover:text-primary/80" href="{{ route('dashboard.orders') }}">View All</a>
        </div>
        <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-[#f8f7f6] dark:bg-[#2c241b] border-b border-[#e6e0db] dark:border-[#3d362e]">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-[#897561] uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-4 text-xs font-semibold text-[#897561] uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-xs font-semibold text-[#897561] uppercase tracking-wider">Items Summary</th>
                            <th class="px-6 py-4 text-xs font-semibold text-[#897561] uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-xs font-semibold text-[#897561] uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-[#897561] uppercase tracking-wider text-right">Time</th>
                            <th class="px-6 py-4 text-xs font-semibold text-[#897561] uppercase tracking-wider text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e6e0db] dark:divide-[#3d362e]">
                        @forelse($recentOrders as $order)
                        <tr class="hover:bg-[#fcfbf9] dark:hover:bg-[#25201a] transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-[#181411] dark:text-white">{{ $order->order_number }}</td>
                            <td class="px-6 py-4 text-sm text-[#5c4d40] dark:text-[#a89c92]">
                                {{ $order->customer_name ?? 'Guest' }}
                                @if($order->table_number) 
                                <span class="text-xs text-[#897561] block">Table {{ $order->table_number }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-[#5c4d40] dark:text-[#a89c92]">
                                {{ $order->items->first()->menu->name ?? 'Unknown item' }}
                                @if($order->items->count() > 1)
                                    <span class="text-xs text-[#897561] ml-1">+{{ $order->items->count() - 1 }} more</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-[#181411] dark:text-white">{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClass = match($order->status) {
                                        'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'processing', 'preparing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                        'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }} capitalize">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#897561] text-right">{{ $order->created_at->diffForHumans() }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('dashboard.orders.show', $order) }}" class="text-[#897561] hover:text-primary transition-colors p-2 rounded-full hover:bg-primary/10 inline-flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-[#897561]">No recent orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
