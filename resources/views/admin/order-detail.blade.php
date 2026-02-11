@extends('layouts.dashboard')

@section('title', 'Order Details')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#181411] dark:text-white">Order #{{ $order->order_number }}</h1>
            <p class="text-[#897561] text-sm">{{ $order->created_at->format('M d, Y H:i') }}</p>
        </div>
        <a href="{{ route('admin.orders') }}" class="px-4 py-2 border border-[#e6e0db] dark:border-[#3d362e] text-[#897561] rounded-lg hover:bg-gray-50 dark:hover:bg-[#2c241b] transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined">arrow_back</span>
            Back to Orders
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm overflow-hidden">
                <div class="p-6 border-b border-[#e6e0db] dark:border-[#3d362e]">
                    <h2 class="text-lg font-bold text-[#181411] dark:text-white">Order Items</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 dark:bg-[#2c241b] border-b border-[#e6e0db] dark:border-[#3d362e]">
                            <tr>
                                <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs">Item</th>
                                <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs text-right">Price</th>
                                <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs text-right">Qty</th>
                                <th class="px-6 py-4 font-bold text-[#897561] uppercase text-xs text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e6e0db] dark:divide-[#3d362e]">
                            @foreach($order->items as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#2c241b] transition-colors">
                                <td class="px-6 py-4 text-[#181411] dark:text-white font-medium">
                                    {{ $item->menu_name ?? $item->menu->name ?? 'Unknown Item' }}
                                </td>
                                <td class="px-6 py-4 text-right text-[#897561] dark:text-[#a89c92]">
                                    Rp {{ number_format($item->price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-[#181411] dark:text-white">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-[#181411] dark:text-white">
                                    Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-6 border-t border-[#e6e0db] dark:border-[#3d362e] bg-gray-50 dark:bg-[#2c241b]">
                    <div class="flex justify-end">
                        <div class="w-full max-w-xs space-y-2">
                            <div class="flex justify-between text-[#897561] dark:text-[#a89c92]">
                                <span>Subtotal:</span>
                                <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between font-bold text-[#181411] dark:text-white text-lg pt-2 border-t border-[#e6e0db] dark:border-[#3d362e]">
                                <span>Total:</span>
                                <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm p-6">
                <h2 class="text-lg font-bold text-[#181411] dark:text-white mb-4">Customer Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-[#897561] uppercase tracking-wider">Name</label>
                        <p class="text-[#181411] dark:text-white mt-1">{{ $order->customer_name ?? 'Guest' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-[#897561] uppercase tracking-wider">Phone</label>
                        <p class="text-[#181411] dark:text-white mt-1">{{ $order->customer_phone ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-[#897561] uppercase tracking-wider">Order Type</label>
                        <p class="text-[#181411] dark:text-white mt-1 capitalize">{{ $order->order_type ?? 'Dine In' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-[#897561] uppercase tracking-wider">Table Number</label>
                        <p class="text-[#181411] dark:text-white mt-1">{{ $order->table_number ?? '-' }}</p>
                    </div>
                </div>
                @if($order->notes)
                <div class="mt-4 pt-4 border-t border-[#e6e0db] dark:border-[#3d362e]">
                    <label class="text-xs font-bold text-[#897561] uppercase tracking-wider">Notes</label>
                    <p class="text-[#181411] dark:text-white mt-1">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Order Status -->
            <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm p-6">
                <h2 class="text-lg font-bold text-[#181411] dark:text-white mb-4">Order Status</h2>
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400',
                        'processing' => 'bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400',
                        'completed' => 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400',
                        'cancelled' => 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400',
                    ];
                @endphp
                <div class="mb-4">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold uppercase {{ $statusColors[$order->status] ?? '' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-[#897561] dark:text-[#a89c92]">Status Updated:</span>
                        <span class="text-[#181411] dark:text-white">{{ $order->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Status -->
            <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm p-6">
                <h2 class="text-lg font-bold text-[#181411] dark:text-white mb-4">Payment Status</h2>
                @if($order->payment_status == 'paid')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold uppercase bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400">
                        Paid
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold uppercase bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400">
                        Unpaid
                    </span>
                @endif
                <div class="space-y-2 text-sm mt-4">
                    <div class="flex justify-between">
                        <span class="text-[#897561] dark:text-[#a89c92]">Payment Method:</span>
                        <span class="text-[#181411] dark:text-white capitalize">{{ $order->payment_method ?? 'Cash' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#897561] dark:text-[#a89c92]">Total Amount:</span>
                        <span class="text-[#181411] dark:text-white font-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Cashier Info -->
            @if($order->user)
            <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm p-6">
                <h2 class="text-lg font-bold text-[#181411] dark:text-white mb-4">Cashier</h2>
                <div class="space-y-2 text-sm">
                    <div>
                        <span class="text-[#897561] dark:text-[#a89c92]">Name:</span>
                        <p class="text-[#181411] dark:text-white">{{ $order->user->name }}</p>
                    </div>
                    <div>
                        <span class="text-[#897561] dark:text-[#a89c92]">Email:</span>
                        <p class="text-[#181411] dark:text-white text-xs break-all">{{ $order->user->email }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
