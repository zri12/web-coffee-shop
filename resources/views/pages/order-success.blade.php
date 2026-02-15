@extends('layouts.app')

@section('title', 'Order Status')

@section('content')
<section class="min-h-[100dvh] bg-[#F9F9F9] flex items-start justify-center px-4 py-6">
    <div class="w-full max-w-md mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden animate-slide-up">
            <!-- Live Status Badge -->
            <div class="px-4 md:px-6 pt-3 md:pt-4 flex justify-end">
                <div class="flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 border border-blue-200 rounded-full">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                    <span class="text-[10px] md:text-xs font-medium text-blue-700">Status otomatis diperbarui</span>
                </div>
            </div>
            
            <!-- Success Icon & Title -->
            <div class="text-center pt-4 md:pt-6 pb-3 md:pb-4 px-4 md:px-6">
                @php
                    $iconColor = 'green';
                    $iconName = 'check';
                    $title = 'Order Confirmed';
                    $subtitle = 'Payment successful! Thank you for your order!';
                    
                    if ($order->payment_status === 'paid') {
                        if ($order->status === 'completed') {
                            $iconColor = 'green';
                            $iconName = 'task_alt';
                            $title = 'Pesanan Siap!';
                            $subtitle = 'Pesanan Anda sudah siap diambil';
                        } elseif ($order->status === 'processing' || $order->status === 'preparing') {
                            $iconColor = 'blue';
                            $iconName = 'cooking';
                            $title = 'Sedang Disiapkan';
                            $subtitle = 'Barista sedang menyiapkan pesanan Anda';
                        } else {
                            $iconColor = 'green';
                            $iconName = 'check_circle';
                            $title = 'Pembayaran Berhasil';
                            $subtitle = 'Pesanan akan segera diproses';
                        }
                    } elseif ($order->payment_method === 'cash') {
                        $iconColor = 'yellow';
                        $iconName = 'schedule';
                        $title = 'Menunggu Pembayaran';
                        $subtitle = 'Silakan bayar di kasir';
                    } else {
                        $iconColor = 'blue';
                        $iconName = 'pending';
                        $title = 'Menunggu Pembayaran';
                        $subtitle = 'Selesaikan pembayaran QRIS';
                    }
                @endphp
                
                <!-- Dynamic Icon Circle -->
                <div class="inline-flex items-center justify-center w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-{{ $iconColor }}-400 to-{{ $iconColor }}-500 rounded-full mb-3 md:mb-4 shadow-lg {{ $iconColor === 'blue' ? 'animate-pulse' : '' }}">
                    <div class="bg-white rounded-full p-1.5 md:p-2">
                        <span class="material-symbols-outlined text-3xl md:text-4xl text-{{ $iconColor }}-500">{{ $iconName }}</span>
                    </div>
                </div>
                
                <h1 class="text-xl md:text-2xl font-bold text-[#2F2D2C] mb-1.5 md:mb-2">{{ $title }}</h1>
                <p class="text-[#9B9B9B] text-xs md:text-sm">{{ $subtitle }}</p>
            </div>

            <!-- Pickup Number Box -->
            <div class="mx-4 md:mx-6 mb-4 md:mb-5">
                <div class="bg-gradient-to-br from-[#FFF5F0] to-[#FFE8D9] rounded-xl p-4 md:p-6 border-2 border-[#C67C4E]/20 shadow-sm">
                    <p class="text-[10px] md:text-xs font-bold text-[#C67C4E] mb-1.5 md:mb-2 uppercase tracking-wider text-center">Pickup Number</p>
                    <p class="text-3xl md:text-4xl font-black text-[#2F2D2C] text-center mb-2 md:mb-3 tracking-tight">{{ $order->order_number }}</p>
                    <div class="flex items-center justify-center gap-1.5 text-[#9B9B9B]">
                        <span class="material-symbols-outlined text-[18px] md:text-[20px]">store</span>
                        <span class="text-xs md:text-sm font-medium">Pick up at counter</span>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="px-4 md:px-6 pb-4 md:pb-5">
                <h3 class="text-sm md:text-base font-bold text-[#2F2D2C] mb-3 md:mb-4 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[#C67C4E] text-xl md:text-2xl">receipt_long</span>
                    Order Summary
                </h3>

                <!-- Items List -->
                <div class="space-y-3 md:space-y-4 mb-4 md:mb-5">
                    @foreach($order->items as $item)
                    <div class="flex items-start gap-3 md:gap-4">
                        <!-- Item Image (if available) -->
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
                        $subtotal = $order->total_amount / 1.08; // Assuming 8% tax
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
                        <span class="text-base md:text-lg font-bold text-[#2F2D2C]">Total</span>
                        <span class="text-xl md:text-2xl font-black text-[#C67C4E]">{{ $order->formatted_total }}</span>
                    </div>
                </div>
            </div>

            <!-- Info Message -->
            <div class="mx-4 md:mx-6 mb-4 md:mb-5">
                @if($order->payment_method === 'cash' && $order->payment_status === 'unpaid')
                <div class="bg-yellow-50 rounded-xl p-4 md:p-5 flex items-start gap-2 md:gap-3 border border-yellow-200">
                    <span class="material-symbols-outlined text-yellow-600 flex-shrink-0 text-xl md:text-2xl">schedule</span>
                    <div>
                        <p class="text-sm md:text-base text-yellow-800 font-medium mb-1">Menunggu Pembayaran</p>
                        <p class="text-xs md:text-sm text-yellow-700 leading-relaxed">Silakan lakukan pembayaran di kasir. Status akan diperbarui otomatis.</p>
                    </div>
                </div>
                @elseif($order->payment_status === 'paid')
                    @if($order->status === 'completed')
                    <div class="bg-green-50 rounded-xl p-4 md:p-5 flex items-start gap-2 md:gap-3 border border-green-200">
                        <span class="material-symbols-outlined text-green-600 flex-shrink-0 text-xl md:text-2xl">task_alt</span>
                        <div>
                            <p class="text-sm md:text-base text-green-800 font-medium mb-1">Pesanan Siap!</p>
                            <p class="text-xs md:text-sm text-green-700 leading-relaxed">Pesanan Anda sudah siap diambil di counter.</p>
                        </div>
                    </div>
                    @elseif($order->status === 'processing' || $order->status === 'preparing')
                    <div class="bg-blue-50 rounded-xl p-4 md:p-5 flex items-start gap-2 md:gap-3 border border-blue-200">
                        <span class="material-symbols-outlined text-blue-600 flex-shrink-0 text-xl md:text-2xl animate-pulse">cooking</span>
                        <div>
                            <p class="text-sm md:text-base text-blue-800 font-medium mb-1">Sedang Disiapkan</p>
                            <p class="text-xs md:text-sm text-blue-700 leading-relaxed">Barista sedang menyiapkan pesanan Anda. Mohon menunggu sebentar.</p>
                        </div>
                    </div>
                    @else
                    <div class="bg-green-50 rounded-xl p-4 md:p-5 flex items-start gap-2 md:gap-3 border border-green-200">
                        <span class="material-symbols-outlined text-green-600 flex-shrink-0 text-xl md:text-2xl">verified</span>
                        <div>
                            <p class="text-sm md:text-base text-green-800 font-medium mb-1">Pembayaran Berhasil!</p>
                            <p class="text-xs md:text-sm text-green-700 leading-relaxed">Pesanan Anda akan segera diproses.</p>
                        </div>
                    </div>
                    @endif
                @elseif($paymentError)
                <div class="bg-red-50 rounded-xl p-4 md:p-5 flex items-start gap-2 md:gap-3 border border-red-200">
                    <span class="material-symbols-outlined text-red-600 flex-shrink-0 text-xl md:text-2xl">error</span>
                    <div>
                        <p class="text-sm md:text-base text-red-700 font-medium mb-1.5">Gagal membuat pembayaran</p>
                        <p class="text-xs md:text-sm text-red-600">{{ $paymentError }}</p>                        <p class="text-xs md:text-sm text-red-600 mt-1.5">Silakan hubungi kasir untuk bantuan pembayaran.</p>                    </div>
                </div>
                @else
                <div class="bg-blue-50 rounded-xl p-4 md:p-5 flex items-start gap-2 md:gap-3 border border-blue-200 animate-pulse">
                    <span class="material-symbols-outlined text-blue-600 flex-shrink-0 text-xl md:text-2xl">pending</span>
                    <div>
                        <p class="text-sm md:text-base text-blue-800 font-medium mb-1">Menunggu Pembayaran QRIS</p>
                        <p class="text-xs md:text-sm text-blue-700 leading-relaxed">Silakan selesaikan pembayaran untuk mengkonfirmasi pesanan Anda.</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="px-4 md:px-6 pb-5 md:pb-6 space-y-2.5 md:space-y-3">
                @if($order->payment_method === 'qris' && $order->payment_status !== 'paid')
                    @if(isset($snapToken) && $snapToken)
                    <!-- QRIS Payment Button -->
                    <button id="pay-button" 
                       class="block w-full bg-[#C67C4E] hover:bg-[#A05E35] text-white text-center py-3 md:py-3.5 rounded-xl font-bold text-sm md:text-base shadow-lg hover:shadow-xl active:scale-[0.99] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="material-symbols-outlined align-middle mr-2">qr_code_2</span>
                        <span id="pay-button-text">Bayar Sekarang (QRIS)</span>
                    </button>
                    @elseif(isset($paymentError) && $paymentError)
                    <!-- Payment Error State -->
                    <div class="w-full bg-red-100 text-red-700 text-center py-3 md:py-3.5 rounded-xl font-bold text-sm md:text-base border-2 border-red-200">
                        <span class="material-symbols-outlined align-middle mr-2">error</span>
                        Gagal Membuat Pembayaran
                    </div>
                    <div class="text-center text-sm text-red-600 p-4">
                        {{ $paymentError }}
                    </div>
                    @endif
                @endif
                
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
// Clear cart after successful order
document.addEventListener('DOMContentLoaded', function() {
    // Only verify clear method exists before calling to prevent errors
    if (window.Cart && typeof window.Cart.clear === 'function') {
        Cart.clear();
    } else {
        localStorage.removeItem('cart'); // Fallback
    }
    
    // Auto-refresh status every 8 seconds for status updates
    const orderNumber = '{{ $order->order_number }}';
    const currentPaymentStatus = '{{ $order->payment_status }}';
    const currentOrderStatus = '{{ $order->status }}';
    
    let pollInterval = setInterval(checkOrderStatus, 8000);
    
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
                // If status changed, reload page to show updated UI
                if (data.order.payment_status !== currentPaymentStatus || 
                    data.order.status !== currentOrderStatus) {
                    window.location.reload();
                }
            }
        } catch (error) {
            console.error('Error checking order status:', error);
        }
    }
});
</script>

