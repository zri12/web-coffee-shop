@extends('layouts.dashboard')

@section('title', 'Order History')

@section('content')
<div class="h-full flex flex-col overflow-hidden">
    <!-- Header & Filters -->
    <header class="px-6 py-4 shrink-0 flex flex-col md:flex-row gap-4 justify-between items-center border-b border-[#e6e2de] dark:border-[#3e342b]">
        <div>
            <h1 class="text-2xl font-bold text-text-main-light dark:text-text-main-dark">Order History</h1>
            <p class="text-sm text-text-sec-light dark:text-text-sec-dark">Track and manage past orders</p>
        </div>
        
        <form action="{{ route('dashboard.orders') }}" method="GET" class="flex flex-wrap gap-2 w-full md:w-auto">
            <!-- Status Filter -->
            <div class="relative">
                <select name="status" onchange="this.form.submit()" class="appearance-none pl-10 pr-8 py-2 rounded-lg border border-[#e6e0db] dark:border-[#3e342b] bg-white dark:bg-card-dark text-sm font-medium text-text-main-light dark:text-text-main-dark focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none cursor-pointer hover:bg-gray-50 dark:hover:bg-[#2c241b] transition-colors">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-lg text-text-sec-light">filter_list</span>
                <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-lg text-text-sec-light pointer-events-none">expand_more</span>
            </div>
            
            <!-- Date Filter -->
            <div class="relative">
                <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()" class="pl-10 pr-4 py-2 rounded-lg border border-[#e6e0db] dark:border-[#3e342b] bg-white dark:bg-card-dark text-sm font-medium text-text-main-light dark:text-text-main-dark focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none cursor-pointer hover:bg-gray-50 dark:hover:bg-[#2c241b] transition-colors [&::-webkit-calendar-picker-indicator]:opacity-0 [&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:w-full">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-lg text-text-sec-light">calendar_today</span>
            </div>
            
            @if(request()->hasAny(['status', 'date']))
            <a href="{{ route('dashboard.orders') }}" class="flex items-center justify-center px-4 py-2 text-sm font-bold text-red-500 hover:text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors" title="Clear Filters">
                <span class="material-symbols-outlined text-lg mr-1">close</span>
                Reset
            </a>
            @endif
        </form>
    </header>

    <!-- Table Container -->
    <div class="flex-1 overflow-auto p-6">
        <div class="bg-white dark:bg-card-dark border border-[#e6e0db] dark:border-[#3e342b] rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#faf9f8] dark:bg-[#2c241b] border-b border-[#e6e0db] dark:border-[#3e342b]">
                        <th class="px-6 py-4 text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Total</th>
                        <th class="px-6 py-4 text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-text-sec-light dark:text-text-sec-dark uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e6e0db] dark:divide-[#3e342b]">
                    @forelse($orders as $order)
                    <tr class="group hover:bg-[#faf9f8] dark:hover:bg-[#2c241b] transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-text-main-light dark:text-text-main-dark">{{ $order->order_number }}</span>
                                <span class="text-xs text-text-sec-light dark:text-text-sec-dark flex items-center gap-1 mt-0.5">
                                    <span class="material-symbols-outlined text-[10px]">{{ $order->order_type === 'dine_in' ? 'table_restaurant' : 'shopping_bag' }}</span>
                                    {{ $order->order_type === 'dine_in' ? 'Table ' . $order->table_number : 'Takeaway' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs uppercase shrink-0">
                                    {{ substr($order->customer_name ?? 'G', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-text-main-light dark:text-text-main-dark">{{ $order->customer_name ?? 'Guest' }}</p>
                                    <p class="text-xs text-text-sec-light dark:text-text-sec-dark">{{ $order->items->count() }} Items</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-text-main-light dark:text-text-main-dark">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                            <p class="text-xs text-text-sec-light dark:text-text-sec-dark capitalize">{{ $order->payment_method ?? 'cash' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-text-main-light dark:text-text-main-dark">{{ $order->created_at->format('M d, Y') }}</p>
                            <p class="text-xs text-text-sec-light dark:text-text-sec-dark">{{ $order->created_at->format('h:i A') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border
                                {{ $order->status === 'pending' ? 'bg-primary/10 text-primary border-primary/20' : '' }}
                                {{ $order->status === 'processing' ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : '' }}
                                {{ $order->status === 'completed' ? 'bg-green-50 text-green-700 border-green-200' : '' }}
                                {{ $order->status === 'cancelled' ? 'bg-red-50 text-red-700 border-red-200' : '' }}">
                                <span class="w-1.5 h-1.5 rounded-full 
                                    {{ $order->status === 'pending' ? 'bg-primary' : '' }}
                                    {{ $order->status === 'processing' ? 'bg-yellow-500' : '' }}
                                    {{ $order->status === 'completed' ? 'bg-green-500' : '' }}
                                    {{ $order->status === 'cancelled' ? 'bg-red-500' : '' }}"></span>
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('dashboard.orders.show', $order) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[#e6e0db] dark:border-[#3e342b] text-text-sec-light hover:text-primary hover:border-primary hover:bg-white dark:hover:bg-[#3e342b] transition-all bg-transparent" title="View Details">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-text-sec-light">
                            <span class="material-symbols-outlined text-4xl mb-2 opacity-50">receipt_long</span>
                            <p class="text-base font-medium">No orders found</p>
                            <p class="text-sm">Try adjusting your filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($orders->hasPages())
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
