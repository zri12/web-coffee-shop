@extends('layouts.dashboard')

@section('title', 'Payments')

@section('content')
<div class="p-6 space-y-6" x-data="paymentsManager()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#181411] dark:text-white">Payments</h1>
            <p class="text-sm text-[#897561] dark:text-[#a89c92] mt-1">Manage cash and QRIS payments</p>
        </div>
        
        <!-- Filter Tabs -->
        <div class="flex gap-2">
            <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-primary text-white' : 'bg-white dark:bg-[#2d2115] text-[#181411] dark:text-white border border-[#f4f2f0] dark:border-[#3e2d23]'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                All
            </button>
            <button @click="filter = 'unpaid'" :class="filter === 'unpaid' ? 'bg-primary text-white' : 'bg-white dark:bg-[#2d2115] text-[#181411] dark:text-white border border-[#f4f2f0] dark:border-[#3e2d23]'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Unpaid
            </button>
            <button @click="filter = 'paid'" :class="filter === 'paid' ? 'bg-primary text-white' : 'bg-white dark:bg-[#2d2115] text-[#181411] dark:text-white border border-[#f4f2f0] dark:border-[#3e2d23]'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Paid
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 rounded-xl p-4 border border-yellow-200 dark:border-yellow-800">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-yellow-700 dark:text-yellow-400">Unpaid</span>
                <span class="material-symbols-outlined text-yellow-600 dark:text-yellow-400">pending</span>
            </div>
            <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-300">{{ $orders->where('payment_status', 'unpaid')->count() }}</p>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl p-4 border border-green-200 dark:border-green-800">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-green-700 dark:text-green-400">Paid Today</span>
                <span class="material-symbols-outlined text-green-600 dark:text-green-400">check_circle</span>
            </div>
            <p class="text-2xl font-bold text-green-900 dark:text-green-300">{{ $orders->where('payment_status', 'paid')->count() }}</p>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-blue-700 dark:text-blue-400">Cash</span>
                <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">payments</span>
            </div>
            <p class="text-2xl font-bold text-blue-900 dark:text-blue-300">{{ $orders->where('payment_method', 'cash')->count() }}</p>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl p-4 border border-purple-200 dark:border-purple-800">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-purple-700 dark:text-purple-400">QRIS</span>
                <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">qr_code</span>
            </div>
            <p class="text-2xl font-bold text-purple-900 dark:text-purple-300">{{ $orders->where('payment_method', 'qris')->count() }}</p>
        </div>
    </div>

    <!-- Orders List -->
    <div class="bg-white dark:bg-[#2d2115] rounded-xl border border-[#f4f2f0] dark:border-[#3e2d23] overflow-hidden">
        <div class="p-4 border-b border-[#f4f2f0] dark:border-[#3e2d23]">
            <h2 class="text-lg font-bold text-[#181411] dark:text-white">Payment Queue</h2>
        </div>

        @if($orders->isEmpty())
        <div class="p-12 text-center">
            <div class="size-16 mx-auto rounded-full bg-[#f4f2f0] dark:bg-[#221910] flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[#897561] dark:text-[#a89c92] text-[32px]">payments</span>
            </div>
            <h3 class="text-lg font-medium text-[#181411] dark:text-white mb-2">No Pending Payments</h3>
            <p class="text-sm text-[#897561] dark:text-[#a89c92]">All orders have been paid</p>
        </div>
        @else
        <div class="divide-y divide-[#f4f2f0] dark:divide-[#3e2d23]">
            @foreach($orders as $order)
            <div x-show="filter === 'all' || filter === '{{ $order->payment_status }}'" class="p-4 hover:bg-[#fdfbf7] dark:hover:bg-[#221910] transition-colors">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="font-bold text-[#181411] dark:text-white">{{ $order->order_number }}</h3>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($order->payment_status === 'unpaid') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400
                                @else bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                                @endif">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                {{ strtoupper($order->payment_method) }}
                            </span>
                        </div>

                        <div class="flex items-center gap-4 text-sm text-[#897561] dark:text-[#a89c92] mb-3">
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">table_restaurant</span>
                                <span>Table {{ $order->table_number }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">schedule</span>
                                <span>{{ $order->created_at->format('H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">shopping_bag</span>
                                <span>{{ $order->items->count() }} items</span>
                            </div>
                        </div>

                        <!-- Order Items Preview -->
                        <div class="flex flex-wrap gap-2">
                            @foreach($order->items->take(3) as $item)
                            <span class="px-2 py-1 bg-[#f4f2f0] dark:bg-[#221910] rounded text-xs text-[#181411] dark:text-white">
                                {{ $item->quantity }}x {{ $item->menu->name }}
                            </span>
                            @endforeach
                            @if($order->items->count() > 3)
                            <span class="px-2 py-1 bg-[#f4f2f0] dark:bg-[#221910] rounded text-xs text-[#897561] dark:text-[#a89c92]">
                                +{{ $order->items->count() - 3 }} more
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-2xl font-bold text-primary mb-3">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        
                        @if($order->payment_status === 'unpaid')
                        <button @click="processPayment({{ $order->id }}, '{{ $order->payment_method }}')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                            Mark as Paid
                        </button>
                        @else
                        <div class="flex items-center gap-2 text-green-600 dark:text-green-400">
                            <span class="material-symbols-outlined text-[20px]">check_circle</span>
                            <span class="text-sm font-medium">Paid</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $orders->links() }}
    </div>
</div>

@push('scripts')
<script>
function paymentsManager() {
    return {
        filter: 'all',

        async processPayment(orderId, method) {
            if (!confirm('Mark this order as paid?')) return;

            try {
                const response = await fetch(`/cashier/payments/${orderId}/process`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ payment_method: method })
                });

                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to process payment');
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        }
    }
}
</script>
@endpush
@endsection
