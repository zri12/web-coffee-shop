@extends('layouts.dashboard')

@section('title', 'All Orders')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-[#181411] dark:text-white">All Orders</h1>
            <p class="text-[#897561] text-sm">Manage all customer orders from web and QR.</p>
        </div>
        <div class="flex gap-2">
            <select class="px-4 py-2.5 bg-white dark:bg-[#1a1612] border border-[#e6e0db] dark:border-[#3d362e] rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary/50 shadow-sm" onchange="window.location.href='?status='+this.value">
                <option value="" {{ request('status') === null ? 'selected' : '' }}>All Status</option>
                <option value="waiting_payment" {{ request('status') === 'waiting_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>Sedang Dipersiapkan</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>
    </div>

    <div class="bg-white dark:bg-[#1a1612] rounded-2xl border border-[#e6e0db] dark:border-[#3d362e] overflow-hidden shadow-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gradient-to-r from-[#f9f6f3] to-[#f4f0eb] dark:from-[#2c241b] dark:to-[#3d362e] border-b-2 border-[#e6e0db] dark:border-[#3d362e]">
                    <tr>
                        <th class="px-6 py-4 font-bold text-[#181411] dark:text-white text-xs uppercase tracking-wide">Order ID</th>
                        <th class="px-6 py-4 font-bold text-[#181411] dark:text-white text-xs uppercase tracking-wide">Customer</th>
                        <th class="px-6 py-4 font-bold text-[#181411] dark:text-white text-xs uppercase tracking-wide">Items</th>
                        <th class="px-6 py-4 font-bold text-[#181411] dark:text-white text-xs uppercase tracking-wide">Total</th>
                        <th class="px-6 py-4 font-bold text-[#181411] dark:text-white text-xs uppercase tracking-wide">Status</th>
                        <th class="px-6 py-4 font-bold text-[#181411] dark:text-white text-xs uppercase tracking-wide">Date</th>
                        <th class="px-6 py-4 font-bold text-[#181411] dark:text-white text-xs uppercase tracking-wide text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e6e0db] dark:divide-[#3d362e]">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gradient-to-r hover:from-[#f9f6f3]/50 hover:to-transparent dark:hover:from-[#2c241b]/50 transition-all duration-200 group">
                        <td class="px-6 py-5">
                            <span class="font-bold text-[#181411] dark:text-white text-base">#{{ $order->order_number }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col gap-1">
                                <span class="font-medium text-[#181411] dark:text-white">{{ $order->customer_name }}</span>
                                @if($order->table_number && $order->order_type === 'dine_in')
                                    <span class="text-xs bg-gray-100 dark:bg-[#3d362e] text-gray-700 dark:text-gray-300 px-2 py-1 rounded-md w-fit flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">table_restaurant</span>
                                        Table {{ $order->table_number }}
                                    </span>
                                @else
                                    <span class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-1 rounded-md w-fit flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">shopping_bag</span>
                                        Takeaway
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-5 text-[#5c4d40] dark:text-[#a89c92] font-medium">
                            {{ $order->items_count }} items
                        </td>
                        <td class="px-6 py-5">
                            <span class="font-black text-lg text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-5">
                            @php
                                // LOGIC STATUS YANG BENAR SESUAI FLOW BARU
                                $statusBadge = '';
                                $statusText = '';
                                $statusIcon = '';
                                
                                if ($order->status === 'waiting_payment') {
                                    if ($order->payment_method === 'cash') {
                                        // 🟠 Menunggu Pembayaran Tunai
                                        $statusBadge = 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300';
                                        $statusText = 'Menunggu Pembayaran Tunai';
                                        $statusIcon = 'payments';
                                    } else {
                                        // 🔵 Menunggu Pembayaran QRIS
                                        $statusBadge = 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300';
                                        $statusText = 'Menunggu Pembayaran QRIS';
                                        $statusIcon = 'qr_code_2';
                                    }
                                } elseif ($order->status === 'paid') {
                                    // 🟢 Sudah Dibayar (siap diproses)
                                    $statusBadge = 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300';
                                    $statusText = 'Sudah Dibayar';
                                    $statusIcon = 'check_circle';
                                } elseif ($order->status === 'preparing') {
                                    // 🟣 Sedang Dipersiapkan
                                    $statusBadge = 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300';
                                    $statusText = 'Sedang Dipersiapkan';
                                    $statusIcon = 'restaurant';
                                } elseif ($order->status === 'completed') {
                                    // 🟢 Selesai
                                    $statusBadge = 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300';
                                    $statusText = 'Selesai';
                                    $statusIcon = 'verified';
                                } elseif ($order->status === 'cancelled') {
                                    // 🔴 Dibatalkan
                                    $statusBadge = 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300';
                                    $statusText = 'Dibatalkan';
                                    $statusIcon = 'cancel';
                                } else {
                                    // Default fallback
                                    $statusBadge = 'bg-gray-100 dark:bg-gray-900/30 text-gray-700 dark:text-gray-300';
                                    $statusText = ucfirst($order->status);
                                    $statusIcon = 'info';
                                }
                            @endphp
                            <span class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 w-fit {{ $statusBadge }}">
                                <span class="material-symbols-outlined text-[14px]">{{ $statusIcon }}</span>
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col gap-0.5">
                                <span class="text-sm font-medium text-[#181411] dark:text-white">{{ $order->created_at->format('d M Y, H:i') }}</span>
                                <span class="text-xs text-[#897561] dark:text-[#a89c92]">{{ $order->created_at->diffForHumans() }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <a href="{{ route('cashier.orders.show', $order->id) }}" 
                               class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-[#3d362e] text-[#897561] hover:bg-primary hover:text-white dark:hover:bg-primary transition-all shadow-sm hover:shadow-md group-hover:scale-110"
                               title="Lihat Detail Order">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-[#3d362e] flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[32px] text-[#897561]">receipt_long</span>
                                </div>
                                <div>
                                    <p class="text-lg font-semibold text-[#181411] dark:text-white mb-1">Tidak Ada Order</p>
                                    <p class="text-sm text-[#897561]">Belum ada order yang masuk{{ request('status') ? ' dengan status ini' : '' }}.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-[#e6e0db] dark:border-[#3d362e] bg-gray-50 dark:bg-[#2c241b]">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
