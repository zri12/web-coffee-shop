@extends('layouts.dashboard')

@section('title', 'Manager Dashboard')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-[#181411] dark:text-white">Dashboard Overview</h1>
        <p class="text-[#897561] text-sm">Welcome back, {{ auth()->user()->name }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Revenue -->
        <div class="bg-white dark:bg-[#1a1612] p-5 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[#897561] text-xs font-bold uppercase tracking-wider">Daily Revenue</p>
                    <h3 class="text-2xl font-bold text-[#181411] dark:text-white mt-1">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</h3>
                </div>
                <div class="p-2 bg-green-50 dark:bg-green-900/20 text-green-600 rounded-lg">
                    <span class="material-symbols-outlined">payments</span>
                </div>
            </div>
            <div class="flex items-center gap-1 text-xs font-medium text-green-600">
                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                <span>+5.2% vs yesterday</span>
            </div>
        </div>

        <!-- Orders -->
        <div class="bg-white dark:bg-[#1a1612] p-5 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[#897561] text-xs font-bold uppercase tracking-wider">Total Orders</p>
                    <h3 class="text-2xl font-bold text-[#181411] dark:text-white mt-1">{{ $stats['orders'] }}</h3>
                </div>
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-lg">
                    <span class="material-symbols-outlined">shopping_cart</span>
                </div>
            </div>
            <div class="flex items-center gap-1 text-xs font-medium text-green-600">
                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                <span>+3.1% vs yesterday</span>
            </div>
        </div>

        <!-- Avg Value -->
        <div class="bg-white dark:bg-[#1a1612] p-5 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[#897561] text-xs font-bold uppercase tracking-wider">Avg. Order Value</p>
                    <h3 class="text-2xl font-bold text-[#181411] dark:text-white mt-1">Rp {{ number_format($stats['avg_order_value'], 0, ',', '.') }}</h3>
                </div>
                <div class="p-2 bg-orange-50 dark:bg-orange-900/20 text-orange-600 rounded-lg">
                    <span class="material-symbols-outlined">receipt_long</span>
                </div>
            </div>
            <div class="flex items-center gap-1 text-xs font-medium text-red-500">
                <span class="material-symbols-outlined text-[16px]">trending_down</span>
                <span>-1.5% vs yesterday</span>
            </div>
        </div>

        <!-- Occupancy -->
        <div class="bg-white dark:bg-[#1a1612] p-5 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[#897561] text-xs font-bold uppercase tracking-wider">Table Occupancy</p>
                    <h3 class="text-2xl font-bold text-[#181411] dark:text-white mt-1">{{ $stats['occupancy'] }}%</h3>
                </div>
                <div class="p-2 bg-purple-50 dark:bg-purple-900/20 text-purple-600 rounded-lg">
                    <span class="material-symbols-outlined">chair</span>
                </div>
            </div>
            <div class="flex items-center gap-1 text-xs font-medium text-green-600">
                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                <span>+10% peak hour</span>
            </div>
        </div>
    </div>

    <!-- Daily Orders Chart (Full Width) -->
    <div class="bg-white dark:bg-[#1a1612] p-6 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-lg font-bold text-[#181411] dark:text-white">Daily Orders</h3>
                <p class="text-sm text-[#897561]">Order count over the last 7 days</p>
            </div>
        </div>
        
        <!-- Chart Container -->
        <div class="relative">
            <!-- Chart Bars -->
            <div class="flex items-end justify-between gap-4 h-80 px-4">
                @php
                    $maxCount = max(array_column($dailyOrders, 'count'));
                    $maxCount = $maxCount > 0 ? $maxCount : 1;
                @endphp
                @foreach($dailyOrders as $day)
                <div class="flex-1 flex flex-col items-center gap-2">
                    <!-- Bar -->
                    @php
                        $barHeight = $day['count'] > 0 ? (($day['count'] / $maxCount) * 100) : 2;
                    @endphp
                    <div class="w-full bg-gradient-to-t from-[#C8A17D] to-[#E5C9A8] rounded-t-lg transition-all hover:from-primary hover:to-[#C8A17D] relative group" 
                         style="height: {{ $barHeight }}%;">
                        <!-- Tooltip on hover -->
                        <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 bg-[#181411] text-white px-3 py-1.5 rounded-lg text-xs font-bold opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10">
                            {{ $day['count'] }} orders
                        </div>
                    </div>
                    <!-- Count Label -->
                    <div class="text-base font-bold text-[#181411] dark:text-white">{{ $day['count'] }}</div>
                    <!-- Day Label -->
                    <div class="text-sm text-[#897561] font-medium">{{ $day['day'] }}</div>
                    <!-- Date Label -->
                    <div class="text-xs text-[#897561]">{{ $day['date'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>


    <!-- Recent Orders Table -->
    <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm overflow-hidden">
        <div class="p-6 border-b border-[#e6e0db] dark:border-[#3d362e] flex justify-between items-center">
            <h3 class="text-lg font-bold text-[#181411] dark:text-white">Recent Orders</h3>
            <button class="text-sm font-medium text-[#5c4d40] hover:text-primary">Filter</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-[#2c241b] border-b border-[#e6e0db] dark:border-[#3d362e]">
                    <tr>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Order ID</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Customer</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Items</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Amount</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Status</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e6e0db] dark:divide-[#3d362e]">
                    @foreach($recentOrders as $order)
                    <tr class="hover:bg-gray-50 dark:hover:bg-[#2c241b]/50 transition-colors">
                        <td class="px-6 py-4 font-bold text-primary">#{{ $order->order_number }}</td>
                        <td class="px-6 py-4 font-medium text-[#181411] dark:text-white">{{ $order->customer_name }}</td>
                        <td class="px-6 py-4 text-[#5c4d40] dark:text-[#a89c92] truncate max-w-[200px]">
                            @if($order->items)
                                @foreach($order->items->take(2) as $item)
                                    {{ $item->quantity }}x {{ $item->menu->name ?? 'Item' }}{{ !$loop->last ? ',' : '' }}
                                @endforeach
                                @if($order->items->count() > 2)
                                    ...
                                @endif
                            @else
                                No items
                            @endif
                        </td>
                        <td class="px-6 py-4 font-bold text-[#181411] dark:text-white">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 rounded text-xs font-bold uppercase
                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $order->status === 'pending' ? 'bg-orange-100 text-orange-700' : '' }}
                                {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                            ">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-[#897561] text-xs">
                            {{ $order->created_at->diffForHumans() }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
