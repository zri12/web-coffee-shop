@extends('layouts.dashboard')

@section('title', 'Incoming Orders')

@section('content')
<div class="p-6 bg-gray-50 dark:bg-[#0d0b09] min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-2xl font-bold text-[#181411] dark:text-white mb-1">Incoming Orders</h1>
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-sm text-[#897561] dark:text-[#a89c92]">Kitchen Status: <span class="text-green-600 dark:text-green-500 font-semibold">Online</span></span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-[#897561]">{{ now()->format('M d, g:i A') }}</span>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <!-- Waiting Payment -->
        <div class="bg-white dark:bg-[#1a1612] rounded-xl p-4 border border-[#e6e0db] dark:border-[#3d362e] shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-[#897561] uppercase mb-1">Waiting Payment</p>
                    <h3 class="text-3xl font-black text-[#181411] dark:text-white">{{ $stats['waiting_payment'] }}</h3>
                </div>
                <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-full">
                    <span class="material-symbols-outlined text-orange-600 text-[28px]">payments</span>
                </div>
            </div>
        </div>

        <!-- Preparing -->
        <div class="bg-white dark:bg-[#1a1612] rounded-xl p-4 border border-[#e6e0db] dark:border-[#3d362e] shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-[#897561] uppercase mb-1">Preparing</p>
                    <h3 class="text-3xl font-black text-[#181411] dark:text-white">{{ $stats['preparing'] }}</h3>
                </div>
                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-full">
                    <span class="material-symbols-outlined text-yellow-600 text-[28px]">restaurant</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="flex gap-3 mb-5 overflow-x-auto pb-2">
        <button class="px-4 py-2 bg-orange-500 text-white rounded-lg text-sm font-bold whitespace-nowrap flex items-center gap-2 shadow-md">
            <span class="material-symbols-outlined text-[18px]">payments</span>
            Menunggu Pembayaran ({{ $waitingPaymentOrders->count() }})
        </button>
        <button class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-bold whitespace-nowrap flex items-center gap-2 shadow-md">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            Sudah Dibayar ({{ $paidOrders->count() }})
        </button>
        <button class="px-4 py-2 bg-purple-500 text-white rounded-lg text-sm font-bold whitespace-nowrap flex items-center gap-2 shadow-md">
            <span class="material-symbols-outlined text-[18px]">restaurant</span>
            Sedang Diproses ({{ $preparingOrders->count() }})
        </button>
    </div>

    <!-- Orders Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4">

        <!-- PROCESSING / PREPARING ORDERS - SHOW FIRST -->
        @foreach($preparingOrders as $order)
        <div class="bg-white dark:bg-[#1a1612] rounded-xl border-2 border-green-400 shadow-sm overflow-hidden hover:shadow-lg transition-shadow flex flex-col h-full">
            <!-- Card Header -->
            <div class="relative">
                <!-- Status Badge -->
                <div class="absolute top-3 left-3 z-10 flex flex-col gap-2">
                    <span class="px-2.5 py-1.5 bg-yellow-500 text-white text-xs font-bold rounded-lg flex items-center gap-1.5 shadow-md">
                        <span class="material-symbols-outlined text-[14px]">restaurant</span>
                        SEDANG DIPROSES
                    </span>
                </div>
                
                <!-- Time Badge -->
                <div class="absolute top-3 right-3 z-10">
                    <span class="px-2 py-1 bg-gray-900/80 text-white text-xs font-semibold rounded-md">
                        {{ $order->created_at->diffForHumans(null, true, true) }}
                    </span>
                </div>
                
                <!-- Order Image -->
                <div class="h-36 bg-gradient-to-br from-[#f9f2ec] to-[#f4ebe0] dark:from-[#2c241b] dark:to-[#1a1612] flex items-center justify-center overflow-hidden">
                    @php
                        $coverImage = optional(optional($order->items->first())->menu)->display_image_url;
                        $coverFallback = optional(optional($order->items->first())->menu)->placeholder_image_url
                            ?? route('menu.ai-image', ['menu' => 'incoming-order-' . $order->id, 'name' => 'Coffee Order']);
                    @endphp
                    <img src="{{ $coverImage ?: $coverFallback }}"
                         alt="Order Image"
                         class="h-full w-full object-cover"
                         onerror="this.onerror=null;this.src='{{ $coverFallback }}'">
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-4 flex flex-col flex-1">
                <!-- Order Number & Price -->
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="text-lg font-bold text-[#181411] dark:text-white">{{ $order->order_number }}</h3>
                        <div class="flex items-center gap-1 text-xs text-[#897561] mt-0.5">
                            @if($order->order_type === 'dine_in')
                                <span class="material-symbols-outlined text-[14px]">table_restaurant</span>
                                <span>{{ $order->table_label ?? 'Table ' . $order->table_number }}</span>
                            @else
                                <span class="material-symbols-outlined text-[14px]">shopping_bag</span>
                                <span>Takeaway</span>
                            @endif
                        </div>
                    </div>
                    <span class="text-lg font-black text-primary">Rp {{ number_format($order->display_total_amount, 0, ',', '.') }}</span>
                </div>

                <!-- Items List (full detail) -->
                <div class="space-y-2 mb-3">
                    @foreach($order->items as $item)
                        <div class="text-sm text-[#181411] dark:text-white">
                            <span class="font-semibold">{{ $item->quantity }}x</span>
                            {{ $item->menu_name ?? $item->menu->name ?? 'Menu Item' }}
                            @if($item->options_text)
                                <div class="text-xs text-[#a89c92]">{{ $item->options_text }}</div>
                            @endif
                            @if($item->notes)
                                <div class="text-xs italic text-[#a89c92]">Note: {{ $item->notes }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Action Buttons -->
                <div class="mt-auto pt-3 space-y-2">
                    <button onclick="updateStatus({{ $order->id }}, 'completed')" 
                            class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded-xl transition-all text-sm flex items-center justify-center gap-2 shadow-lg hover:shadow-xl active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[20px]">task_alt</span>
                        Tandai Siap
                    </button>
                    <button onclick="printKitchen({{ $order->id }})" 
                            class="w-full bg-gray-700 hover:bg-gray-800 text-white font-bold py-2.5 rounded-lg transition-all text-sm flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                        Print Kitchen
                    </button>
                </div>
            </div>
        </div>
        @endforeach

        <!-- PAID ORDERS (Ready to Start Preparing) -->
        @foreach($paidOrders as $order)
        <div class="bg-white dark:bg-[#1a1612] rounded-xl border-2 border-green-400 shadow-sm overflow-hidden hover:shadow-lg transition-shadow flex flex-col h-full">
            <!-- Card Header -->
            <div class="relative">
                <!-- Status Badge -->
                <div class="absolute top-3 left-3 z-10 flex flex-col gap-2">
                    <span class="px-2.5 py-1.5 bg-green-500 text-white text-xs font-bold rounded-lg flex items-center gap-1.5 shadow-md">
                        <span class="material-symbols-outlined text-[14px]">check_circle</span>
                        SUDAH DIBAYAR ({{ strtoupper($order->payment_method) }})
                    </span>
                    
                    <!-- Walk-in Badge for Manual Orders -->
                    @if($order->order_type === 'dine_in')
                        <span class="px-2 py-1 bg-purple-600 dark:bg-purple-500 text-white text-[10px] font-bold rounded-md flex items-center gap-1 shadow-sm w-fit">
                            <span class="material-symbols-outlined text-[12px]">storefront</span>
                            WALK-IN
                        </span>
                    @endif
                </div>
                
                <!-- Time Badge -->
                <div class="absolute top-3 right-3 z-10">
                    <span class="px-2 py-1 bg-gray-900/80 text-white text-xs font-semibold rounded-md">
                        {{ $order->created_at->diffForHumans(null, true, true) }}
                    </span>
                </div>
                
                <!-- Order Image -->
                <div class="h-36 bg-gradient-to-br from-[#f9f2ec] to-[#f4ebe0] dark:from-[#2c241b] dark:to-[#1a1612] flex items-center justify-center overflow-hidden">
                    @php
                        $coverImage = optional(optional($order->items->first())->menu)->display_image_url;
                        $coverFallback = optional(optional($order->items->first())->menu)->placeholder_image_url
                            ?? route('menu.ai-image', ['menu' => 'incoming-order-' . $order->id, 'name' => 'Coffee Order']);
                    @endphp
                    <img src="{{ $coverImage ?: $coverFallback }}"
                         alt="Order Image"
                         class="h-full w-full object-cover"
                         onerror="this.onerror=null;this.src='{{ $coverFallback }}'">
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-4 flex flex-col flex-1">
                <!-- Order Number & Price -->
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="text-lg font-bold text-[#181411] dark:text-white">{{ $order->order_number }}</h3>
                        <div class="flex items-center gap-1 text-xs text-[#897561] mt-0.5">
                            @if($order->order_type === 'dine_in')
                                <span class="material-symbols-outlined text-[14px]">table_restaurant</span>
                                <span>{{ $order->table_label ?? 'Table ' . $order->table_number }}</span>
                            @else
                                <span class="material-symbols-outlined text-[14px]">shopping_bag</span>
                                <span>Takeaway</span>
                            @endif
                        </div>
                    </div>
                    <span class="text-lg font-black text-primary">Rp {{ number_format($order->display_total_amount, 0, ',', '.') }}</span>
                </div>

                <!-- Items List (full detail) -->
                <div class="space-y-2 mb-3">
                    @foreach($order->items as $item)
                        <div class="text-sm text-[#181411] dark:text-white">
                            <span class="font-semibold">{{ $item->quantity }}x</span>
                            {{ $item->menu_name ?? $item->menu->name ?? 'Menu Item' }}
                            @if($item->options_text)
                                <div class="text-xs text-[#a89c92]">{{ $item->options_text }}</div>
                            @endif
                            @if($item->notes)
                                <div class="text-xs italic text-[#a89c92]">Note: {{ $item->notes }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Action Buttons -->
                <div class="mt-auto pt-3 space-y-2">
                    <button onclick="updateStatus({{ $order->id }}, 'processing')" 
                            class="w-full bg-primary hover:bg-[#A05E35] text-white font-bold py-3 rounded-xl transition-all text-sm flex items-center justify-center gap-2 shadow-lg hover:shadow-xl active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[20px]">restaurant</span>
                        Start Preparing
                    </button>
                    <button onclick="printKitchen({{ $order->id }})" 
                            class="w-full bg-gray-700 hover:bg-gray-800 text-white font-bold py-2.5 rounded-lg transition-all text-sm flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                        Print Kitchen
                    </button>
                </div>
            </div>
        </div>
        @endforeach

        <!-- WAITING PAYMENT ORDERS -->
        @foreach($waitingPaymentOrders as $order)
        <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm overflow-hidden hover:shadow-lg transition-shadow flex flex-col h-full">
            <!-- Card Header -->
            <div class="relative">
                <!-- Payment Status Badge -->
                <div class="absolute top-3 left-3 z-10 flex flex-col gap-2">
                    @if($order->payment_method === 'qris')
                        <span class="px-2.5 py-1.5 bg-orange-500 text-white text-xs font-bold rounded-lg flex items-center gap-1.5 shadow-md animate-pulse">
                            <span class="material-symbols-outlined text-[14px]">qr_code_2</span>
                            MENUNGGU PEMBAYARAN QRIS
                        </span>
                    @else
                        <span class="px-2.5 py-1.5 bg-yellow-500 text-white text-xs font-bold rounded-lg flex items-center gap-1.5 shadow-md">
                            <span class="material-symbols-outlined text-[14px]">warning</span>
                            PERLU KONFIRMASI PEMBAYARAN
                        </span>
                    @endif
                    
                    <!-- Walk-in Badge for Manual Orders -->
                    @if($order->payment_method === 'cash')
                        <span class="px-2 py-1 bg-purple-600 dark:bg-purple-500 text-white text-[10px] font-bold rounded-md flex items-center gap-1 shadow-sm w-fit">
                            <span class="material-symbols-outlined text-[12px]">storefront</span>
                            WALK-IN
                        </span>
                    @endif
                </div>
                
                <!-- Time Badge -->
                <div class="absolute top-3 right-3 z-10">
                    <span class="px-2 py-1 bg-gray-900/80 text-white text-xs font-semibold rounded-md">
                        {{ $order->created_at->diffForHumans(null, true, true) }}
                    </span>
                </div>
                
                <!-- Order Image -->
                <div class="h-36 bg-gradient-to-br from-[#f9f2ec] to-[#f4ebe0] dark:from-[#2c241b] dark:to-[#1a1612] flex items-center justify-center overflow-hidden">
                    @php
                        $coverImage = optional(optional($order->items->first())->menu)->display_image_url;
                        $coverFallback = optional(optional($order->items->first())->menu)->placeholder_image_url
                            ?? route('menu.ai-image', ['menu' => 'incoming-order-' . $order->id, 'name' => 'Coffee Order']);
                    @endphp
                    <img src="{{ $coverImage ?: $coverFallback }}"
                         alt="Order Image"
                         class="h-full w-full object-cover"
                         onerror="this.onerror=null;this.src='{{ $coverFallback }}'">
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-4 flex flex-col flex-1">
                <!-- Order Number & Price -->
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="text-lg font-bold text-[#181411] dark:text-white">{{ $order->order_number }}</h3>
                        <div class="flex items-center gap-1 text-xs text-[#897561] mt-0.5">
                            @if($order->order_type === 'dine_in')
                                <span class="material-symbols-outlined text-[14px]">table_restaurant</span>
                                <span>{{ $order->table_label ?? 'Table ' . $order->table_number }}</span>
                            @else
                                <span class="material-symbols-outlined text-[14px]">shopping_bag</span>
                                <span>Takeaway</span>
                            @endif
                        </div>
                    </div>
                    <span class="text-lg font-black text-primary">Rp {{ number_format($order->display_total_amount, 0, ',', '.') }}</span>
                </div>

                <!-- Items List (full detail) -->
                <div class="space-y-2 mb-3">
                    @foreach($order->items as $item)
                        <div class="text-sm text-[#181411] dark:text-white">
                            <span class="font-semibold">{{ $item->quantity }}x</span>
                            {{ $item->menu_name ?? $item->menu->name ?? 'Menu Item' }}
                            @if($item->options_text)
                                <div class="text-xs text-[#a89c92]">{{ $item->options_text }}</div>
                            @endif
                            @if($item->notes)
                                <div class="text-xs italic text-[#a89c92]">Note: {{ $item->notes }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($order->payment_method === 'qris')
                <p class="text-xs font-semibold text-orange-600 mb-2">Belum Dibayar</p>
                @endif

                <!-- Action Buttons - Push to bottom -->
                <div class="mt-auto pt-3 space-y-2">
                    @if($order->payment_method === 'cash')
                        <button onclick="confirmCashPayment({{ $order->id }})" 
                                class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-xl transition-all text-sm flex items-center justify-center gap-2 shadow-lg hover:shadow-xl active:scale-[0.98]">
                            <span class="material-symbols-outlined text-[20px]">check_circle</span>
                            Konfirmasi Pembayaran Walk-in
                        </button>
                    @else
                        <!-- QRIS waiting: no start preparing until payment callback success -->
                        <div class="w-full bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 font-semibold py-3 rounded-xl text-sm flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[20px] animate-pulse">schedule</span>
                            Menunggu Pembayaran QRIS
                        </div>
                        <button onclick="printBill({{ $order->id }})" 
                                class="w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-2.5 rounded-lg transition-all text-sm flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">receipt</span>
                            Print Bill
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach

        <!-- PREPARING ORDERS -->
        @foreach($preparingOrders as $order)
        <div class="bg-white dark:bg-[#1a1612] rounded-xl border-2 border-yellow-400 shadow-sm overflow-hidden hover:shadow-lg transition-shadow flex flex-col h-full">
            <!-- Card Header -->
            <div class="relative">
                <!-- Status Badge -->
                <div class="absolute top-3 left-3 z-10 flex flex-col gap-2">
                    <span class="px-2.5 py-1.5 bg-yellow-500 text-white text-xs font-bold rounded-lg flex items-center gap-1.5 shadow-md animate-pulse">
                        <span class="material-symbols-outlined text-[14px]">restaurant</span>
                        SEDANG DIPROSES
                    </span>
                    
                    <!-- Walk-in Badge for Manual Orders -->
                    @if($order->order_type === 'dine_in')
                        <span class="px-2 py-1 bg-purple-600 dark:bg-purple-500 text-white text-[10px] font-bold rounded-md flex items-center gap-1 shadow-sm w-fit">
                            <span class="material-symbols-outlined text-[12px]">storefront</span>
                            WALK-IN
                        </span>
                    @endif
                </div>
                
                <!-- Time Badge -->
                <div class="absolute top-3 right-3 z-10">
                    <span class="px-2 py-1 bg-gray-900/80 text-white text-xs font-semibold rounded-md">
                        {{ $order->created_at->diffForHumans(null, true, true) }}
                    </span>
                </div>
                
                <!-- Order Image -->
                <div class="h-36 bg-gradient-to-br from-[#f9f2ec] to-[#f4ebe0] dark:from-[#2c241b] dark:to-[#1a1612] flex items-center justify-center overflow-hidden">
                    @php
                        $coverImage = optional(optional($order->items->first())->menu)->display_image_url;
                        $coverFallback = optional(optional($order->items->first())->menu)->placeholder_image_url
                            ?? route('menu.ai-image', ['menu' => 'incoming-order-' . $order->id, 'name' => 'Coffee Order']);
                    @endphp
                    <img src="{{ $coverImage ?: $coverFallback }}"
                         alt="Order Image"
                         class="h-full w-full object-cover"
                         onerror="this.onerror=null;this.src='{{ $coverFallback }}'">
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-4 flex flex-col flex-1">
                <!-- Order Number & Price -->
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="text-lg font-bold text-[#181411] dark:text-white">{{ $order->order_number }}</h3>
                        <div class="flex items-center gap-1 text-xs text-[#897561] mt-0.5">
                            @if($order->order_type === 'dine_in')
                                <span class="material-symbols-outlined text-[14px]">table_restaurant</span>
                                <span>{{ $order->table_label ?? 'Table ' . $order->table_number }}</span>
                            @else
                                <span class="material-symbols-outlined text-[14px]">shopping_bag</span>
                                <span>Takeaway</span>
                            @endif
                        </div>
                    </div>
                    <span class="text-lg font-black text-primary">Rp {{ number_format($order->display_total_amount, 0, ',', '.') }}</span>
                </div>

                <!-- Items List (full detail) -->
                <div class="space-y-2 mb-3">
                    @foreach($order->items as $item)
                        <div class="text-sm text-[#181411] dark:text-white">
                            <span class="font-semibold">{{ $item->quantity }}x</span>
                            {{ $item->menu_name ?? $item->menu->name ?? 'Menu Item' }}
                            @if($item->options_text)
                                <div class="text-xs text-[#a89c92]">{{ $item->options_text }}</div>
                            @endif
                            @if($item->notes)
                                <div class="text-xs italic text-[#a89c92]">Note: {{ $item->notes }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Action Button -->
                <div class="mt-auto pt-3">
                    <button onclick="updateStatus({{ $order->id }}, 'completed')" 
                            class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded-xl transition-all text-sm flex items-center justify-center gap-2 shadow-lg hover:shadow-xl active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[20px]">task_alt</span>
                        Tandai Siap
                    </button>
                </div>
            </div>
        </div>
        @endforeach

        @if($waitingPaymentOrders->isEmpty() && $paidOrders->isEmpty() && $preparingOrders->isEmpty())
        <div class="col-span-full text-center py-12">
            <span class="material-symbols-outlined text-[64px] text-[#897561] mb-3">coffee</span>
            <p class="text-[#897561] text-lg">Belum ada order masuk</p>
        </div>
        @endif
    </div>
</div>

<script>
// Confirm Cash Payment
async function confirmCashPayment(orderId) {
    if (!confirm('Konfirmasi pembayaran tunai untuk order ini?')) {
        return;
    }

    try {
        const response = await fetch(`/cashier/orders/${orderId}/confirm-payment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.success) {
            alert('Pembayaran tunai berhasil dikonfirmasi!');
            location.reload();
        } else {
            alert('Gagal: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat konfirmasi pembayaran');
    }
}

// Update Order Status
async function updateStatus(orderId, newStatus) {
    try {
        const response = await fetch(`/cashier/orders/${orderId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: newStatus })
        });

        const data = await response.json();

        if (data.success) {
            location.reload();
        } else {
            alert('Gagal: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    }
}

// Print Bill (Receipt for customer)
function printBill(orderId) {
    window.open(`/cashier/orders/${orderId}`, '_blank');
}

// Print Kitchen (for kitchen staff)
function printKitchen(orderId) {
    window.open(`/cashier/orders/${orderId}/print-kitchen`, '_blank');
}

// Auto refresh every 30 seconds
setInterval(() => {
    location.reload();
}, 30000);
</script>
@endsection
