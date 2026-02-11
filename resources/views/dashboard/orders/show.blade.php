@extends('layouts.dashboard')

@section('title', 'Order Details')

@section('content')
<div class="h-full flex flex-col overflow-hidden">
    <!-- Header -->
    <header class="bg-white dark:bg-card-dark border-b border-[#e6e0db] dark:border-[#3e342b] px-6 py-4 flex items-center gap-4 shrink-0">
        <a href="{{ route('dashboard.orders') }}" class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-background-light dark:hover:bg-[#3e342b] text-text-sec-light transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div class="flex flex-col">
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-bold text-text-main-light dark:text-text-main-dark">{{ $order->order_number }}</h1>
                <span class="px-2 py-1 rounded text-xs font-bold text-white
                    {{ $order->status === 'pending' ? 'bg-primary' : '' }}
                    {{ $order->status === 'processing' ? 'bg-[#eab308]' : '' }}
                    {{ $order->status === 'completed' ? 'bg-[#07880e]' : '' }}
                    {{ $order->status === 'cancelled' ? 'bg-red-500' : '' }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <p class="text-sm text-text-sec-light dark:text-text-sec-dark">
                {{ $order->created_at->format('M d, Y • h:i A') }} • {{ $order->order_type === 'dine_in' ? 'Table ' . $order->table_number : 'Takeaway' }}
            </p>
        </div>
        <div class="ml-auto flex gap-2">
            @if($order->status !== 'completed' && $order->status !== 'cancelled')
                @if($order->status === 'pending')
                <form action="{{ route('dashboard.orders.status', $order) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="processing">
                    <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-colors">
                        Start Preparing
                    </button>
                </form>
                @elseif($order->status === 'processing')
                <form action="{{ route('dashboard.orders.status', $order) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="bg-[#eab308] hover:bg-[#ca9a04] text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-colors">
                        Mark Ready
                    </button>
                </form>
                @endif
            @endif
            <button class="w-10 h-10 flex items-center justify-center rounded-lg border border-[#e6e0db] dark:border-[#3e342b] text-text-sec-light hover:text-primary hover:bg-background-light dark:hover:bg-[#3e342b] transition-colors" title="Print Receipt">
                <span class="material-symbols-outlined">print</span>
            </button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Items -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-[#e6e0db] dark:border-[#3e342b] overflow-hidden">
                    <div class="px-6 py-4 border-b border-[#e6e0db] dark:border-[#3e342b] bg-[#faf9f8] dark:bg-[#2c241b]">
                        <h3 class="font-bold text-text-main-light dark:text-text-main-dark">Items</h3>
                    </div>
                    <div class="divide-y divide-[#e6e0db] dark:divide-[#3e342b]">
                        @foreach($order->items as $item)
                        <div class="p-4 flex gap-4 items-center">
                            <div class="w-16 h-16 rounded-lg bg-cover bg-center shrink-0 bg-coffee-100" 
                                 style="background-image: url('{{ $item->menu && $item->menu->image_url ? asset('storage/' . $item->menu->image_url) : '' }}');">
                                 @if(!$item->menu || !$item->menu->image_url)
                                 <span class="flex w-full h-full items-center justify-center text-xl">☕</span>
                                 @endif
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-text-main-light dark:text-text-main-dark">{{ $item->menu->name ?? 'Deleted Item' }}</h4>
                                <p class="text-sm text-text-sec-light dark:text-text-sec-dark">Qty: {{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                @if($item->notes)
                                <p class="text-xs text-text-sec-light italic mt-1">"{{ $item->notes }}"</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-text-main-light dark:text-text-main-dark">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="p-4 bg-[#faf9f8] dark:bg-[#2c241b] border-t border-[#e6e0db] dark:border-[#3e342b] space-y-2">
                        <div class="flex justify-between text-sm text-text-sec-light dark:text-text-sec-dark">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-text-main-light dark:text-text-main-dark pt-2 border-t border-dashed border-[#e6e0db] dark:border-[#3e342b]">
                            <span>Total</span>
                            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer & Payment Info -->
            <div class="space-y-6">
                <!-- Customer Info -->
                <div class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-[#e6e0db] dark:border-[#3e342b] p-6">
                    <h3 class="font-bold text-text-main-light dark:text-text-main-dark mb-4">Customer Details</h3>
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-text-sec-light">person</span>
                            <div>
                                <p class="text-xs text-text-sec-light uppercase font-bold">Name</p>
                                <p class="text-text-main-light dark:text-text-main-dark font-medium">{{ $order->customer_name }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-text-sec-light">call</span>
                            <div>
                                <p class="text-xs text-text-sec-light uppercase font-bold">Phone</p>
                                <p class="text-text-main-light dark:text-text-main-dark font-medium">{{ $order->customer_phone ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-text-sec-light">table_restaurant</span>
                            <div>
                                <p class="text-xs text-text-sec-light uppercase font-bold">Table</p>
                                <p class="text-text-main-light dark:text-text-main-dark font-medium">{{ $order->table_number ?: 'Takeaway' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-[#e6e0db] dark:border-[#3e342b] p-6">
                    <h3 class="font-bold text-text-main-light dark:text-text-main-dark mb-4">Payment Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-text-sec-light">Method</span>
                            <span class="text-sm font-bold capitalize bg-gray-100 dark:bg-[#3e342b] px-2 py-1 rounded">
                                {{ $order->payment->method ?? 'Unpaid' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-text-sec-light">Status</span>
                            <span class="text-sm font-bold text-[#07880e] flex items-center gap-1">
                                <span class="material-symbols-outlined text-base">check_circle</span>
                                {{ ucfirst($order->payment->status ?? 'pending') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-text-sec-light">Date</span>
                            <span class="text-sm text-text-main-light dark:text-text-main-dark">
                                {{ $order->created_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
