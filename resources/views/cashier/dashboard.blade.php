@extends('layouts.dashboard')

@section('title', 'Cashier Dashboard')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#181411] dark:text-white">Dashboard</h1>
            <p class="text-sm text-[#897561] dark:text-[#a89c92] mt-1">Ringkasan hari ini dan order masuk</p>
        </div>
        <div class="flex items-center gap-2 text-sm text-[#897561] dark:text-[#a89c92]">
            <span class="material-symbols-outlined text-[20px]">schedule</span>
            <span>{{ now()->format('l, d F Y') }}</span>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Pending Orders -->
        <div class="bg-white dark:bg-[#2d2115] rounded-xl p-4 border border-[#f4f2f0] dark:border-[#3e2d23]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[#897561] dark:text-[#a89c92]">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $stats['pending'] }}</p>
                </div>
                <div class="size-12 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-yellow-600 dark:text-yellow-400">pending</span>
                </div>
            </div>
        </div>

        <!-- Preparing Orders -->
        <div class="bg-white dark:bg-[#2d2115] rounded-xl p-4 border border-[#f4f2f0] dark:border-[#3e2d23]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[#897561] dark:text-[#a89c92]">Preparing</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $stats['preparing'] }}</p>
                </div>
                <div class="size-12 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">restaurant</span>
                </div>
            </div>
        </div>

        <!-- Served Today -->
        <div class="bg-white dark:bg-[#2d2115] rounded-xl p-4 border border-[#f4f2f0] dark:border-[#3e2d23]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[#897561] dark:text-[#a89c92]">Served Today</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $stats['served'] }}</p>
                </div>
                <div class="size-12 rounded-lg bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600 dark:text-green-400">check_circle</span>
                </div>
            </div>
        </div>

        <!-- Today Revenue -->
        <div class="bg-white dark:bg-[#2d2115] rounded-xl p-4 border border-[#f4f2f0] dark:border-[#3e2d23]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[#897561] dark:text-[#a89c92]">Today Revenue</p>
                    <p class="text-2xl font-bold text-primary mt-1">Rp {{ number_format($stats['today_revenue'], 0, ',', '.') }}</p>
                </div>
                <div class="size-12 rounded-lg bg-primary/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary">payments</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Orders -->
    <div class="bg-white dark:bg-[#2d2115] rounded-xl border border-[#f4f2f0] dark:border-[#3e2d23] overflow-hidden">
        <div class="p-4 border-b border-[#f4f2f0] dark:border-[#3e2d23] flex items-center justify-between">
            <h2 class="text-lg font-bold text-[#181411] dark:text-white">Active Orders</h2>
            <span class="px-3 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium">{{ $activeOrders->count() }} orders</span>
        </div>

        @if($activeOrders->isEmpty())
        <div class="p-12 text-center">
            <div class="size-16 mx-auto rounded-full bg-[#f4f2f0] dark:bg-[#221910] flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[#897561] dark:text-[#a89c92] text-[32px]">receipt_long</span>
            </div>
            <h3 class="text-lg font-medium text-[#181411] dark:text-white mb-2">No Active Orders</h3>
            <p class="text-sm text-[#897561] dark:text-[#a89c92]">All orders have been completed</p>
        </div>
        @else
        <div class="divide-y divide-[#f4f2f0] dark:divide-[#3e2d23]">
            @foreach($activeOrders as $order)
            <div class="p-4 hover:bg-[#fdfbf7] dark:hover:bg-[#221910] transition-colors">
                <div class="flex items-start justify-between gap-4">
                    <!-- Order Info -->
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="font-bold text-[#181411] dark:text-white">{{ $order->order_number }}</h3>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($order->status === 'pending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400
                                @else bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-4 text-sm text-[#897561] dark:text-[#a89c92] mb-3">
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">table_restaurant</span>
                                <span>Table {{ $order->table_number }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">schedule</span>
                                <span>{{ $order->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="space-y-1">
                            @foreach($order->items as $item)
                            <div class="flex items-center gap-2 text-sm">
                                <span class="text-[#897561] dark:text-[#a89c92]">{{ $item->quantity }}x</span>
                                <span class="text-[#181411] dark:text-white">{{ $item->menu_name ?? $item->menu->name ?? 'Menu Item' }}</span>
                                @if($item->notes)
                                <span class="text-xs text-[#897561] dark:text-[#a89c92] italic">({{ $item->notes }})</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Total & Actions -->
                    <div class="text-right">
                        <p class="text-lg font-bold text-primary mb-3">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        <div class="flex gap-2">
                            @if($order->status === 'pending')
                            <button onclick="updateOrderStatus({{ $order->id }}, 'processing')" class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                Start Preparing
                            </button>
                            @else
                            <button onclick="updateOrderStatus({{ $order->id }}, 'completed')" class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                                Mark Served
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
async function updateOrderStatus(orderId, status) {
    try {
        const response = await fetch(`/cashier/orders/${orderId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: status })
        });

        const data = await response.json();

        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Failed to update order status');
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
    }
}
</script>
@endpush
@endsection
