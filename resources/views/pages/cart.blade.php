@extends('layouts.app')

@section('title', 'Keranjang')

@section('content')
<section class="bg-surface-light dark:bg-surface-dark py-12 lg:py-16 border-b border-[#f4f2f0] dark:border-[#3E2723]">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl lg:text-4xl font-bold text-text-main dark:text-white mb-4">Keranjang Belanja</h1>
        <p class="text-text-subtle dark:text-gray-400 max-w-2xl mx-auto">Selesaikan pesanan Anda dan nikmati hidangan lezat kami.</p>
    </div>
</section>

<section class="py-12 lg:py-16" x-data="cartPage()">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Error/Success Messages -->
        @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
            <div class="flex items-center">
                <span class="material-symbols-outlined text-red-500 mr-3">error</span>
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        </div>
        @endif
        
        @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg">
            <div class="flex items-center">
                <span class="material-symbols-outlined text-green-500 mr-3">check_circle</span>
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
        @endif
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <template x-if="items.length === 0">
                    <div class="bg-white dark:bg-surface-dark border border-[#f4f2f0] dark:border-[#3E2723] rounded-xl p-12 text-center">
                        <div class="w-24 h-24 mx-auto mb-6 bg-background-light dark:bg-background-dark rounded-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-4xl text-text-subtle dark:text-gray-400">shopping_cart_off</span>
                        </div>
                        <h3 class="text-lg font-bold text-text-main dark:text-white mb-2">Keranjang kosong</h3>
                        <p class="text-text-subtle dark:text-gray-400 mb-6">Belum ada item di keranjang Anda.</p>
                        <a :href="window.appTableNumber ? `/order/${window.appTableNumber}/menu` : '{{ route('menu.index') }}'" class="inline-flex items-center justify-center h-12 px-8 rounded-full bg-primary hover:bg-primary-dark text-white text-sm font-bold transition-colors shadow-sm">
                            Lihat Menu
                        </a>
                    </div>
                </template>
                
                <template x-if="items.length > 0">
                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="bg-white dark:bg-surface-dark border border-[#f4f2f0] dark:border-[#3E2723] rounded-xl p-4 flex items-center gap-4 shadow-sm">
                                <!-- Image -->
                                <div class="w-20 h-20 bg-background-light dark:bg-background-dark rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                                    <template x-if="item.image">
                                        <img :src="resolveImageUrl(item.image)" :alt="item.name" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!item.image">
                                        <span class="material-symbols-outlined text-3xl text-text-subtle/30">coffee</span>
                                    </template>
                                </div>
                                
                                <!-- Details -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-text-main dark:text-white truncate text-lg" x-text="item.name"></h3>
                                    <p class="text-primary font-bold mb-1" x-text="formatPrice(getUnitPrice(item))"></p>
                                    <p class="text-xs text-text-subtle dark:text-gray-400" x-text="'Qty: ' + (item.quantity || 1)"></p>
                                    
                                    <!-- Product Options -->
                                    <div x-show="item.options" class="text-xs text-text-subtle dark:text-gray-400 space-y-0.5 mt-2">
                                        <!-- BEVERAGE OPTIONS -->
                                        <template x-if="item.type === 'beverage'">
                                            <div class="space-y-0.5">
                                                <!-- Temperature -->
                                                <template x-if="item.options && item.options.temperature">
                                                    <p class="flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[14px]">thermostat</span>
                                                        <span>Temp: <span x-text="item.options.temperature.charAt(0).toUpperCase() + item.options.temperature.slice(1)"></span></span>
                                                    </p>
                                                </template>

                                                <!-- Ice Level -->
                                                <template x-if="item.options && item.options.iceLevel">
                                                    <p class="flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[14px]">ac_unit</span>
                                                        <span>Ice: <span x-text="item.options.iceLevel === 'normal' ? 'Normal' : item.options.iceLevel === 'less' ? 'Less' : 'No Ice'"></span></span>
                                                    </p>
                                                </template>

                                                <!-- Sugar Level -->
                                                <template x-if="item.options && item.options.sugarLevel">
                                                    <p class="flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[14px]">water_drop</span>
                                                        <span>Sugar: <span x-text="item.options.sugarLevel === 'no-sugar' ? 'No Sugar' : item.options.sugarLevel.charAt(0).toUpperCase() + item.options.sugarLevel.slice(1)"></span></span>
                                                    </p>
                                                </template>
                                                
                                                <!-- Size -->
                                                <template x-if="item.options && item.options.size">
                                                    <p class="flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[14px]">height</span>
                                                        <span>Size: <span x-text="item.options.size.charAt(0).toUpperCase() + item.options.size.slice(1)"></span></span>
                                                    </p>
                                                </template>

                                                <!-- Add-Ons -->
                                                <template x-if="item.options && item.options.addOns && item.options.addOns.length > 0">
                                                    <p class="flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[14px]">add_circle</span>
                                                        <span>Add-Ons: <span x-text="item.options.addOns.map(a => a.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')).join(', ')"></span></span>
                                                    </p>
                                                </template>
                                            </div>
                                        </template>

                                        <!-- FOOD OPTIONS -->
                                        <template x-if="item.type === 'food'">
                                            <div class="space-y-0.5">
                                                <!-- Spice Level -->
                                                <template x-if="item.options && item.options.spiceLevel">
                                                    <p class="flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[14px]">local_fire_department</span>
                                                        <span>Spice: <span x-text="item.options.spiceLevel.charAt(0).toUpperCase() + item.options.spiceLevel.slice(1)"></span></span>
                                                    </p>
                                                </template>
                                                
                                                <!-- Portion -->
                                                <template x-if="item.options && item.options.portion">
                                                    <p class="flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[14px]">restaurant</span>
                                                        <span>Portion: <span x-text="item.options.portion.charAt(0).toUpperCase() + item.options.portion.slice(1)"></span></span>
                                                    </p>
                                                </template>

                                                <!-- Add-Ons -->
                                                <template x-if="item.options && item.options.addOns && item.options.addOns.length > 0">
                                                    <p class="flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[14px]">add_circle</span>
                                                        <span>Add-Ons: <span x-text="item.options.addOns.map(a => a.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')).join(', ')"></span></span>
                                                    </p>
                                                </template>
                                            </div>
                                        </template>

                                        <!-- SNACK OPTIONS -->
                                        <template x-if="item.type === 'snack'">
                                            <div class="space-y-0.5">
                                                <!-- Portion -->
                                                <template x-if="item.options && item.options.portion">
                                                    <p class="flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[14px]">restaurant</span>
                                                        <span>Size: <span x-text="item.options.portion.charAt(0).toUpperCase() + item.options.portion.slice(1)"></span></span>
                                                    </p>
                                                </template>

                                                <!-- Sauces -->
                                                <template x-if="item.options && item.options.sauces && item.options.sauces.length > 0">
                                                    <p class="flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[14px]">liquor</span>
                                                        <span>Sauces: <span x-text="item.options.sauces.map(s => s.charAt(0).toUpperCase() + s.slice(1)).join(', ')"></span></span>
                                                    </p>
                                                </template>
                                            </div>
                                        </template>

                                        <!-- DESSERT OPTIONS -->
                                        <template x-if="item.type === 'dessert'">
                                            <div class="space-y-0.5">
                                                <!-- Portion/Size -->
                                                <template x-if="item.options && item.options.portion">
                                                    <p class="flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[14px]">restaurant</span>
                                                        <span>Size: <span x-text="item.options.portion.charAt(0).toUpperCase() + item.options.portion.slice(1)"></span></span>
                                                    </p>
                                                </template>

                                                <!-- Toppings -->
                                                <template x-if="item.options && item.options.toppings && item.options.toppings.length > 0">
                                                    <p class="flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[14px]">cake</span>
                                                        <span>Toppings: <span x-text="item.options.toppings.map(t => t.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')).join(', ')"></span></span>
                                                    </p>
                                                </template>
                                            </div>
                                        </template>
                                        
                                        <!-- Special Request (All Types) -->
                                        <template x-if="item.options && item.options.specialRequest && item.options.specialRequest.trim() !== ''">
                                            <p class="flex items-start gap-1">
                                                <span class="material-symbols-outlined text-[14px]">note</span>
                                                <span class="flex-1 line-clamp-2">Note: <span x-text="item.options.specialRequest"></span></span>
                                            </p>
                                        </template>

                                        <!-- Add-On Price Breakdown -->
                                        <template x-if="getAddons(item).length > 0">
                                            <div class="pt-1">
                                                <template x-for="(addon, addonIndex) in getAddons(item)" :key="`${index}-${addonIndex}`">
                                                    <p class="flex items-center justify-between gap-2">
                                                        <span>+ <span x-text="addon.name"></span></span>
                                                        <span class="font-medium text-primary" x-text="formatPrice(addon.price)"></span>
                                                    </p>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                
                                <!-- Subtotal (Price for this single item) -->
                                <div class="hidden sm:block text-right min-w-[100px] flex-shrink-0">
                                    <p class="font-bold text-text-main dark:text-white" x-text="formatPrice(getItemSubtotal(item))"></p>
                                </div>
                                
                                <!-- Remove Button (No quantity controls - each item is qty=1) -->
                                <button @click="removeItem(index)" 
                                        class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10 rounded-lg transition-colors flex-shrink-0"
                                        title="Remove this item">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
            
            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-surface-dark border border-[#f4f2f0] dark:border-[#3E2723] rounded-xl p-6 sticky top-24 shadow-sm">
                    <h3 class="font-bold text-text-main dark:text-white text-lg mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">receipt_long</span>
                        Ringkasan Pesanan
                    </h3>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-text-subtle dark:text-gray-400">
                            <span>Subtotal (<span x-text="totalItems"></span> item)</span>
                            <span class="font-medium text-text-main dark:text-white" x-text="formatPrice(subtotalPrice)"></span>
                        </div>
                        <div class="flex justify-between text-text-subtle dark:text-gray-400">
                            <span>Biaya Layanan</span>
                            <span class="font-medium text-text-main dark:text-white">Gratis</span>
                        </div>
                        <hr class="border-[#f4f2f0] dark:border-[#3E2723]">
                        <div class="flex justify-between text-xl font-bold text-text-main dark:text-white">
                            <span>Total</span>
                            <span class="text-primary" x-text="formatPrice(totalPrice)"></span>
                        </div>
                    </div>
                    
                    <form action="{{ route('checkout') }}" method="POST" x-show="items.length > 0">
                        @csrf
                        <input type="hidden" name="cart_items" x-bind:value="JSON.stringify(items)">
                        
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
                                <label class="block text-sm font-bold text-text-main dark:text-white mb-2">Nomor Telepon (untuk pembayaran online)</label>
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
                                           class="w-full pl-10 pr-4 py-3 bg-background-light dark:bg-background-dark border border-[#e6e0db] dark:border-[#3E2723] rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all {{ isset($table) ? 'bg-gray-100 cursor-not-allowed' : '' }}" 
                                           placeholder="Contoh: 5"
                                           value="{{ isset($table) ? $table->table_number : '' }}"
                                           {{ isset($table) ? 'readonly' : '' }}>
                                    <span class="material-symbols-outlined text-text-subtle dark:text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 text-[20px]">table_restaurant</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-text-main dark:text-white mb-2">Catatan</label>
                                <textarea name="notes" rows="2" 
                                          class="w-full px-4 py-3 bg-background-light dark:bg-background-dark border border-[#e6e0db] dark:border-[#3E2723] rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" 
                                          placeholder="Permintaan khusus..."></textarea>
                            </div>
                            
                            <!-- Payment Method -->
                            <div>
                                <label class="block text-sm font-bold text-text-main dark:text-white mb-2">Metode Pembayaran</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="payment_method" value="cash" class="peer sr-only" x-model="paymentMethod">
                                        <div class="p-3 border border-[#e6e0db] dark:border-[#3E2723] rounded-xl peer-checked:border-primary peer-checked:bg-primary/5 hover:border-primary/50 transition-all text-center">
                                            <span class="material-symbols-outlined text-2xl mb-1 text-text-main dark:text-white">payments</span>
                                            <p class="text-sm font-bold text-text-main dark:text-white">Tunai</p>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="payment_method" value="qris" class="peer sr-only" x-model="paymentMethod">
                                        <div class="p-3 border border-[#e6e0db] dark:border-[#3E2723] rounded-xl peer-checked:border-primary peer-checked:bg-primary/5 hover:border-primary/50 transition-all text-center">
                                             <span class="material-symbols-outlined text-2xl mb-1 text-text-main dark:text-white">qr_code_scanner</span>
                                            <p class="text-sm font-bold text-text-main dark:text-white">QRIS</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full flex items-center justify-center h-14 px-8 rounded-xl bg-primary hover:bg-primary-dark text-white text-lg font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5" x-bind:disabled="items.length === 0">
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
    @if(isset($table))
        window.appTableNumber = "{{ $table->table_number }}";
    @endif

function cartPage() {
    return {
        items: [],
        paymentMethod: 'cash',
        taxRate: 0,
        serviceFee: 0,
        
        get totalItems() {
            return this.items.reduce((sum, item) => sum + (Math.max(1, parseInt(item.quantity || 1, 10) || 1)), 0);
        },

        get subtotalPrice() {
            return this.items.reduce((sum, item) => sum + this.getItemSubtotal(item), 0);
        },

        get taxAmount() {
            return Math.round(this.subtotalPrice * this.taxRate);
        },
        
        get totalPrice() {
            return this.subtotalPrice + this.taxAmount + this.serviceFee;
        },
        
        formatPrice(price) {
            if (window.Cart && typeof window.Cart.formatPrice === 'function') {
                return window.Cart.formatPrice(price);
            }
            const safe = Number(price) || 0;
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(safe);
        },

        resolveImageUrl(image) {
            if (!image) return '';
            if (/^(https?:)?\/\//.test(image) || image.startsWith('/')) {
                return image;
            }
            return `/images/menus/${image}`;
        },

        getUnitPrice(item) {
            const safeItem = window.Cart && typeof window.Cart.recalculateItem === 'function'
                ? window.Cart.recalculateItem(item)
                : item;
            return Number(
                safeItem.total_price ??
                safeItem.totalPrice ??
                safeItem.final_price ??
                safeItem.finalPrice ??
                safeItem.price ??
                safeItem.base_price ??
                safeItem.basePrice ??
                0
            ) || 0;
        },

        getItemSubtotal(item) {
            const safeItem = window.Cart && typeof window.Cart.recalculateItem === 'function'
                ? window.Cart.recalculateItem(item)
                : item;
            const quantity = Math.max(1, parseInt(safeItem.quantity || 1, 10) || 1);
            return Number(safeItem.subtotal ?? (this.getUnitPrice(safeItem) * quantity)) || 0;
        },

        getAddons(item) {
            const safeItem = window.Cart && typeof window.Cart.recalculateItem === 'function'
                ? window.Cart.recalculateItem(item)
                : item;
            return Array.isArray(safeItem.addons) ? safeItem.addons : [];
        },
        
        async removeItem(index) {
            // Find item by index
            const item = this.items[index];
            if (item && item.cartItemId) {
                if (confirm('Hapus item ini dari keranjang?')) {
                    await window.Cart.remove(item.cartItemId);
                    // The cart-updated event will update this.items
                }
            }
        },
        
        init() {
            // Sync with global cart
            this.items = window.Cart.items;
            
            // Listen for global updates
            window.addEventListener('cart-updated', (e) => {
                this.items = e.detail || window.Cart.items || [];
            });
            
            // Force valid table number if present in global scope (for QR flow compatibility in cart page)
            if (window.appTableNumber) {
                 window.Cart.tableNumber = window.appTableNumber;
            }
        }
    }
}
</script>
@endpush
@endsection
