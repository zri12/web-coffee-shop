@extends('layouts.app')

@section('title', 'Keranjang')

@section('content')
<section class="bg-surface-light dark:bg-surface-dark py-12 lg:py-16 border-b border-[#f4f2f0] dark:border-[#3E2723]">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl lg:text-4xl font-bold text-text-main dark:text-white mb-4">Keranjang Belanja</h1>
        <p class="text-text-subtle dark:text-gray-400 max-w-2xl mx-auto">Selesaikan pesanan Anda dan nikmati hidangan lezat kami.</p>
    </div>
</section>

<section class="py-12 lg:py-16">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Success/Error Messages --}}
        @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg">
            <div class="flex items-center">
                <span class="material-symbols-outlined text-green-500 mr-3">check_circle</span>
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
            <div class="flex items-center">
                <span class="material-symbols-outlined text-red-500 mr-3">error</span>
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        </div>
        @endif
        
        @if(empty($cartItems))
        {{-- Empty Cart State --}}
        <div class="bg-white dark:bg-surface-dark border border-[#f4f2f0] dark:border-[#3E2723] rounded-xl p-12 text-center">
            <div class="w-24 h-24 mx-auto mb-6 bg-background-light dark:bg-background-dark rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined text-6xl text-text-subtle dark:text-gray-400">shopping_cart_off</span>
            </div>
            <h3 class="text-xl font-bold text-text-main dark:text-white mb-2">Keranjang Masih Kosong ☕</h3>
            <p class="text-text-subtle dark:text-gray-400 mb-6">Belum ada item di keranjang Anda.</p>
            <a href="{{ route('menu.index') }}" 
               class="inline-flex items-center justify-center h-12 px-8 rounded-full bg-primary hover:bg-primary-dark text-white text-sm font-bold transition-colors shadow-sm">
                Lihat Menu
            </a>
        </div>
        @else
        {{-- Cart with Items --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Cart Items --}}
            <div class="lg:col-span-2">
                <div class="space-y-4">
                    @foreach($cartItems as $item)
                    <div class="bg-white dark:bg-surface-dark border border-[#f4f2f0] dark:border-[#3E2723] rounded-xl p-4 flex items-center gap-4 shadow-sm">
                        {{-- Product Image --}}
                        <div class="w-20 h-20 bg-background-light dark:bg-background-dark rounded-lg flex-shrink-0 overflow-hidden">
                            @if(!empty($item['image']))
                                <img src="{{ $item['image'] }}" 
                                     alt="{{ $item['name'] }}" 
                                     class="w-full h-full object-cover"
                                     onerror="this.onerror=null; this.src='/images/placeholder-menu.jpg';">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="material-symbols-outlined text-3xl text-text-subtle/30">coffee</span>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Product Details --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-text-main dark:text-white truncate text-lg">{{ $item['name'] }}</h3>
                            <p class="text-primary font-bold mb-1">Rp {{ number_format($item['total_price'] ?? $item['base_price'] ?? 0, 0, ',', '.') }}</p>
                            <p class="text-xs text-text-subtle dark:text-gray-400">Qty: {{ $item['quantity'] ?? $item['qty'] ?? 1 }}</p>
                            
                            {{-- Options Display --}}
                            @php
                                $opts = $item['options'] ?? [];
                                $displayLines = [];

                                // Simple option fields
                                if(!empty($opts['size'])) $displayLines[] = ['label' => 'Size', 'values' => [ucfirst($opts['size'])]];
                                if(!empty($opts['portion'])) $displayLines[] = ['label' => 'Portion', 'values' => [ucfirst($opts['portion'])]];
                                if(!empty($opts['temperature'])) $displayLines[] = ['label' => 'Temp', 'values' => [ucfirst($opts['temperature'])]];
                                if(!empty($opts['ice_level'])) $displayLines[] = ['label' => 'Ice', 'values' => [ucfirst(str_replace('_',' ', $opts['ice_level']))]];
                                if(!empty($opts['sugar_level'])) $displayLines[] = ['label' => 'Sugar', 'values' => [ucfirst(str_replace('_',' ', $opts['sugar_level']))]];
                                if(!empty($opts['spice_level'])) $displayLines[] = ['label' => 'Spice', 'values' => [ucfirst($opts['spice_level'])]];
                                if(!empty($opts['addons'])) {
                                    $addons = array_filter((array)$opts['addons']);
                                    if(count($addons)) {
                                        $displayLines[] = ['label' => 'Add-ons', 'values' => array_map(fn($a)=>ucfirst(str_replace('_',' ',$a)), $addons)];
                                    }
                                }

                                // From raw_options (option_groups array)
                                $rawOptionGroups = $item['raw_options']['option_groups'] ?? null;
                                if(is_array($rawOptionGroups)) {
                                    $displayLines = array_merge($displayLines,
                                        collect($rawOptionGroups)->map(function($g){
                                            $vals = $g['selected_values'] ?? [];
                                            $names = collect($vals)->pluck('name')->filter()->all();
                                            return [
                                                'label' => $g['name'] ?? ucfirst($g['id'] ?? ''),
                                                'values' => $names
                                            ];
                                        })->filter(fn($g)=>!empty($g['values']))->values()->all()
                                    );
                                }

                                // From raw_options associative (key => ['selected_values'=>...])
                                if(empty($rawOptionGroups) && isset($item['raw_options']) && is_array($item['raw_options'])) {
                                    foreach ($item['raw_options'] as $key => $group) {
                                        if ($key === 'option_groups') continue;
                                        if (is_array($group) && isset($group['selected_values'])) {
                                            $names = collect($group['selected_values'])->pluck('name')->filter()->all();
                                            if (!empty($names)) {
                                                $displayLines[] = [
                                                    'label' => $group['name'] ?? ucfirst($key),
                                                    'values' => $names
                                                ];
                                            }
                                        }
                                    }
                                }

                                // From normalized_options (fallback)
                                if(isset($item['normalized_options']) || isset($item['normalized_options_display'])) {
                                    $norm = $item['normalized_options'] ?? ($item['normalized_options_display'] ?? []);
                                    if(is_array($norm)) {
                                        foreach ($norm as $gid => $vals) {
                                            $names = collect($vals)->pluck('name')->filter()->all();
                                            if (!empty($names)) {
                                                $displayLines[] = [
                                                    'label' => ucfirst(str_replace('_',' ',$gid)),
                                                    'values' => $names
                                                ];
                                            }
                                        }
                                    }
                                }
                            @endphp
                            @if(!empty($displayLines))
                            <div class="text-xs text-text-subtle dark:text-gray-400 mt-2 space-y-1">
                                @foreach($displayLines as $line)
                                    <p><span class="font-semibold text-text-main dark:text-white">{{ $line['label'] }}:</span> {{ implode(', ', $line['values']) }}</p>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        
                        {{-- Subtotal --}}
                        <div class="hidden sm:block text-right min-w-[100px] flex-shrink-0">
                            <p class="font-bold text-text-main dark:text-white">
                                Rp {{ number_format(($item['total_price'] ?? $item['base_price'] ?? 0) * ($item['quantity'] ?? $item['qty'] ?? 1), 0, ',', '.') }}
                            </p>
                        </div>
                        
                        {{-- Delete Button --}}
                        <form action="{{ route('cart.remove', $item['cart_key']) }}" method="POST" class="flex-shrink-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10 rounded-lg transition-colors"
                                    onclick="return confirm('Hapus item ini dari keranjang?')"
                                    title="Hapus item">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                
                {{-- Clear Cart Button --}}
                <div class="mt-4">
                    <form action="{{ route('cart.clear') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="text-sm text-red-600 hover:text-red-700 font-medium"
                                onclick="return confirm('Kosongkan seluruh keranjang?')">
                            <span class="material-symbols-outlined text-[16px] align-middle">delete_sweep</span>
                            Kosongkan Keranjang
                        </button>
                    </form>
                </div>
            </div>
            
            {{-- Order Summary --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-surface-dark border border-[#f4f2f0] dark:border-[#3E2723] rounded-xl p-6 sticky top-24 shadow-sm">
                    <h3 class="font-bold text-text-main dark:text-white text-lg mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">receipt_long</span>
                        Ringkasan Pesanan
                    </h3>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-text-subtle dark:text-gray-400">
                            <span>Subtotal ({{ count($cartItems) }} item)</span>
                            <span class="font-medium text-text-main dark:text-white">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-text-subtle dark:text-gray-400">
                            <span>Biaya Layanan</span>
                            <span class="font-medium text-text-main dark:text-white">Gratis</span>
                        </div>
                        <hr class="border-[#f4f2f0] dark:border-[#3E2723]">
                        <div class="flex justify-between text-xl font-bold text-text-main dark:text-white">
                            <span>Total</span>
                            <span class="text-primary">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <form action="{{ route('checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="cart_items" id="cart_items_field">
                        
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-bold text-text-main dark:text-white mb-2">Nama Pemesan</label>
                                <div class="relative">
                                    <input type="text" name="customer_name" required 
                                           class="w-full pl-10 pr-4 py-3 bg-background-light dark:bg-background-dark border border-[#e6e0db] dark:border-[#3E2723] rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" 
                                           placeholder="Nama Anda">
                                    <span class="material-symbols-outlined text-text-subtle dark:text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 text-[20px]">person</span>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-text-main dark:text-white mb-2">Nomor Telepon</label>
                                <div class="relative">
                                    <input type="tel" name="customer_phone"
                                           class="w-full pl-10 pr-4 py-3 bg-background-light dark:bg-background-dark border border-[#e6e0db] dark:border-[#3E2723] rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" 
                                           placeholder="08xx xxxx xxxx">
                                    <span class="material-symbols-outlined text-text-subtle dark:text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 text-[20px]">phone</span>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-text-main dark:text-white mb-2">Nomor Meja</label>
                                <div class="relative">
                                    <input type="number" name="table_number" min="0"
                                           class="w-full pl-10 pr-4 py-3 bg-background-light dark:bg-background-dark border border-[#e6e0db] dark:border-[#3E2723] rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" 
                                           placeholder="Contoh: 5">
                                    <span class="material-symbols-outlined text-text-subtle dark:text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 text-[20px]">table_restaurant</span>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-text-main dark:text-white mb-2">Catatan</label>
                                <textarea name="notes" rows="2" 
                                          class="w-full px-4 py-3 bg-background-light dark:bg-background-dark border border-[#e6e0db] dark:border-[#3E2723] rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" 
                                          placeholder="Permintaan khusus..."></textarea>
                            </div>
                            
                            {{-- Payment Method --}}
                            <div>
                                <label class="block text-sm font-bold text-text-main dark:text-white mb-2">Metode Pembayaran</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="payment_method" value="cash" class="peer sr-only" checked>
                                        <div class="p-3 border border-[#e6e0db] dark:border-[#3E2723] rounded-xl peer-checked:border-primary peer-checked:bg-primary/5 hover:border-primary/50 transition-all text-center">
                                            <span class="material-symbols-outlined text-2xl mb-1 text-text-main dark:text-white">payments</span>
                                            <p class="text-sm font-bold text-text-main dark:text-white">Tunai</p>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="payment_method" value="qris" class="peer sr-only">
                                        <div class="p-3 border border-[#e6e0db] dark:border-[#3E2723] rounded-xl peer-checked:border-primary peer-checked:bg-primary/5 hover:border-primary/50 transition-all text-center">
                                            <span class="material-symbols-outlined text-2xl mb-1 text-text-main dark:text-white">qr_code_scanner</span>
                                            <p class="text-sm font-bold text-text-main dark:text-white">QRIS</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full flex items-center justify-center h-14 px-8 rounded-xl bg-primary hover:bg-primary-dark text-white text-lg font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                            Checkout Sekarang
                            <span class="material-symbols-outlined ml-2">arrow_forward</span>
                        </button>
                    </form>
                    
                    <p class="text-xs text-text-subtle dark:text-gray-500 text-center mt-4">
                        Dengan memesan, Anda menyetujui syarat dan ketentuan kami
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

@push('styles')
<style>
/* Hide cart icon in navbar on cart page */
header a[href*="cart"] {
    display: none !important;
}
</style>
@endpush
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tableInput = document.querySelector('input[name="table_number"]');
        if (!tableInput) return;

        const storageKey = 'customer_table_number';
        const storedTable = localStorage.getItem(storageKey);
        if (storedTable) {
            tableInput.value = storedTable;
        }

        tableInput.addEventListener('input', () => {
            if (tableInput.value) {
                localStorage.setItem(storageKey, tableInput.value);
            } else {
                localStorage.removeItem(storageKey);
            }
        });

        const cartField = document.getElementById('cart_items_field');
        if (cartField) {
            cartField.value = JSON.stringify(@json($cartItems ?? []));
        }
    });
</script>
@endpush

@endsection
