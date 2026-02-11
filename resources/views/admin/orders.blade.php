@extends('layouts.dashboard')

@section('title', 'Orders Management')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-[#181411] dark:text-white">Order Management</h1>
            <p class="text-[#897561] text-sm">Track and manage all orders.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] p-4">
        <form method="GET" class="flex flex-col md:flex-row flex-wrap gap-3 md:gap-4 items-end">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-bold text-[#897561] uppercase tracking-wider mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#2c241b] text-[#181411] dark:text-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-bold text-[#897561] uppercase tracking-wider mb-2">Payment Status</label>
                <select name="payment_status" class="w-full px-4 py-2 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#2c241b] text-[#181411] dark:text-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">All Payments</option>
                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-bold text-[#897561] uppercase tracking-wider mb-2">Date</label>
                <input type="date" name="date" value="{{ request('date') }}" 
                       class="w-full px-4 py-2 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#2c241b] text-[#181411] dark:text-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            
            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-none px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm font-medium">
                    Filter
                </button>
                
                <a href="{{ route('admin.orders') }}" class="flex-1 md:flex-none px-6 py-2 border border-[#e6e0db] dark:border-[#3d362e] text-[#897561] rounded-lg hover:bg-gray-50 dark:hover:bg-[#2c241b] transition-colors text-center text-sm font-medium">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-[#1a1612] p-4 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] flex items-center gap-4">
            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-full">
                <span class="material-symbols-outlined">shopping_bag</span>
            </div>
            <div>
                <p class="text-xs font-bold text-[#897561] uppercase">Total Orders</p>
                <h4 class="text-xl font-bold text-[#181411] dark:text-white">{{ number_format($totalOrders ?? 0) }}</h4>
            </div>
        </div>
        <div class="bg-white dark:bg-[#1a1612] p-4 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] flex items-center gap-4">
            <div class="p-3 bg-orange-50 dark:bg-orange-900/20 text-orange-600 rounded-full">
                <span class="material-symbols-outlined">pending</span>
            </div>
            <div>
                <p class="text-xs font-bold text-[#897561] uppercase">Pending</p>
                <h4 class="text-xl font-bold text-[#181411] dark:text-white">{{ $pendingOrders ?? 0 }}</h4>
            </div>
        </div>
        <div class="bg-white dark:bg-[#1a1612] p-4 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] flex items-center gap-4">
            <div class="p-3 bg-purple-50 dark:bg-purple-900/20 text-purple-600 rounded-full">
                <span class="material-symbols-outlined">local_shipping</span>
            </div>
            <div>
                <p class="text-xs font-bold text-[#897561] uppercase">Processing</p>
                <h4 class="text-xl font-bold text-[#181411] dark:text-white">{{ $processingOrders ?? 0 }}</h4>
            </div>
        </div>
        <div class="bg-white dark:bg-[#1a1612] p-4 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] flex items-center gap-4">
            <div class="p-3 bg-green-50 dark:bg-green-900/20 text-green-600 rounded-full">
                <span class="material-symbols-outlined">check_circle</span>
            </div>
            <div>
                <p class="text-xs font-bold text-[#897561] uppercase">Completed</p>
                <h4 class="text-xl font-bold text-[#181411] dark:text-white">{{ $completedOrders ?? 0 }}</h4>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-[#2c241b] border-b border-[#e6e0db] dark:border-[#3d362e]">
                    <tr>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Order ID</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Date</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Customer</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Items</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs text-right">Total</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Status</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Payment</th>
                        <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e6e0db] dark:divide-[#3d362e]">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 dark:hover:bg-[#2c241b] transition-colors">
                        <td class="px-6 py-4 font-mono text-sm text-primary font-bold">#{{ $order->order_number }}</td>
                        <td class="px-6 py-4 text-sm text-[#897561] dark:text-[#a89c92]">
                            {{ $order->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-[#181411] dark:text-white">
                            {{ $order->customer_name ?? 'Guest' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-[#897561] dark:text-[#a89c92]">
                            {{ $order->items->count() }} item(s)
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-[#181411] dark:text-white">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400',
                                    'processing' => 'bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400',
                                    'completed' => 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400',
                                    'cancelled' => 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold uppercase {{ $statusColors[$order->status] ?? '' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($order->payment_status == 'paid')
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold uppercase bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400">
                                    Paid
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold uppercase bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400">
                                    Unpaid
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.orders.detail', $order) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors inline-block">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <span class="material-symbols-outlined text-6xl text-[#897561]/30">receipt_long</span>
                            <p class="text-[#897561] dark:text-[#a89c92] mt-4">No orders found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($orders->hasPages())
        <div class="p-6 border-t border-[#e6e0db] dark:border-[#3d362e]">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
