@extends('layouts.app')

@section('title', 'Menunggu Pembayaran')

@section('content')
<section class="min-h-[100dvh] bg-[#F9F9F9] flex items-start justify-center px-4 py-6">
    <div class="w-full max-w-md mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden animate-slide-up">
            <!-- Live Status Badge -->
            <div class="px-4 md:px-6 pt-3 md:pt-4 flex justify-end">
                <div class="flex items-center gap-1.5 px-2.5 py-1 bg-yellow-50 border border-yellow-200 rounded-full">
                    <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full animate-pulse"></span>
                    <span class="text-[10px] md:text-xs font-medium text-yellow-700">Menunggu konfirmasi pembayaran</span>
                </div>
            </div>
            
            <!-- Pending Payment Icon & Title -->
            <div class="text-center pt-4 md:pt-6 pb-3 md:pb-4 px-4 md:px-6">
                <!-- Yellow Clock Circle -->
                <div class="inline-flex items-center justify-center w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-full mb-3 md:mb-4 shadow-lg animate-pulse">
                    <div class="bg-white rounded-full p-1.5 md:p-2">
                        <span class="material-symbols-outlined text-3xl md:text-4xl text-yellow-500">schedule</span>
                    </div>
                </div>
                
                <h1 class="text-xl md:text-2xl font-bold text-[#2F2D2C] mb-1.5 md:mb-2">Pesanan Diterima</h1>
                <p class="text-[#9B9B9B] text-xs md:text-sm">Silakan lakukan pembayaran di kasir</p>
            </div>

            <!-- Pickup Number Box -->
            <div class="mx-4 md:mx-6 mb-4 md:mb-5">
                <div class="bg-gradient-to-br from-[#FFF5F0] to-[#FFE8D9] rounded-xl p-4 md:p-6 border-2 border-[#C67C4E]/20 shadow-sm">
                    <p class="text-[10px] md:text-xs font-bold text-[#C67C4E] mb-1.5 md:mb-2 uppercase tracking-wider text-center">Nomor Pesanan</p>
                    <p class="text-3xl md:text-4xl font-black text-[#2F2D2C] text-center mb-2 md:mb-3 tracking-tight">{{ $order->order_number }}</p>
                    <div class="flex items-center justify-center gap-1.5 text-[#9B9B9B]">
                        <span class="material-symbols-outlined text-[18px] md:text-[20px]">payments</span>
                        <span class="text-xs md:text-sm font-medium">Tunai - Bayar di Kasir</span>
                    </div>
                </div>
            </div>

            <!-- Important Notice -->
            <div class="mx-4 md:mx-6 mb-4 md:mb-5">
                <div class="bg-yellow-50 rounded-lg p-4 md:p-5 border-2 border-yellow-200">
                    <div class="flex items-start gap-2 md:gap-3 mb-2.5">
                        <span class="material-symbols-outlined text-yellow-600 flex-shrink-0 text-lg md:text-2xl">info</span>
                        <div>
                            <h3 class="font-bold text-yellow-900 text-sm md:text-base mb-1.5">Petunjuk Pembayaran</h3>
                            <div class="space-y-1 text-xs md:text-sm text-yellow-800">
                                <p class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[18px] mt-0.5">arrow_right</span>
                                    <span>Silakan menuju kasir untuk melakukan pembayaran</span>
                                </p>
                                <p class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[18px] mt-0.5">arrow_right</span>
                                    <span>Tunjukkan <strong>nomor pesanan</strong> di atas kepada kasir</span>
                                </p>
                                <p class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[18px] mt-0.5">arrow_right</span>
                                    <span>Pesanan akan mulai disiapkan setelah pembayaran dikonfirmasi</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="px-4 md:px-6 pb-4 md:pb-5">
                <h3 class="text-sm md:text-base font-bold text-[#2F2D2C] mb-3 md:mb-4 flex items-center gap-1.5 md:gap-2">
                    <span class="material-symbols-outlined text-[#C67C4E] text-xl md:text-2xl">receipt_long</span>
                    Rincian Pesanan
                </h3>

                <!-- Items List -->
                <div class="space-y-3 md:space-y-4 mb-4 md:mb-5">
                    @foreach($order->items as $item)
                    <div class="flex items-start gap-3 md:gap-4">
                        <!-- Item Image -->
                        <div class="w-14 h-14 md:w-16 md:h-16 bg-[#F9F9F9] rounded-xl flex-shrink-0 flex items-center justify-center overflow-hidden">
                            @if($item->menu)
                                <img src="{{ $item->menu->display_image_url }}" alt="{{ $item->menu_name }}" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='{{ $item->menu->placeholder_image_url }}'">
                            @else
                                <span class="material-symbols-outlined text-[#E0E0E0] text-2xl md:text-3xl">local_cafe</span>
                            @endif
                        </div>

                        <!-- Item Details -->
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-[#2F2D2C] text-sm md:text-base mb-0.5 md:mb-1">{{ $item->menu_name ?? $item->menu->name ?? 'Menu Item' }}</h4>
                            <p class="text-xs md:text-sm text-[#9B9B9B]">{{ $item->notes ?? 'Regular' }}</p>
                        </div>

                        <!-- Price -->
                        <div class="text-right flex-shrink-0">
                            <p class="font-bold text-[#2F2D2C] text-sm md:text-base">{{ 'Rp ' . number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Price Summary -->
                <div class="space-y-2 md:space-y-3 pt-4 md:pt-5 border-t-2 border-dashed border-[#E8E8E8]">
                    @php
                        $subtotal = $order->total_amount / 1.08;
                        $tax = $order->total_amount - $subtotal;
                    @endphp
                    
                    <div class="flex justify-between text-sm md:text-base">
                        <span class="text-[#9B9B9B]">Subtotal</span>
                        <span class="font-semibold text-[#2F2D2C]">{{ 'Rp ' . number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-between text-sm md:text-base">
                        <span class="text-[#9B9B9B]">Tax (8%)</span>
                        <span class="font-semibold text-[#2F2D2C]">{{ 'Rp ' . number_format($tax, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center pt-2 md:pt-3 border-t border-[#E8E8E8]">
                        <span class="text-base md:text-lg font-bold text-[#2F2D2C]">Total Bayar</span>
                        <span class="text-xl md:text-2xl font-black text-[#C67C4E]">{{ $order->formatted_total }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Status Notice -->
            <div class="mx-4 md:mx-6 mb-4 md:mb-5">
                <div class="bg-[#F9F9F9] rounded-xl p-4 md:p-5 flex items-start gap-2 md:gap-3 border border-[#E8E8E8]">
                    <span class="material-symbols-outlined text-[#9B9B9B] flex-shrink-0 text-xl md:text-2xl">update</span>
                    <div>
                        <h4 class="font-bold text-[#2F2D2C] text-sm md:text-base mb-1">Status Pembayaran</h4>
                        <p class="text-xs md:text-sm text-[#9B9B9B] leading-relaxed">
                            Status pesanan akan otomatis diperbarui setelah kasir mengkonfirmasi pembayaran Anda.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="px-4 md:px-6 pb-5 md:pb-6 space-y-2.5 md:space-y-3">
                <a href="{{ route('track') }}" 
                   class="block w-full bg-[#C67C4E] hover:bg-[#A05E35] text-white text-center py-3 md:py-3.5 rounded-xl font-bold text-sm md:text-base shadow-lg hover:shadow-xl active:scale-[0.99] transition-all">
                    <span class="material-symbols-outlined align-middle mr-2">my_location</span>
                    Lacak Status Pesanan
                </a>
                
                <a href="{{ route('home') }}" 
                   class="block w-full bg-white hover:bg-[#F9F9F9] text-[#2F2D2C] text-center py-3 md:py-3.5 rounded-xl font-bold text-sm md:text-base border-2 border-[#E8E8E8] hover:border-[#C67C4E] transition-all">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
@keyframes slide-up {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-slide-up { animation: slide-up 0.6s ease-out; }
</style>
@endpush

@push('scripts')
<script>
// Clear cart after order created
document.addEventListener('DOMContentLoaded', function() {
    if (window.Cart && typeof window.Cart.clear === 'function') {
        Cart.clear();
    } else {
        localStorage.removeItem('cart');
    }
    
    // Auto-refresh status every 5 seconds
    const orderNumber = '{{ $order->order_number }}';
    let pollInterval = setInterval(checkOrderStatus, 5000);
    
    async function checkOrderStatus() {
        try {
            const response = await fetch(`/api/order/${orderNumber}/status`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                console.warn('Order status polling failed', response.status);
                return;
            }

            const data = await response.json().catch(() => null);
            
            if (data && data.success && data.order) {
                // If payment status changed to paid, redirect to success page
                if (data.order.payment_status === 'paid') {
                    clearInterval(pollInterval);
                    window.location.href = `/order/${orderNumber}/success`;
                }
            }
        } catch (error) {
            console.error('Error checking order status:', error);
        }
    }
});
</script>
@endpush
@endsection
