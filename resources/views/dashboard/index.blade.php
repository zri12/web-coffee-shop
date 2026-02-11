@extends('layouts.dashboard')

@section('title', 'Dashboard Overview')

@section('content')
<div class="max-w-[1200px] mx-auto flex flex-col gap-8 p-8">
    <!-- Analytics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Sales -->
        <div class="bg-white dark:bg-[#1a1612] p-6 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-primary/10 rounded-lg text-primary">
                    <span class="material-symbols-outlined">attach_money</span>
                </div>
                <span class="flex items-center text-green-600 bg-green-50 px-2 py-1 rounded text-xs font-medium border border-green-100">
                    <span class="material-symbols-outlined text-[14px] mr-1">trending_up</span> +12%
                </span>
            </div>
            <p class="text-[#897561] text-sm font-medium">Total Sales</p>
            <h3 class="text-[#181411] dark:text-white text-3xl font-bold mt-1">Rp {{ number_format($stats['total_revenue_today'], 0, ',', '.') }}</h3>
            <p class="text-xs text-[#897561] mt-2">Today's revenue</p>
        </div>

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
            
            <!-- Progress Bar -->
            <div class="w-full bg-[#f4f2f0] dark:bg-[#2c241b] h-1.5 rounded-full mt-4 overflow-hidden">
                @php
                    $percentage = min(($stats['total_orders_today'] / 100) * 100, 100); // Variable goal, e.g., 100
                @endphp
                <div class="bg-primary h-full rounded-full" style="width: {{ $percentage }}%"></div>
            </div>
            <p class="text-xs text-[#897561] mt-2">{{ $stats['total_orders_today'] }} orders recorded today</p>
        </div>

        <!-- Best Seller -->
        <div class="bg-white dark:bg-[#1a1612] p-6 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-primary/10 rounded-lg text-primary">
                    <span class="material-symbols-outlined">award_star</span>
                </div>
                <span class="text-xs text-[#897561]">Most Popular</span>
            </div>
            <p class="text-[#897561] text-sm font-medium">Best Seller</p>
            <h3 class="text-[#181411] dark:text-white text-2xl font-bold mt-1 truncate">
                {{ $bestSeller->name ?? 'N/A' }}
            </h3>
            <!-- Mini chart for trend -->
            <div class="h-10 mt-3 flex items-end gap-1">
                <div class="w-1/6 bg-[#f4f2f0] dark:bg-[#2c241b] rounded-t-sm h-[40%]"></div>
                <div class="w-1/6 bg-[#f4f2f0] dark:bg-[#2c241b] rounded-t-sm h-[60%]"></div>
                <div class="w-1/6 bg-[#f4f2f0] dark:bg-[#2c241b] rounded-t-sm h-[50%]"></div>
                <div class="w-1/6 bg-[#f4f2f0] dark:bg-[#2c241b] rounded-t-sm h-[80%]"></div>
                <div class="w-1/6 bg-primary/40 rounded-t-sm h-[70%]"></div>
                <div class="w-1/6 bg-primary rounded-t-sm h-[100%]"></div>
            </div>
            <p class="text-xs text-[#897561] mt-2">{{ $bestSeller->total_sold ?? 0 }} sold all time</p>
        </div>
    </div>

    <!-- Main Chart Section -->
    <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-[#181411] dark:text-white text-lg font-bold">Sales Overview</h3>
                <p class="text-[#897561] text-sm">Hourly performance for today</p>
            </div>
            <div class="flex bg-[#f4f2f0] dark:bg-[#2c241b] p-1 rounded-lg self-start">
                <button class="px-3 py-1 text-xs font-medium bg-white dark:bg-[#3d362e] text-[#181411] dark:text-white rounded shadow-sm border border-[#e6e0db] dark:border-[#4a423b]">Daily</button>
                <button class="px-3 py-1 text-xs font-medium text-[#897561] hover:text-[#181411] dark:hover:text-white transition-colors">Weekly</button>
                <button class="px-3 py-1 text-xs font-medium text-[#897561] hover:text-[#181411] dark:hover:text-white transition-colors">Monthly</button>
            </div>
        </div>
        <div class="relative w-full h-[250px] overflow-hidden">
            <!-- Chart SVG from snippet, placeholder for now -->
            <svg class="w-full h-full" fill="none" preserveAspectRatio="none" viewBox="0 0 478 150">
                <defs>
                    <linearGradient gradientUnits="userSpaceOnUse" id="chartGradient" x1="236" x2="236" y1="20" y2="150">
                        <stop stop-color="#d47311" stop-opacity="0.2"></stop>
                        <stop offset="1" stop-color="#d47311" stop-opacity="0"></stop>
                    </linearGradient>
                </defs>
                <path d="M0 109C18.15 109 18.15 21 36.3 21C54.46 21 54.46 41 72.6 41C90.7 41 90.7 93 108.9 93C127 93 127 33 145.2 33C163.3 33 163.3 101 181.5 101C199.6 101 199.6 61 217.8 61C236 61 236 45 254.1 45C272.3 45 272.3 121 290.4 121C308.6 121 308.6 149 326.7 149C344.9 149 344.9 20 363 20C381.2 20 381.2 81 399.3 81C417.5 81 417.5 129 435.6 129C453.8 129 453.8 25 472 25V150H0V109Z" fill="url(#chartGradient)"></path>
                <path d="M0 109C18.15 109 18.15 21 36.3 21C54.46 21 54.46 41 72.6 41C90.7 41 90.7 93 108.9 93C127 93 127 33 145.2 33C163.3 33 163.3 101 181.5 101C199.6 101 199.6 61 217.8 61C236 61 236 45 254.1 45C272.3 45 272.3 121 290.4 121C308.6 121 308.6 149 326.7 149C344.9 149 344.9 20 363 20C381.2 20 381.2 81 399.3 81C417.5 81 417.5 129 435.6 129C453.8 129 453.8 25 472 25" stroke="#d47311" stroke-linecap="round" stroke-width="3"></path>
            </svg>
        </div>
        <div class="flex justify-between px-4 mt-2">
            <span class="text-xs text-[#897561] font-medium">8:00 AM</span>
            <span class="text-xs text-[#897561] font-medium">10:00 AM</span>
            <span class="text-xs text-[#897561] font-medium">12:00 PM</span>
            <span class="text-xs text-[#897561] font-medium">2:00 PM</span>
            <span class="text-xs text-[#897561] font-medium">4:00 PM</span>
            <span class="text-xs text-[#897561] font-medium">6:00 PM</span>
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
                                        'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
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
