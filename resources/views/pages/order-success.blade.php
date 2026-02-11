@extends('layouts.app')

@section('title', 'Order Confirmed')

@section('content')
<section class="min-h-screen bg-[#F9F9F9] py-8 md:py-12 lg:py-16 flex items-center justify-center px-4">
    <div class="w-full max-w-md md:max-w-2xl lg:max-w-3xl xl:max-w-4xl mx-auto">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden animate-slide-up">
            <!-- Success Icon & Title -->
            <div class="text-center pt-10 md:pt-12 lg:pt-14 pb-6 md:pb-8 lg:pb-10 px-6 md:px-8 lg:px-10">
                <!-- Green Check Circle -->
                <div class="inline-flex items-center justify-center w-20 h-20 md:w-28 md:h-28 lg:w-32 lg:h-32 bg-gradient-to-br from-green-400 to-green-500 rounded-full mb-6 md:mb-8 shadow-lg">
                    <div class="bg-white rounded-full p-2 md:p-3">
                        <span class="material-symbols-outlined text-4xl md:text-6xl lg:text-7xl text-green-500">check</span>
                    </div>
                </div>
                
                <h1 class="text-2xl md:text-4xl lg:text-5xl font-bold text-[#2F2D2C] mb-3 md:mb-4">Order Confirmed</h1>
                <p class="text-[#9B9B9B] text-sm md:text-lg lg:text-xl">Thank you for your order!</p>
            </div>

            <!-- Pickup Number Box -->
            <div class="mx-6 md:mx-10 lg:mx-12 mb-6 md:mb-10 lg:mb-12">
                <div class="bg-gradient-to-br from-[#FFF5F0] to-[#FFE8D9] rounded-2xl md:rounded-3xl p-8 md:p-10 lg:p-12 border-2 border-[#C67C4E]/20 shadow-sm">
                    <p class="text-xs md:text-base lg:text-lg font-bold text-[#C67C4E] mb-3 md:mb-4 uppercase tracking-wider text-center">Pickup Number</p>
                    <p class="text-4xl md:text-6xl lg:text-7xl xl:text-8xl font-black text-[#2F2D2C] text-center mb-4 md:mb-6 tracking-tight">{{ $order->order_number }}</p>
                    <div class="flex items-center justify-center gap-2 md:gap-3 text-[#9B9B9B]">
                        <span class="material-symbols-outlined text-[20px] md:text-[28px] lg:text-[32px]">store</span>
                        <span class="text-sm md:text-lg lg:text-xl font-medium">Pick up at counter</span>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="px-6 md:px-10 lg:px-12 pb-6 md:pb-10 lg:pb-12">
                <h3 class="text-base md:text-xl lg:text-2xl font-bold text-[#2F2D2C] mb-5 md:mb-6 lg:mb-8 flex items-center gap-2 md:gap-3">
                    <span class="material-symbols-outlined text-[#C67C4E] text-xl md:text-3xl lg:text-4xl">receipt_long</span>
                    Order Summary
                </h3>

                <!-- Items List -->
                <div class="space-y-4 md:space-y-5 lg:space-y-6 mb-5 md:mb-6 lg:mb-8">
                    @foreach($order->items as $item)
                    <div class="flex items-start gap-3 md:gap-5 lg:gap-6">
                        <!-- Item Image (if available) -->
                        <div class="w-14 h-14 md:w-20 md:h-20 lg:w-24 lg:h-24 bg-[#F9F9F9] rounded-xl md:rounded-2xl flex-shrink-0 flex items-center justify-center overflow-hidden">
                            @if($item->menu && $item->menu->image)
                                <img src="/images/menus/{{ $item->menu->image }}" alt="{{ $item->menu_name }}" class="w-full h-full object-cover">
                            @else
                                <span class="material-symbols-outlined text-[#E0E0E0] text-2xl md:text-4xl lg:text-5xl">local_cafe</span>
                            @endif
                        </div>

                        <!-- Item Details -->
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-[#2F2D2C] text-sm md:text-lg lg:text-xl mb-1 md:mb-2">{{ $item->menu_name ?? $item->menu->name ?? 'Menu Item' }}</h4>
                            <p class="text-xs md:text-base lg:text-lg text-[#9B9B9B]">{{ $item->notes ?? 'Regular' }}</p>
                        </div>

                        <!-- Price -->
                        <div class="text-right flex-shrink-0">
                            <p class="font-bold text-[#2F2D2C] text-sm md:text-lg lg:text-xl">{{ 'Rp ' . number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Price Summary -->
                <div class="space-y-3 md:space-y-4 lg:space-y-5 pt-5 md:pt-6 lg:pt-8 border-t-2 border-dashed border-[#E8E8E8]">
                    @php
                        $subtotal = $order->total_amount / 1.08; // Assuming 8% tax
                        $tax = $order->total_amount - $subtotal;
                    @endphp
                    
                    <div class="flex justify-between text-sm md:text-lg lg:text-xl">
                        <span class="text-[#9B9B9B]">Subtotal</span>
                        <span class="font-semibold text-[#2F2D2C]">{{ 'Rp ' . number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-between text-sm md:text-lg lg:text-xl">
                        <span class="text-[#9B9B9B]">Tax (8%)</span>
                        <span class="font-semibold text-[#2F2D2C]">{{ 'Rp ' . number_format($tax, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center pt-3 md:pt-4 lg:pt-5 border-t border-[#E8E8E8]">
                        <span class="text-base md:text-xl lg:text-2xl font-bold text-[#2F2D2C]">Total</span>
                        <span class="text-xl md:text-3xl lg:text-4xl font-black text-[#C67C4E]">{{ $order->formatted_total }}</span>
                    </div>
                </div>
            </div>

            <!-- Info Message -->
            <div class="mx-6 md:mx-10 lg:mx-12 mb-6 md:mb-10 lg:mb-12">
                @if($order->payment_method === 'cash')
                <div class="bg-[#F9F9F9] rounded-xl md:rounded-2xl p-5 md:p-6 lg:p-8 flex items-start gap-3 md:gap-4">
                    <span class="material-symbols-outlined text-[#9B9B9B] flex-shrink-0 text-xl md:text-3xl lg:text-4xl">schedule</span>
                    <p class="text-sm md:text-base lg:text-lg text-[#9B9B9B] leading-relaxed">Pesanan Anda sedang disiapkan. Pembayaran akan dilakukan saat pengambilan pesanan.</p>
                </div>
                @elseif($order->payment_status === 'paid')
                <div class="bg-green-50 rounded-xl md:rounded-2xl p-5 md:p-6 lg:p-8 flex items-start gap-3 md:gap-4 border border-green-200">
                    <span class="material-symbols-outlined text-green-600 flex-shrink-0 text-xl md:text-3xl lg:text-4xl">verified</span>
                    <p class="text-sm md:text-base lg:text-lg text-green-700 leading-relaxed">Pembayaran berhasil! Pesanan Anda sedang disiapkan.</p>
                </div>
                @elseif($paymentError)
                <div class="bg-red-50 rounded-xl md:rounded-2xl p-5 md:p-6 lg:p-8 flex items-start gap-3 md:gap-4 border border-red-200">
                    <span class="material-symbols-outlined text-red-600 flex-shrink-0 text-xl md:text-3xl lg:text-4xl">error</span>
                    <div>
                        <p class="text-sm md:text-base lg:text-lg text-red-700 font-medium mb-2">Gagal membuat pembayaran</p>
                        <p class="text-xs md:text-sm text-red-600">{{ $paymentError }}</p>
                    </div>
                </div>
                @else
                <div class="bg-blue-50 rounded-xl md:rounded-2xl p-5 md:p-6 lg:p-8 flex items-start gap-3 md:gap-4 border border-blue-200">
                    <span class="material-symbols-outlined text-blue-600 flex-shrink-0 text-xl md:text-3xl lg:text-4xl">info</span>
                    <p class="text-sm md:text-base lg:text-lg text-blue-700 leading-relaxed">Silakan selesaikan pembayaran untuk mengkonfirmasi pesanan Anda.</p>
                </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="px-6 md:px-10 lg:px-12 pb-8 md:pb-10 lg:pb-12 space-y-3 md:space-y-4 lg:space-y-5">
                @if($order->payment_method !== 'cash' && !$paymentError && $order->payment_status !== 'paid')
                    @if(isset($snapToken))
                    <button id="pay-button" 
                       class="block w-full bg-[#C67C4E] hover:bg-[#A05E35] text-white text-center py-4 md:py-5 lg:py-6 rounded-2xl md:rounded-3xl font-bold text-sm md:text-lg lg:text-xl shadow-lg hover:shadow-xl active:scale-[0.99] transition-all">
                        <span class="material-symbols-outlined align-middle mr-2">qr_code_2</span>
                        Bayar dengan QRIS/Kartu
                    </button>
                    @else
                    <div class="w-full bg-gray-100 text-gray-600 text-center py-4 md:py-5 lg:py-6 rounded-2xl md:rounded-3xl font-bold text-sm md:text-lg lg:text-xl">
                        Sedang menyiapkan pembayaran...
                    </div>
                    @endif
                @endif
                
                <a href="{{ route('track') }}" 
                   class="block w-full bg-[#C67C4E] hover:bg-[#A05E35] text-white text-center py-4 md:py-5 lg:py-6 rounded-2xl md:rounded-3xl font-bold text-sm md:text-lg lg:text-xl shadow-lg hover:shadow-xl active:scale-[0.99] transition-all">
                    <span class="material-symbols-outlined align-middle mr-2">my_location</span>
                    Lacak Status Pesanan
                </a>
                
                <a href="{{ route('home') }}" 
                   class="block w-full bg-white hover:bg-[#F9F9F9] text-[#2F2D2C] text-center py-4 md:py-5 lg:py-6 rounded-2xl md:rounded-3xl font-bold text-sm md:text-lg lg:text-xl border-2 border-[#E8E8E8] hover:border-[#C67C4E] transition-all">
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
// Clear cart after successful order
document.addEventListener('DOMContentLoaded', function() {
    // Only verify clear method exists before calling to prevent errors
    if (window.Cart && typeof window.Cart.clear === 'function') {
        Cart.clear();
    } else {
        localStorage.removeItem('cart'); // Fallback
    }
});
</script>

@if(isset($snapToken) && $snapToken)
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script type="text/javascript">
    document.getElementById('pay-button').onclick = function(){
        snap.pay('{{ $snapToken }}', {
            onSuccess: function(result){
                // Payment success - redirect to order page
                window.location.href = '{{ route("track") }}';
            },
            onPending: function(result){
                // Payment pending
                console.log('Payment pending:', result);
            },
            onError: function(result){
                // Payment error
                alert('Pembayaran gagal: ' + result.status_message);
                console.log('Payment error:', result);
            },
            onClose: function(){
                // User closed the payment popup
                console.log('Payment popup closed');
            }
        });
    };
</script>
@endif
@endpush
@endsection
