@extends('layouts.dashboard')

@section('title', 'Incoming Orders')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header & Stats -->
    <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#181411] dark:text-white">Incoming Orders</h1>
            <p class="text-[#897561] text-sm">Manage pending and preparing orders.</p>
        </div>
        
        <!-- Kitchen Status Toggle (Visual Only for now) -->
        <div class="flex items-center gap-3 bg-white dark:bg-[#1a1612] px-4 py-2 rounded-full border border-[#e6e0db] dark:border-[#3d362e] shadow-sm">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
            <span class="text-sm font-medium text-[#181411] dark:text-white">Kitchen Status: <span class="text-green-600">Online</span></span>
            <button class="ml-2 text-xs bg-[#f4f2f0] dark:bg-[#2c241b] px-2 py-1 rounded hover:bg-[#e6e0db] dark:hover:bg-[#3d362e] transition">Pause</button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Pending -->
        <div class="bg-white dark:bg-[#1a1612] p-4 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] flex items-center justify-between">
            <div>
                <p class="text-[#897561] text-xs font-bold tracking-wider uppercase">Pending</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-3xl font-bold text-[#181411] dark:text-white">{{ $stats['pending'] }}</h3>
                    <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">Active</span>
                </div>
            </div>
            <div class="text-orange-500 bg-orange-50 dark:bg-orange-900/20 p-2 rounded-lg">
                <span class="material-symbols-outlined">hourglass_top</span>
            </div>
        </div>

        <!-- Preparing -->
        <div class="bg-white dark:bg-[#1a1612] p-4 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] flex items-center justify-between">
            <div>
                <p class="text-[#897561] text-xs font-bold tracking-wider uppercase">Preparing</p>
                <h3 class="text-3xl font-bold text-[#181411] dark:text-white mt-1">{{ $stats['preparing'] }}</h3>
            </div>
            <div class="text-blue-500 bg-blue-50 dark:bg-blue-900/20 p-2 rounded-lg">
                <span class="material-symbols-outlined">skillet</span>
            </div>
        </div>

        <!-- Completed Today -->
        <div class="bg-white dark:bg-[#1a1612] p-4 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] flex items-center justify-between">
            <div>
                <p class="text-[#897561] text-xs font-bold tracking-wider uppercase">Completed Today</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-3xl font-bold text-[#181411] dark:text-white">{{ $stats['completed_today'] }}</h3>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">+12%</span>
                </div>
            </div>
            <div class="text-green-500 bg-green-50 dark:bg-green-900/20 p-2 rounded-lg">
                <span class="material-symbols-outlined">check_circle</span>
            </div>
        </div>
    </div>

    <!-- Active Orders Grid -->
    <div>
        @if($activeOrders->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($activeOrders as $order)
            <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] overflow-hidden flex flex-col h-full shadow-sm hover:shadow-md transition-shadow">
                <!-- Card Header -->
                <div class="p-4 border-b border-[#e6e0db] dark:border-[#3d362e] flex justify-between items-start bg-gray-50 dark:bg-[#2c241b]/50">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2 py-1 text-xs font-bold rounded {{ $order->status === 'pending' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                            <span class="text-xs text-[#897561] flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">schedule</span>
                                {{ $order->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <h4 class="font-bold text-lg text-[#181411] dark:text-white">#{{ $order->order_number }}</h4>
                        <div class="flex items-center gap-2 text-sm text-[#5c4d40] dark:text-[#a89c92] mt-0.5">
                            <span class="material-symbols-outlined text-[16px]">table_bar</span>
                            @if(str_contains(strtolower($order->order_items ?? ''), 'table'))
                                Table {{ preg_replace('/[^0-9]/', '', $order->order_items) }}
                            @else
                                {{ $order->customer_name ?? 'Walk-in' }}
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-lg text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        <p class="text-xs text-[#897561]">{{ $order->payment_method ? ucfirst($order->payment_method) : 'Unpaid' }}</p>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="p-4 flex-1">
                    <ul class="space-y-3">
                        @foreach($order->items as $item)
                        <li class="flex justify-between items-start text-sm">
                            <div class="flex gap-2">
                                <span class="font-bold text-[#181411] dark:text-white">{{ $item->quantity }}x</span>
                                <span class="text-[#5c4d40] dark:text-[#a89c92]">{{ $item->menu->name }}</span>
                            </div>
                            <span class="text-[#897561] text-xs">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @if($order->notes)
                        <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/10 rounded-lg text-xs text-yellow-800 dark:text-yellow-200 border border-yellow-100 dark:border-yellow-900/20">
                            <span class="font-bold">Note:</span> {{ $order->notes }}
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="p-4 border-t border-[#e6e0db] dark:border-[#3d362e] gap-2 grid grid-cols-1">
                    @if($order->status === 'pending')
                    <form action="{{ route('cashier.orders.status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="processing">
                        <button type="submit" class="w-full py-2.5 px-4 bg-primary hover:bg-primary-dark text-white rounded-lg font-medium transition flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">skillet</span>
                            Start Preparing
                        </button>
                    </form>
                    @elseif($order->status === 'processing')
                    <form action="{{ route('cashier.orders.status', $order) }}" method="POST" class="grid grid-cols-2 gap-2">
                        @csrf
                        @method('PATCH')
                        
                        <!-- Print Button (Visual) -->
                        <button type="button" onclick="window.print()" class="py-2.5 px-4 bg-white dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] text-[#5c4d40] dark:text-[#a89c92] rounded-lg font-medium transition flex items-center justify-center gap-2 hover:bg-gray-50 dark:hover:bg-[#3d362e]/50">
                            <span class="material-symbols-outlined">print</span>
                        </button>

                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="py-2.5 px-4 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">check</span>
                            Complete
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] p-12 text-center">
            <div class="bg-gray-50 dark:bg-[#2c241b] w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-3xl text-gray-400">checklist</span>
            </div>
            <h3 class="text-lg font-bold text-[#181411] dark:text-white mb-1">No Active Orders</h3>
            <p class="text-[#897561]">Great job! All incoming orders have been processed.</p>
        </div>
        @endif
    </div>
</div>
@endsection
