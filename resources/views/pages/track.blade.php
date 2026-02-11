@extends('layouts.app')

@section('title', 'Lacak Pesanan')

@section('content')
<section class="bg-surface-light dark:bg-surface-dark py-12 lg:py-16 border-b border-[#f4f2f0] dark:border-[#3E2723]">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl lg:text-4xl font-bold text-text-main dark:text-white mb-4">Lacak Pesanan</h1>
        <p class="text-text-subtle dark:text-gray-400 max-w-2xl mx-auto">Masukkan nomor pesanan untuk melihat status pesanan Anda.</p>
    </div>
</section>

<section class="py-12 lg:py-16">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Search Form -->
        <form action="{{ route('track.search') }}" method="POST" class="mb-12">
            @csrf
            <div class="flex gap-4">
                <div class="relative flex-1">
                    <input type="text" name="order_number" 
                           value="{{ request('order_number') }}" 
                           placeholder="Contoh: ORD-XXXXXXXX" 
                           class="w-full pl-12 pr-4 py-3 bg-background-light dark:bg-background-dark border border-[#e6e0db] dark:border-[#3E2723] rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all font-mono text-lg"
                           required>
                    <span class="material-symbols-outlined text-text-subtle dark:text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 text-[24px]">search</span>
                </div>
                <button type="submit" class="flex items-center justify-center px-8 rounded-xl bg-primary hover:bg-primary-dark text-white text-lg font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                    Cari
                </button>
            </div>
        </form>
        
        @isset($order)
        <!-- Order Found -->
        <div class="bg-white dark:bg-surface-dark border border-[#f4f2f0] dark:border-[#3E2723] rounded-2xl overflow-hidden shadow-sm animate-slide-up">
            <!-- Status Header -->
            <div class="p-6 {{ $order->status == 'completed' ? 'bg-green-500' : ($order->status == 'cancelled' ? 'bg-red-500' : 'bg-primary') }} text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80 mb-1">Nomor Pesanan</p>
                        <p class="text-2xl font-bold font-mono tracking-wider">{{ $order->order_number }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm opacity-80 mb-1">Status</p>
                        <p class="text-xl font-bold flex items-center gap-2 justify-end">
                            @if($order->status == 'completed')
                                <span class="material-symbols-outlined">check_circle</span>
                            @elseif($order->status == 'cancelled')
                                <span class="material-symbols-outlined">cancel</span>
                            @else
                                <span class="material-symbols-outlined">schedule</span>
                            @endif
                            {{ $order->status_label }}
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Status Timeline -->
            <div class="p-8 border-b border-[#f4f2f0] dark:border-[#3E2723]">
                <div class="flex justify-between relative">
                    @php
                        $statuses = ['pending' => 'Menunggu', 'processing' => 'Diproses', 'ready' => 'Siap', 'completed' => 'Selesai'];
                        $currentIndex = array_search($order->status, array_keys($statuses));
                        if ($order->status == 'cancelled') $currentIndex = -1;
                    @endphp
                    
                    @foreach($statuses as $key => $label)
                    @php $index = array_search($key, array_keys($statuses)); @endphp
                    <div class="flex flex-col items-center relative z-10 w-1/4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center border-4 transition-colors duration-500 {{ $index <= $currentIndex ? 'bg-primary border-primary text-white' : 'bg-background-light dark:bg-background-dark border-[#e6e0db] dark:border-[#3E2723] text-text-subtle dark:text-gray-600' }}">
                            @if($index < $currentIndex)
                            <span class="material-symbols-outlined text-[20px]">check</span>
                            @else
                            <span class="text-sm font-bold">{{ $index + 1 }}</span>
                            @endif
                        </div>
                        <p class="text-xs sm:text-sm mt-3 font-bold text-center {{ $index <= $currentIndex ? 'text-primary' : 'text-text-subtle dark:text-gray-600' }}">{{ $label }}</p>
                    </div>
                    @endforeach
                    
                    <!-- Progress Line -->
                    <div class="absolute top-6 left-[12.5%] right-[12.5%] h-1 bg-[#e6e0db] dark:bg-[#3E2723] -z-0 rounded-full">
                        <div class="h-full bg-primary transition-all duration-700 rounded-full" 
                             style="width: {{ $currentIndex >= 0 ? (($currentIndex) / 3 * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Order Details -->
            <div class="p-6">
                <h3 class="font-bold text-text-main dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">receipt_long</span>
                    Detail Pesanan
                </h3>
                <div class="space-y-4 mb-6">
                    @foreach($order->items as $item)
                    <div class="flex justify-between items-center py-3 border-b border-[#f4f2f0] dark:border-[#3E2723] last:border-0">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-background-light dark:bg-background-dark rounded-lg flex items-center justify-center text-text-subtle">
                                <span class="material-symbols-outlined text-[20px]">coffee</span>
                            </div>
                            <div>
                                <p class="font-bold text-text-main dark:text-white">{{ $item->menu_name }}</p>
                                <p class="text-sm text-text-subtle dark:text-gray-400">{{ $item->quantity }}x</p>
                            </div>
                        </div>
                        <span class="font-bold text-text-main dark:text-white">{{ 'Rp ' . number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                
                <div class="flex justify-between items-center pt-4 border-t-2 border-[#f4f2f0] dark:border-[#3E2723]">
                    <span class="text-lg font-bold text-text-main dark:text-white">Total</span>
                    <span class="text-2xl font-black text-primary">{{ $order->formatted_total }}</span>
                </div>
                
                @if($order->notes)
                <div class="mt-6 p-4 bg-background-light dark:bg-background-dark rounded-xl border border-[#e6e0db] dark:border-[#3E2723]">
                    <p class="text-sm text-text-subtle dark:text-gray-400">
                        <span class="font-bold text-text-main dark:text-white block mb-1">Catatan Tambahan:</span>
                        {{ $order->notes }}
                    </p>
                </div>
                @endif
            </div>
        </div>
        @endisset
    </div>
</section>

@push('styles')
<style>
@keyframes slide-up {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-slide-up { animation: slide-up 0.5s ease-out; }
</style>
@endpush
@endsection
