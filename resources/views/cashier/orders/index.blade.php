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
            <select class="px-3 py-2 bg-white dark:bg-[#1a1612] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50" onchange="window.location.href='?status='+this.value">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-[#2c241b] border-b border-[#e6e0db] dark:border-[#3d362e]">
                    <tr>
                        <th class="px-6 py-4 font-bold text-[#181411] dark:text-white">Order ID</th>
                        <th class="px-6 py-4 font-bold text-[#181411] dark:text-white">Customer</th>
                        <th class="px-6 py-4 font-bold text-[#181411] dark:text-white">Items</th>
                        <th class="px-6 py-4 font-bold text-[#181411] dark:text-white">Total</th>
                        <th class="px-6 py-4 font-bold text-[#181411] dark:text-white">Status</th>
                        <th class="px-6 py-4 font-bold text-[#181411] dark:text-white">Date</th>
                        <th class="px-6 py-4 font-bold text-[#181411] dark:text-white text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e6e0db] dark:divide-[#3d362e]">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 dark:hover:bg-[#2c241b]/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-[#181411] dark:text-white">#{{ $order->order_number }}</td>
                        <td class="px-6 py-4 text-[#5c4d40] dark:text-[#a89c92]">
                            {{ $order->customer_name }}
                            @if($order->table_number && $order->order_type === 'dine_in')
                                <span class="text-xs bg-gray-100 dark:bg-[#3d362e] px-1.5 py-0.5 rounded ml-1">Table {{ $order->table_number }}</span>
                            @else
                                <span class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-600 px-1.5 py-0.5 rounded ml-1">Takeaway</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-[#5c4d40] dark:text-[#a89c92]">
                            {{ $order->items_count }} items
                        </td>
                        <td class="px-6 py-4 font-bold text-[#181411] dark:text-white">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs font-bold
                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $order->status === 'pending' ? 'bg-orange-100 text-orange-700' : '' }}
                                {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                            ">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-[#897561] text-xs">
                            {{ $order->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('cashier.orders.show', $order->id) }}" class="text-[#897561] hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-[#897561]">
                            No orders found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="p-4 border-t border-[#e6e0db] dark:border-[#3d362e]">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