@if(isset($snapToken) && $snapToken)
<!-- Load Midtrans Snap.js -->
<script 
    src="https://app.sandbox.midtrans.com/snap/snap.js" 
    data-client-key="{{ config('midtrans.client_key') }}">
</script>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const payButton = document.getElementById('pay-button');
        const buttonText = document.getElementById('pay-button-text');
        const snapToken = '{{ $snapToken }}';
        const orderNumber = '{{ $order->order_number }}';
        
        // Debug logging
        console.log('=== MIDTRANS SNAP DEBUG ===');
        console.log('Order Number:', orderNumber);
        console.log('Snap Token:', snapToken ? (snapToken.substring(0, 20) + '... (length: ' + snapToken.length + ')') : 'NULL/EMPTY');
        console.log('Pay Button Found:', !!payButton);
        console.log('Snap Object Available:', typeof snap !== 'undefined');
        console.log('Client Key:', '{{ config("midtrans.client_key") }}');
        console.log('==========================');
        
        if (!payButton) {
            console.warn('Pay button not found in DOM');
            return;
        }
        
        if (!snapToken || snapToken.trim() === '') {
            console.error('❌ Snap token is empty or null!');
            alert('Token pembayaran tidak tersedia. Silakan hubungi kasir.');
            return;
        }
        
        // Button click handler - trigger Snap payment
        payButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            console.log('💳 Pay button clicked, triggering Snap payment...');
            
            // Disable button during payment
            payButton.disabled = true;
            buttonText.textContent = 'Membuka pembayaran...';
            
            // Verify snap object exists
            if (typeof snap === 'undefined') {
                console.error('❌ Snap object not found! Snap.js may not be loaded.');
                alert('Sistem pembayaran belum siap. Silakan refresh halaman.');
                payButton.disabled = false;
                buttonText.textContent = 'Bayar Sekarang (QRIS)';
                return;
            }
            
            console.log('✅ Calling snap.pay() with token:', snapToken.substring(0, 20) + '...');
            
            // Call Snap.pay with callbacks
            snap.pay(snapToken, {
                // SUCCESS: Payment completed successfully
                onSuccess: function(result) {
                    console.log('Payment success:', result);
                    
                    // Redirect to success page to refresh status
                    window.location.href = '/order/' + orderNumber + '/success';
                },
                
                // PENDING: Payment is pending (waiting for confirmation)
                onPending: function(result) {
                    console.log('Payment pending:', result);
                    
                    // Reload page to show updated status
                    alert('Pembayaran sedang diproses. Status akan diperbarui otomatis.');
                    window.location.reload();
                },
                
                // ERROR: Payment failed
                onError: function(result) {
                    console.error('Payment error:', result);
                    
                    // Show error message
                    alert('Pembayaran gagal: ' + (result.status_message || 'Terjadi kesalahan. Silakan coba lagi.'));
                    
                    // Re-enable button
                    payButton.disabled = false;
                    buttonText.textContent = 'Bayar Sekarang (QRIS)';
                },
                
                // CLOSE: User closed the payment popup
                onClose: function() {
                    console.log('Payment popup closed by user');
                    
                    // Show info that payment was not completed
                    alert('Pembayaran belum diselesaikan. Anda dapat melanjutkan pembayaran dengan menekan tombol bayar lagi.');
                    
                    // Re-enable button
                    payButton.disabled = false;
                    buttonText.textContent = 'Bayar Sekarang (QRIS)';
                }
            });
        });
    });
</script>
@endif
@endpush
@endsection
