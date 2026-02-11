@extends('layouts.dashboard')

@section('title', 'Walk-in Order')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="h-[calc(100vh-4rem)] flex" x-data="manualOrder()">
    <!-- Left Side - Menu Selection -->
    <div class="flex-1 flex flex-col bg-[#f8f6f3] dark:bg-[#1a1410] overflow-hidden">
        <!-- Header -->
        <div class="p-6 bg-white dark:bg-[#2d2115] border-b border-[#f4f2f0] dark:border-[#3e2d23]">
            <h1 class="text-xl font-bold text-[#181411] dark:text-white mb-4">Walk-in Order</h1>
            
            <!-- Search Bar -->
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#897561] dark:text-[#a89c92]">search</span>
                <input 
                    type="text" 
                    x-model="searchQuery"
                    placeholder="Search menu (e.g. Latte, Croissant)" 
                    class="w-full pl-10 pr-4 py-2.5 bg-[#f4f2f0] dark:bg-[#221910] border border-transparent focus:border-primary rounded-lg text-sm text-[#181411] dark:text-white placeholder-[#897561] dark:placeholder-[#a89c92]">
            </div>
        </div>

        <!-- Category Tabs -->
        <div class="px-6 py-4 bg-white dark:bg-[#2d2115] border-b border-[#f4f2f0] dark:border-[#3e2d23]">
            <div class="flex gap-2 overflow-x-auto pb-2">
                <button @click="selectedCategory = 'all'" :class="selectedCategory === 'all' ? 'bg-primary text-white' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white hover:bg-[#e8e4df] dark:hover:bg-[#2c241b]'" class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors">
                    All Items
                </button>
                @foreach($categories as $category)
                <button @click="selectedCategory = {{ $category->id }}" :class="selectedCategory === {{ $category->id }} ? 'bg-primary text-white' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white hover:bg-[#e8e4df] dark:hover:bg-[#2c241b]'" class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors">
                    {{ $category->name }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- Menu Grid -->
        <div class="flex-1 overflow-y-auto p-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($categories as $category)
                    @foreach($category->menus as $menu)
                    <div x-show="selectedCategory === 'all' || selectedCategory === {{ $category->id }}" class="bg-white dark:bg-[#2d2115] rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all cursor-pointer group" @click="openMenuDetail({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, '{{ addslashes($category->name) }}', '{{ addslashes($menu->description ?? '') }}', '{{ $menu->image_url ?? '' }}', '{{ $category->slug }}')">
                        <!-- Image -->
                        <div class="aspect-square bg-gradient-to-br from-[#fdfbf7] to-[#f4f2f0] dark:from-[#221910] dark:to-[#2c241b] relative overflow-hidden">
                            @if($menu->image_url)
                            <img src="{{ asset('storage/' . $menu->image_url) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @else
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-[64px] text-[#897561] dark:text-[#a89c92]">restaurant</span>
                            </div>
                            @endif
                            
                            <!-- Category Badge -->
                            <div class="absolute top-2 left-2 px-2 py-1 bg-white/90 dark:bg-[#2d2115]/90 backdrop-blur-sm rounded-full">
                                <span class="text-[10px] font-medium text-[#897561] dark:text-[#a89c92]">{{ $category->name }}</span>
                            </div>
                        </div>
                        
                        <!-- Info -->
                        <div class="p-3">
                            <h3 class="font-bold text-[#181411] dark:text-white text-sm mb-1 line-clamp-1">{{ $menu->name }}</h3>
                            <p class="text-xs text-[#897561] dark:text-[#a89c92] mb-2 line-clamp-2">{{ $menu->description ?? 'Delicious menu item' }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-[#181411] dark:text-white">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                <button class="size-7 rounded-full bg-primary text-white flex items-center justify-center hover:bg-primary-dark transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">add</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

    <!-- Right Side - Current Order -->
    <div class="w-[420px] bg-white dark:bg-[#2d2115] border-l border-[#f4f2f0] dark:border-[#3e2d23] flex flex-col h-full overflow-hidden">
        <!-- Header - Fixed at top -->
        <div class="flex-shrink-0 p-6 border-b border-[#f4f2f0] dark:border-[#3e2d23]">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-[#181411] dark:text-white">Current Order</h2>
                <div class="flex items-center gap-2">
                    <span x-show="items.length > 0" class="px-3 py-1 bg-primary text-white rounded-full text-xs font-bold animate-pulse">
                        <span x-text="items.length"></span> items
                    </span>
                    <span class="px-2 py-1 bg-primary/10 text-primary rounded-full text-xs font-medium">#<span x-text="orderNumber"></span></span>
                </div>
            </div>

            <!-- Table Selection -->
            <div class="space-y-2">
                <label class="text-xs font-medium text-[#897561] dark:text-[#a89c92]">Table</label>
                <select x-model="tableNumber" required class="w-full px-3 py-2 bg-[#f4f2f0] dark:bg-[#221910] border border-transparent focus:border-primary rounded-lg text-sm text-[#181411] dark:text-white">
                    <option value="">Select Table</option>
                    @foreach($tables as $table)
                    <option value="{{ $table->table_number }}">Table {{ $table->table_number }} ({{ $table->capacity }} seats)</option>
                    @endforeach
                </select>
            </div>

            <!-- Customer Info (Optional) -->
            <div class="mt-3 space-y-2">
                <label class="text-xs font-medium text-[#897561] dark:text-[#a89c92]">Customer Name</label>
                <input 
                    type="text" 
                    x-model="customerName"
                    placeholder="Guest" 
                    class="w-full px-3 py-2 bg-[#f4f2f0] dark:bg-[#221910] border border-transparent focus:border-primary rounded-lg text-sm text-[#181411] dark:text-white placeholder-[#897561]">
            </div>
        </div>

        <!-- Order Items - Scrollable Section -->
        <div class="flex-1 overflow-y-auto min-h-0">
            <div class="p-4 space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="flex items-start gap-2 p-3 bg-[#fdfbf7] dark:bg-[#221910] rounded-xl shadow-sm">
                        <!-- Item Image Placeholder -->
                        <div class="size-10 rounded-lg bg-gradient-to-br from-primary/20 to-primary/10 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-primary text-[18px]">restaurant</span>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-sm text-[#181411] dark:text-white mb-0.5" x-text="item.name"></h4>
                            <p class="text-[10px] text-[#897561] dark:text-[#a89c92] mb-1" x-text="item.category"></p>
                            
                            <!-- Notes (if any) -->
                            <template x-if="item.notes">
                                <div class="text-[9px] text-[#897561] dark:text-[#a89c92] mb-2 bg-white dark:bg-[#2d2115] px-2 py-1 rounded leading-relaxed">
                                    <div class="font-semibold text-[#181411] dark:text-white mb-0.5">🔔 Notes:</div>
                                    <div x-html="item.notes.replace(/\|/g, '<br>• ')"></div>
                                </div>
                            </template>
                            
                            <div class="flex items-center justify-between mt-2">
                                <!-- Quantity Controls -->
                                <div class="flex items-center gap-1">
                                    <button type="button" @click="decreaseQuantity(index)" class="size-6 rounded bg-white dark:bg-[#2d2115] border border-[#e8e4df] dark:border-[#3e2d23] flex items-center justify-center hover:border-primary transition-colors">
                                        <span class="material-symbols-outlined text-[14px] text-[#181411] dark:text-white">remove</span>
                                    </button>
                                    <span class="text-sm font-bold text-[#181411] dark:text-white w-8 text-center" x-text="item.quantity"></span>
                                    <button type="button" @click="increaseQuantity(index)" class="size-6 rounded bg-white dark:bg-[#2d2115] border border-[#e8e4df] dark:border-[#3e2d23] flex items-center justify-center hover:border-primary transition-colors">
                                        <span class="material-symbols-outlined text-[14px] text-[#181411] dark:text-white">add</span>
                                    </button>
                                </div>
                                
                                <!-- Price & Remove -->
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-[#181411] dark:text-white">Rp <span x-text="formatPrice(item.price * item.quantity)"></span></span>
                                    <button type="button" @click="removeItem(index)" class="size-6 rounded bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 flex items-center justify-center hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                                        <span class="material-symbols-outlined text-[14px]">close</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Empty State -->
                <div x-show="items.length === 0" class="text-center py-16">
                    <div class="size-16 mx-auto rounded-full bg-[#f4f2f0] dark:bg-[#221910] flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-[#897561] dark:text-[#a89c92] text-[32px]">shopping_cart</span>
                    </div>
                    <p class="text-sm font-medium text-[#897561] dark:text-[#a89c92]">No items added yet</p>
                    <p class="text-xs text-[#897561] dark:text-[#a89c92] mt-1">Click on menu items to add</p>
                </div>
            </div>
        </div>

        <!-- Order Summary & Actions - Fixed at bottom -->
        <div class="flex-shrink-0 p-4 border-t-2 border-[#f4f2f0] dark:border-[#3e2d23] bg-[#f8f6f3] dark:bg-[#1a1410] space-y-3">
            <!-- Totals -->
            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-[#897561] dark:text-[#a89c92]">Subtotal</span>
                    <span class="font-medium text-[#181411] dark:text-white">Rp <span x-text="formatPrice(calculateSubtotal())"></span></span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-[#897561] dark:text-[#a89c92]">Tax (5%)</span>
                    <span class="font-medium text-[#181411] dark:text-white">Rp <span x-text="formatPrice(calculateTax())"></span></span>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-[#f4f2f0] dark:border-[#3e2d23]">
                    <span class="font-bold text-[#181411] dark:text-white">Total</span>
                    <span class="text-xl font-bold text-[#181411] dark:text-white">Rp <span x-text="formatPrice(calculateTotal())"></span></span>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="grid grid-cols-3 gap-2">
                <button type="button" @click="paymentMethod = 'cash'" :class="paymentMethod === 'cash' ? 'bg-primary text-white' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white'" class="px-3 py-3 rounded-lg font-medium transition-colors flex flex-col items-center gap-1">
                    <span class="material-symbols-outlined text-[20px]">payments</span>
                    <span class="text-[10px]">CASH</span>
                </button>
                <button type="button" @click="paymentMethod = 'card'" :class="paymentMethod === 'card' ? 'bg-primary text-white' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white'" class="px-3 py-3 rounded-lg font-medium transition-colors flex flex-col items-center gap-1">
                    <span class="material-symbols-outlined text-[20px]">credit_card</span>
                    <span class="text-[10px]">CARD</span>
                </button>
                <button type="button" @click="paymentMethod = 'qris'" :class="paymentMethod === 'qris' ? 'bg-primary text-white' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white'" class="px-3 py-3 rounded-lg font-medium transition-colors flex flex-col items-center gap-1">
                    <span class="material-symbols-outlined text-[20px]">qr_code</span>
                    <span class="text-[10px]">QRIS</span>
                </button>
            </div>

            <!-- Error Message -->
            <div x-show="errorMessage" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <p class="text-sm text-red-600 dark:text-red-400" x-text="errorMessage"></p>
            </div>

            <!-- Place Order Button -->
            <button 
                type="button"
                onclick="handlePlaceOrderClick()"
                class="w-full px-4 py-4 text-white rounded-xl transition-all font-bold text-base flex items-center justify-center gap-2 group bg-gradient-to-r from-primary to-orange-600 shadow-lg shadow-primary/30 hover:shadow-xl hover:shadow-primary/40 cursor-pointer">
                <span class="material-symbols-outlined text-[24px] animate-bounce">shopping_cart_checkout</span>
                <span>Place Order</span>
                <span class="text-sm font-normal opacity-90" x-show="items.length > 0">• Rp <span x-text="formatPrice(calculateTotal())"></span></span>
            </button>
            
            <script>
            function handlePlaceOrderClick() {
                // Get the Alpine.js component data
                const mainComponent = document.querySelector('[x-data="manualOrder()"]');
                
                if (mainComponent && mainComponent._x_dataStack && mainComponent._x_dataStack[0]) {
                    const data = mainComponent._x_dataStack[0];
                    
                    // Validation
                    if (data.items.length === 0) {
                        showErrorModal({
                            title: 'Item Kosong',
                            message: 'Silakan tambahkan minimal 1 item',
                            icon: '🛒'
                        });
                        return;
                    }
                    if (!data.tableNumber) {
                        showErrorModal({
                            title: 'Meja Belum Dipilih',
                            message: 'Silakan pilih nomor meja',
                            icon: '🪑'
                        });
                        return;
                    }
                    if (!data.paymentMethod) {
                        showErrorModal({
                            title: 'Metode Pembayaran',
                            message: 'Silakan pilih metode pembayaran',
                            icon: '💳'
                        });
                        return;
                    }
                    
                    console.log('Making API request to create order...');
                    
                    // Direct API call
                    fetch('/cashier/manual-order', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            table_number: data.tableNumber,
                            customer_name: data.customerName || 'Guest', 
                            payment_method: data.paymentMethod,
                            items: data.items.map(item => ({
                                menu_id: item.menu_id,
                                quantity: item.quantity,
                                price: item.price,
                                notes: item.notes || ''
                            }))
                        })
                    })
                    .then(response => {
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Show modern success modal instead of alert
                            showSuccessModal({
                                title: 'Order Berhasil Dibuat!',
                                message: 'Order siap diproses oleh dapur.',
                                icon: '✅'
                            });
                            
                            // Reset form
                            const alpineData = mainComponent._x_dataStack[0];
                            alpineData.items = [];
                            alpineData.tableNumber = '';
                            alpineData.customerName = 'Guest';
                            alpineData.paymentMethod = '';
                        } else {
                            // Show modern error modal
                            showErrorModal({
                                title: 'Gagal Membuat Order',
                                message: data.message || 'Terjadi kesalahan',
                                icon: '❌'
                            });
                        }
                    })
                    .catch(error => {
                        showErrorModal({
                            title: 'Network Error',
                            message: 'Koneksi bermasalah: ' + error.message,
                            icon: '⚠️'
                        });
                    });
                    
                } else {
                    showErrorModal({
                        title: 'System Error',
                        message: 'Alpine.js tidak ditemukan. Silakan refresh halaman.',
                        icon: '⚠️'
                    });
                }
            }
            
            // Modern Modal Functions
            function showSuccessModal(options) {
                createModal({
                    ...options,
                    type: 'success',
                    bgColor: 'bg-green-50 dark:bg-green-900/20',
                    borderColor: 'border-green-200 dark:border-green-800',
                    iconBg: 'bg-green-100 dark:bg-green-800',
                    buttonBg: 'bg-green-600 hover:bg-green-700',
                    autoClose: true
                });
            }
            
            function showErrorModal(options) {
                createModal({
                    ...options,
                    type: 'error',
                    bgColor: 'bg-red-50 dark:bg-red-900/20',
                    borderColor: 'border-red-200 dark:border-red-800',
                    iconBg: 'bg-red-100 dark:bg-red-800',
                    buttonBg: 'bg-red-600 hover:bg-red-700',
                    autoClose: false
                });
            }
            
            function createModal(options) {
                // Remove existing modal if any
                const existingModal = document.getElementById('customModal');
                if (existingModal) {
                    existingModal.remove();
                }
                
                // Create modal HTML
                const modal = document.createElement('div');
                modal.id = 'customModal';
                modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4';
                modal.style.opacity = '0';
                modal.style.transition = 'opacity 0.3s ease';
                
                modal.innerHTML = `
                    <div class="relative bg-white dark:bg-[#1a1612] rounded-2xl shadow-2xl max-w-md w-full transform transition-all" 
                         style="transform: scale(0.9); transition: transform 0.3s ease, opacity 0.3s ease;">
                        <!-- Modal Content -->
                        <div class="p-6">
                            <!-- Icon -->
                            <div class="flex justify-center mb-4">
                                <div class="w-16 h-16 ${options.iconBg} rounded-full flex items-center justify-center">
                                    <span class="text-2xl">${options.icon}</span>
                                </div>
                            </div>
                            
                            <!-- Title -->
                            <h3 class="text-xl font-bold text-center text-[#181411] dark:text-white mb-3">
                                ${options.title}
                            </h3>
                            
                            <!-- Message -->
                            <p class="text-center text-[#897561] dark:text-[#a89c92] mb-6 leading-relaxed">
                                ${options.message}
                            </p>
                            
                            <!-- Button -->
                            <button 
                                onclick="closeModal()"
                                class="${options.buttonBg} w-full px-4 py-3 text-white rounded-xl font-medium transition-all hover:shadow-lg">
                                ${options.type === 'success' ? 'Lanjutkan' : 'Tutup'}
                            </button>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(modal);
                
                // Animate in
                requestAnimationFrame(() => {
                    modal.style.opacity = '1';
                    const modalContent = modal.querySelector('div > div');
                    modalContent.style.transform = 'scale(1)';
                });
                
                // Auto close for success
                if (options.autoClose && options.type === 'success') {
                    setTimeout(() => {
                        closeModal(true); // true = refresh page
                    }, 2500);
                }
            }
            
            function closeModal(refresh = false) {
                const modal = document.getElementById('customModal');
                if (modal) {
                    modal.style.opacity = '0';
                    const modalContent = modal.querySelector('div > div');
                    modalContent.style.transform = 'scale(0.9)';
                    
                    setTimeout(() => {
                        modal.remove();
                        if (refresh) {
                            window.location.reload();
                        }
                    }, 300);
                }
            }
            </script>
        </div>
    </div>

    <!-- Menu Detail Modal -->
    <div x-show="showMenuDetail" x-cloak @click.self="closeMenuDetail()" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="closeMenuDetail()" class="relative bg-white dark:bg-[#1a1612] rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto transform transition-all" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <!-- Close Button -->
            <button @click="closeMenuDetail()" class="absolute top-4 right-4 z-10 p-2 bg-white/90 dark:bg-[#2c241b]/90 backdrop-blur-sm rounded-full hover:bg-white dark:hover:bg-[#2c241b] transition-colors">
                <span class="material-symbols-outlined text-[#897561]">close</span>
            </button>

            <!-- Menu Image -->
            <div class="aspect-[16/10] bg-gradient-to-br from-[#fdfbf7] to-[#f4f2f0] dark:from-[#221910] dark:to-[#2c241b] relative overflow-hidden">
                <template x-if="selectedMenu.image">
                    <img :src="'/storage/' + selectedMenu.image" :alt="selectedMenu.name" class="w-full h-full object-cover">
                </template>
                <template x-if="!selectedMenu.image">
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-[120px] text-[#897561] dark:text-[#a89c92]">restaurant</span>
                    </div>
                </template>
                
                <!-- Category Badge -->
                <div class="absolute top-4 left-4 px-3 py-1.5 bg-white/90 dark:bg-[#2d2115]/90 backdrop-blur-sm rounded-full">
                    <span class="text-xs font-semibold text-[#897561] dark:text-[#a89c92]" x-text="selectedMenu.category"></span>
                </div>
            </div>

            <!-- Menu Details -->
            <div class="p-6">
                <!-- Title & Price -->
                <div class="mb-4">
                    <h2 class="text-2xl font-black text-[#181411] dark:text-white mb-2" x-text="selectedMenu.name"></h2>
                    <div class="flex items-center gap-3">
                        <span class="text-2xl font-black text-primary">Rp <span x-text="formatPrice(selectedMenu.price)"></span></span>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <h3 class="text-sm font-bold text-[#181411] dark:text-white mb-2">Description</h3>
                    <p class="text-sm text-[#897561] dark:text-[#a89c92] leading-relaxed" x-text="selectedMenu.description || 'Delicious menu item made with care and quality ingredients.'"></p>
                </div>

                <!-- BEVERAGE OPTIONS -->
                <div x-show="selectedMenu.type === 'beverage'" class="space-y-5 mb-6">
                    <!-- Temperature Selection -->
                    <div>
                        <h3 class="text-sm font-bold text-[#181411] dark:text-white mb-3">Temperature</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <button @click="temperature = 'ice'" :class="temperature === 'ice' ? 'bg-primary text-white border-primary' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white border-[#e8e4df] dark:border-[#3e2d23]'" class="px-4 py-3 rounded-xl border-2 font-semibold transition-all hover:border-primary flex items-center justify-center gap-2">
                                <span class="text-xl">❄️</span>
                                <span>Ice</span>
                            </button>
                            <button @click="temperature = 'hot'" :class="temperature === 'hot' ? 'bg-primary text-white border-primary' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white border-[#e8e4df] dark:border-[#3e2d23]'" class="px-4 py-3 rounded-xl border-2 font-semibold transition-all hover:border-primary flex items-center justify-center gap-2">
                                <span class="text-xl">🔥</span>
                                <span>Hot</span>
                            </button>
                        </div>
                    </div>

                    <!-- Ice Level (only show when ice is selected) -->
                    <div x-show="temperature === 'ice'">
                        <h3 class="text-sm font-bold text-[#181411] dark:text-white mb-3">Ice Level</h3>
                        <div class="grid grid-cols-3 gap-2">
                            <button @click="iceLevel = 'normal'" :class="iceLevel === 'normal' ? 'bg-primary text-white border-primary' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white border-[#e8e4df] dark:border-[#3e2d23]'" class="px-3 py-2.5 rounded-lg border-2 text-xs font-semibold transition-all hover:border-primary">Normal</button>
                            <button @click="iceLevel = 'less'" :class="iceLevel === 'less' ? 'bg-primary text-white border-primary' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white border-[#e8e4df] dark:border-[#3e2d23]'" class="px-3 py-2.5 rounded-lg border-2 text-xs font-semibold transition-all hover:border-primary">Less Ice</button>
                            <button @click="iceLevel = 'no-ice'" :class="iceLevel === 'no-ice' ? 'bg-primary text-white border-primary' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white border-[#e8e4df] dark:border-[#3e2d23]'" class="px-3 py-2.5 rounded-lg border-2 text-xs font-semibold transition-all hover:border-primary">No Ice</button>
                        </div>
                    </div>

                    <!-- Sugar Level -->
                    <div>
                        <h3 class="text-sm font-bold text-[#181411] dark:text-white mb-3">Sugar Level</h3>
                        <div class="grid grid-cols-3 gap-2">
                            <button @click="sugarLevel = 'normal'" :class="sugarLevel === 'normal' ? 'bg-primary text-white border-primary' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white border-[#e8e4df] dark:border-[#3e2d23]'" class="px-3 py-2.5 rounded-lg border-2 text-xs font-semibold transition-all hover:border-primary">Normal</button>
                            <button @click="sugarLevel = 'less'" :class="sugarLevel === 'less' ? 'bg-primary text-white border-primary' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white border-[#e8e4df] dark:border-[#3e2d23]'" class="px-3 py-2.5 rounded-lg border-2 text-xs font-semibold transition-all hover:border-primary">Less Sugar</button>
                            <button @click="sugarLevel = 'no-sugar'" :class="sugarLevel === 'no-sugar' ? 'bg-primary text-white border-primary' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white border-[#e8e4df] dark:border-[#3e2d23]'" class="px-3 py-2.5 rounded-lg border-2 text-xs font-semibold transition-all hover:border-primary">No Sugar</button>
                        </div>
                    </div>

                    <!-- Size Selection -->
                    <div>
                        <h3 class="text-sm font-bold text-[#181411] dark:text-white mb-3">Size</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <button @click="size = 'regular'" :class="size === 'regular' ? 'bg-primary text-white border-primary' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white border-[#e8e4df] dark:border-[#3e2d23]'" class="px-4 py-3 rounded-xl border-2 transition-all hover:border-primary">
                                <div class="text-center">
                                    <div class="font-semibold">Regular</div>
                                    <div class="text-xs opacity-70">Standard</div>
                                </div>
                            </button>
                            <button @click="size = 'large'" :class="size === 'large' ? 'bg-primary text-white border-primary' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white border-[#e8e4df] dark:border-[#3e2d23]'" class="px-4 py-3 rounded-xl border-2 transition-all hover:border-primary">
                                <div class="text-center">
                                    <div class="font-semibold">Large</div>
                                    <div class="text-xs opacity-70">+Rp 8.000</div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Beverage Add-ons -->
                    <div>
                        <h3 class="text-sm font-bold text-[#181411] dark:text-white mb-3">Add-ons (Optional)</h3>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 p-3 bg-[#f4f2f0] dark:bg-[#221910] rounded-lg cursor-pointer hover:bg-[#e8e4df] dark:hover:bg-[#2c241b] transition-colors">
                                <input type="checkbox" value="extra-shot" x-model="addOns" class="size-5 rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="flex-1 text-sm text-[#181411] dark:text-white">Extra Shot</span>
                                <span class="text-xs font-semibold text-primary">+Rp 5.000</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-[#f4f2f0] dark:bg-[#221910] rounded-lg cursor-pointer hover:bg-[#e8e4df] dark:hover:bg-[#2c241b] transition-colors">
                                <input type="checkbox" value="whipped-cream" x-model="addOns" class="size-5 rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="flex-1 text-sm text-[#181411] dark:text-white">Whipped Cream</span>
                                <span class="text-xs font-semibold text-primary">+Rp 3.000</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-[#f4f2f0] dark:bg-[#221910] rounded-lg cursor-pointer hover:bg-[#e8e4df] dark:hover:bg-[#2c241b] transition-colors">
                                <input type="checkbox" value="caramel-syrup" x-model="addOns" class="size-5 rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="flex-1 text-sm text-[#181411] dark:text-white">Caramel Syrup</span>
                                <span class="text-xs font-semibold text-primary">+Rp 3.000</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- FOOD OPTIONS -->
                <div x-show="selectedMenu.type === 'food'" class="space-y-5 mb-6">
                    <!-- Spice Level -->
                    <div>
                        <h3 class="text-sm font-bold text-[#181411] dark:text-white mb-3">Spice Level</h3>
                        <div class="grid grid-cols-3 gap-2">
                            <button @click="spiceLevel = 'mild'" :class="spiceLevel === 'mild' ? 'bg-primary text-white border-primary' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white border-[#e8e4df] dark:border-[#3e2d23]'" class="px-3 py-2.5 rounded-lg border-2 text-xs font-semibold transition-all hover:border-primary">🫑 Mild</button>
                            <button @click="spiceLevel = 'medium'" :class="spiceLevel === 'medium' ? 'bg-primary text-white border-primary' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white border-[#e8e4df] dark:border-[#3e2d23]'" class="px-3 py-2.5 rounded-lg border-2 text-xs font-semibold transition-all hover:border-primary">🌶️ Medium</button>
                            <button @click="spiceLevel = 'spicy'" :class="spiceLevel === 'spicy' ? 'bg-primary text-white border-primary' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white border-[#e8e4df] dark:border-[#3e2d23]'" class="px-3 py-2.5 rounded-lg border-2 text-xs font-semibold transition-all hover:border-primary">🔥 Spicy</button>
                        </div>
                    </div>

                    <!-- Portion Size -->
                    <div>
                        <h3 class="text-sm font-bold text-[#181411] dark:text-white mb-3">Portion</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <button @click="portion = 'regular'" :class="portion === 'regular' ? 'bg-primary text-white border-primary' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white border-[#e8e4df] dark:border-[#3e2d23]'" class="px-4 py-3 rounded-xl border-2 transition-all hover:border-primary">
                                <div class="text-center">
                                    <div class="font-semibold">Regular</div>
                                    <div class="text-xs opacity-70">Standard</div>
                                </div>
                            </button>
                            <button @click="portion = 'large'" :class="portion === 'large' ? 'bg-primary text-white border-primary' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white border-[#e8e4df] dark:border-[#3e2d23]'" class="px-4 py-3 rounded-xl border-2 transition-all hover:border-primary">
                                <div class="text-center">
                                    <div class="font-semibold">Large</div>
                                    <div class="text-xs opacity-70">+Rp 5.000</div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Food Add-ons -->
                    <div>
                        <h3 class="text-sm font-bold text-[#181411] dark:text-white mb-3">Add-ons (Optional)</h3>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 p-3 bg-[#f4f2f0] dark:bg-[#221910] rounded-lg cursor-pointer hover:bg-[#e8e4df] dark:hover:bg-[#2c241b] transition-colors">
                                <input type="checkbox" value="extra-cheese" x-model="addOns" class="size-5 rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="flex-1 text-sm text-[#181411] dark:text-white">Extra Cheese</span>
                                <span class="text-xs font-semibold text-primary">+Rp 5.000</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-[#f4f2f0] dark:bg-[#221910] rounded-lg cursor-pointer hover:bg-[#e8e4df] dark:hover:bg-[#2c241b] transition-colors">
                                <input type="checkbox" value="extra-egg" x-model="addOns" class="size-5 rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="flex-1 text-sm text-[#181411] dark:text-white">Extra Egg</span>
                                <span class="text-xs font-semibold text-primary">+Rp 3.000</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-[#f4f2f0] dark:bg-[#221910] rounded-lg cursor-pointer hover:bg-[#e8e4df] dark:hover:bg-[#2c241b] transition-colors">
                                <input type="checkbox" value="extra-rice" x-model="addOns" class="size-5 rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="flex-1 text-sm text-[#181411] dark:text-white">Extra Rice</span>
                                <span class="text-xs font-semibold text-primary">+Rp 5.000</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- SNACK OPTIONS -->
                <div x-show="selectedMenu.type === 'snack'" class="space-y-5 mb-6">
                    <!-- Size Selection -->
                    <div>
                        <h3 class="text-sm font-bold text-[#181411] dark:text-white mb-3">Size</h3>
                        <div class="grid grid-cols-3 gap-2">
                            <button @click="size = 'small'" :class="size === 'small' ? 'bg-primary text-white border-primary' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white border-[#e8e4df] dark:border-[#3e2d23]'" class="px-3 py-3 rounded-xl border-2 transition-all hover:border-primary">
                                <div class="text-center">
                                    <div class="font-semibold text-sm">Small</div>
                                    <div class="text-xs opacity-70">-Rp 5.000</div>
                                </div>
                            </button>
                            <button @click="size = 'regular'" :class="size === 'regular' ? 'bg-primary text-white border-primary' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white border-[#e8e4df] dark:border-[#3e2d23]'" class="px-3 py-3 rounded-xl border-2 transition-all hover:border-primary">
                                <div class="text-center">
                                    <div class="font-semibold text-sm">Regular</div>
                                    <div class="text-xs opacity-70">Standard</div>
                                </div>
                            </button>
                            <button @click="size = 'large'" :class="size === 'large' ? 'bg-primary text-white border-primary' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white border-[#e8e4df] dark:border-[#3e2d23]'" class="px-3 py-3 rounded-xl border-2 transition-all hover:border-primary">
                                <div class="text-center">
                                    <div class="font-semibold text-sm">Large</div>
                                    <div class="text-xs opacity-70">+Rp 5.000</div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Sauce Options -->
                    <div>
                        <h3 class="text-sm font-bold text-[#181411] dark:text-white mb-3">Sauce Options</h3>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 p-3 bg-[#f4f2f0] dark:bg-[#221910] rounded-lg cursor-pointer hover:bg-[#e8e4df] dark:hover:bg-[#2c241b] transition-colors">
                                <input type="checkbox" value="ketchup" x-model="sauces" class="size-5 rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="flex-1 text-sm text-[#181411] dark:text-white">Ketchup</span>
                                <span class="text-xs font-semibold text-green-600">Free</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-[#f4f2f0] dark:bg-[#221910] rounded-lg cursor-pointer hover:bg-[#e8e4df] dark:hover:bg-[#2c241b] transition-colors">
                                <input type="checkbox" value="mayo" x-model="sauces" class="size-5 rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="flex-1 text-sm text-[#181411] dark:text-white">Mayonnaise</span>
                                <span class="text-xs font-semibold text-green-600">Free</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-[#f4f2f0] dark:bg-[#221910] rounded-lg cursor-pointer hover:bg-[#e8e4df] dark:hover:bg-[#2c241b] transition-colors">
                                <input type="checkbox" value="chili" x-model="sauces" class="size-5 rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="flex-1 text-sm text-[#181411] dark:text-white">Chili Sauce</span>
                                <span class="text-xs font-semibold text-green-600">Free</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-[#f4f2f0] dark:bg-[#221910] rounded-lg cursor-pointer hover:bg-[#e8e4df] dark:hover:bg-[#2c241b] transition-colors">
                                <input type="checkbox" value="bbq" x-model="sauces" class="size-5 rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="flex-1 text-sm text-[#181411] dark:text-white">BBQ Sauce</span>
                                <span class="text-xs font-semibold text-primary">+Rp 2.000</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- DESSERT OPTIONS -->
                <div x-show="selectedMenu.type === 'dessert'" class="space-y-5 mb-6">
                    <!-- Portion Size -->
                    <div>
                        <h3 class="text-sm font-bold text-[#181411] dark:text-white mb-3">Portion</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <button @click="portion = 'regular'" :class="portion === 'regular' ? 'bg-primary text-white border-primary' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white border-[#e8e4df] dark:border-[#3e2d23]'" class="px-4 py-3 rounded-xl border-2 transition-all hover:border-primary">
                                <div class="text-center">
                                    <div class="font-semibold">Regular</div>
                                    <div class="text-xs opacity-70">Standard</div>
                                </div>
                            </button>
                            <button @click="portion = 'large'" :class="portion === 'large' ? 'bg-primary text-white border-primary' : 'bg-[#f4f2f0] dark:bg-[#221910] text-[#181411] dark:text-white border-[#e8e4df] dark:border-[#3e2d23]'" class="px-4 py-3 rounded-xl border-2 transition-all hover:border-primary">
                                <div class="text-center">
                                    <div class="font-semibold">Large</div>
                                    <div class="text-xs opacity-70">+Rp 5.000</div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Toppings -->
                    <div>
                        <h3 class="text-sm font-bold text-[#181411] dark:text-white mb-3">Toppings (Optional)</h3>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 p-3 bg-[#f4f2f0] dark:bg-[#221910] rounded-lg cursor-pointer hover:bg-[#e8e4df] dark:hover:bg-[#2c241b] transition-colors">
                                <input type="checkbox" value="whipped-cream" x-model="toppings" class="size-5 rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="flex-1 text-sm text-[#181411] dark:text-white">Whipped Cream</span>
                                <span class="text-xs font-semibold text-primary">+Rp 3.000</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-[#f4f2f0] dark:bg-[#221910] rounded-lg cursor-pointer hover:bg-[#e8e4df] dark:hover:bg-[#2c241b] transition-colors">
                                <input type="checkbox" value="chocolate-chips" x-model="toppings" class="size-5 rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="flex-1 text-sm text-[#181411] dark:text-white">Chocolate Chips</span>
                                <span class="text-xs font-semibold text-primary">+Rp 4.000</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-[#f4f2f0] dark:bg-[#221910] rounded-lg cursor-pointer hover:bg-[#e8e4df] dark:hover:bg-[#2c241b] transition-colors">
                                <input type="checkbox" value="caramel-drizzle" x-model="toppings" class="size-5 rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="flex-1 text-sm text-[#181411] dark:text-white">Caramel Drizzle</span>
                                <span class="text-xs font-semibold text-primary">+Rp 3.000</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Quantity -->
                <div class="mb-6">
                    <h3 class="text-sm font-bold text-[#181411] dark:text-white mb-3">Quantity</h3>
                    <div class="flex items-center gap-4">
                        <button @click="modalQuantity > 1 ? modalQuantity-- : null" class="size-10 rounded-lg bg-[#f4f2f0] dark:bg-[#221910] border border-[#e8e4df] dark:border-[#3e2d23] flex items-center justify-center hover:border-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px] text-[#181411] dark:text-white">remove</span>
                        </button>
                        <span class="text-xl font-bold text-[#181411] dark:text-white w-12 text-center" x-text="modalQuantity"></span>
                        <button @click="modalQuantity++" class="size-10 rounded-lg bg-[#f4f2f0] dark:bg-[#221910] border border-[#e8e4df] dark:border-[#3e2d23] flex items-center justify-center hover:border-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px] text-[#181411] dark:text-white">add</span>
                        </button>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <h3 class="text-sm font-bold text-[#181411] dark:text-white mb-3">Special Instructions (Optional)</h3>
                    <textarea x-model="modalNotes" rows="3" placeholder="e.g., Extra hot, no sugar, etc." class="w-full px-4 py-3 bg-[#f4f2f0] dark:bg-[#221910] border border-transparent focus:border-primary rounded-lg text-sm text-[#181411] dark:text-white placeholder-[#897561] dark:placeholder-[#a89c92] resize-none"></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <button @click="closeMenuDetail()" class="flex-1 px-6 py-3.5 bg-gray-100 dark:bg-[#2c241b] hover:bg-gray-200 dark:hover:bg-[#3d362e] text-[#181411] dark:text-white font-semibold rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button @click="addItemFromModal()" class="flex-1 px-6 py-3.5 bg-primary hover:bg-orange-600 text-white font-bold rounded-xl transition-colors shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
                        <span>Add to Order</span>
                        <span class="text-sm font-normal opacity-90">• Rp <span x-text="formatPrice(calculateItemPrice() * modalQuantity)"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Order Confirmation Modal -->
<x-order-confirm-modal />

<!-- Success Modal Component -->
<x-success-modal />

@push('scripts')
<script>
function manualOrder() {
    return {
        selectedCategory: 'all',
        searchQuery: '',
        tableNumber: '',
        customerName: 'Guest',
        paymentMethod: '',
        items: [],
        errorMessage: '',
        orderNumber: Math.floor(Math.random() * 10000),
        
        // Menu detail modal
        showMenuDetail: false,
        selectedMenu: {},
        modalQuantity: 1,
        modalNotes: '',
        
        // Product customization options
        temperature: 'ice',
        iceLevel: 'normal',
        sugarLevel: 'normal',
        size: 'regular',
        spiceLevel: 'mild',
        portion: 'regular',
        addOns: [],
        sauces: [],
        toppings: [],

        getProductType(categorySlug) {
            const slugLower = categorySlug.toLowerCase();
            if (slugLower.includes('coffee') || slugLower.includes('non-coffee') || slugLower.includes('drink') || slugLower.includes('beverage')) {
                return 'beverage';
            } else if (slugLower.includes('food')) {
                return 'food';
            } else if (slugLower.includes('snack')) {
                return 'snack';
            } else if (slugLower.includes('dessert')) {
                return 'dessert';
            }
            return 'other';
        },

        resetOptions() {
            this.temperature = 'ice';
            this.iceLevel = 'normal';
            this.sugarLevel = 'normal';
            this.size = 'regular';
            this.spiceLevel = 'mild';
            this.portion = 'regular';
            this.addOns = [];
            this.sauces = [];
            this.toppings = [];
            this.modalQuantity = 1;
            this.modalNotes = '';
        },

        openMenuDetail(menuId, name, price, category, description, image, categorySlug) {
            const productType = this.getProductType(categorySlug);
            this.selectedMenu = {
                id: menuId,
                name: name,
                price: price,
                category: category,
                description: description,
                image: image,
                type: productType
            };
            this.resetOptions();
            this.showMenuDetail = true;
            document.body.style.overflow = 'hidden';
        },

        closeMenuDetail() {
            this.showMenuDetail = false;
            document.body.style.overflow = 'auto';
        },

        calculateItemPrice() {
            let basePrice = this.selectedMenu.price;
            let addOnPrice = 0;

            // Calculate add-on prices based on product type
            if (this.selectedMenu.type === 'beverage') {
                // Size upgrade
                if (this.size === 'large') addOnPrice += 8000;
                
                // Beverage add-ons
                if (this.addOns.includes('extra-shot')) addOnPrice += 5000;
                if (this.addOns.includes('whipped-cream')) addOnPrice += 3000;
                if (this.addOns.includes('caramel-syrup')) addOnPrice += 3000;
            } else if (this.selectedMenu.type === 'food') {
                // Portion upgrade
                if (this.portion === 'large') addOnPrice += 5000;
                
                // Food add-ons
                if (this.addOns.includes('extra-cheese')) addOnPrice += 5000;
                if (this.addOns.includes('extra-egg')) addOnPrice += 3000;
                if (this.addOns.includes('extra-rice')) addOnPrice += 5000;
            } else if (this.selectedMenu.type === 'snack') {
                // Size adjustment
                if (this.size === 'small') addOnPrice -= 5000;
                if (this.size === 'large') addOnPrice += 5000;
                
                // BBQ sauce
                if (this.sauces.includes('bbq')) addOnPrice += 2000;
            } else if (this.selectedMenu.type === 'dessert') {
                // Portion upgrade
                if (this.portion === 'large') addOnPrice += 5000;
                
                // Toppings
                if (this.toppings.includes('whipped-cream')) addOnPrice += 3000;
                if (this.toppings.includes('chocolate-chips')) addOnPrice += 4000;
                if (this.toppings.includes('caramel-drizzle')) addOnPrice += 3000;
            }

            return basePrice + addOnPrice;
        },

        formatItemNotes() {
            let notesParts = [];
            
            if (this.selectedMenu.type === 'beverage') {
                // Temperature
                if (this.temperature === 'ice') {
                    notesParts.push(`❄️ Ice - ${this.iceLevel}`);
                } else {
                    notesParts.push('🔥 Hot');
                }
                
                // Sugar level
                if (this.sugarLevel !== 'normal') {
                    const sugarLabels = {'less': 'Less Sugar', 'no-sugar': 'No Sugar'};
                    notesParts.push(`Sugar: ${sugarLabels[this.sugarLevel] || this.sugarLevel}`);
                }
                
                // Size
                if (this.size === 'large') {
                    notesParts.push('Size: Large (+8k)');
                }
                
                // Add-ons
                if (this.addOns.length > 0) {
                    const addonLabels = {
                        'extra-shot': 'Extra Shot +5k',
                        'whipped-cream': 'Whipped Cream +3k',
                        'caramel-syrup': 'Caramel Syrup +3k'
                    };
                    const addonsText = this.addOns.map(a => addonLabels[a] || a).join(', ');
                    notesParts.push(`Add-ons: ${addonsText}`);
                }
            } else if (this.selectedMenu.type === 'food') {
                // Spice level
                const spiceEmojis = {'mild': '🫑 Mild', 'medium': '🌶️ Medium', 'spicy': '🔥 Spicy'};
                notesParts.push(`Spice: ${spiceEmojis[this.spiceLevel] || this.spiceLevel}`);
                
                // Portion
                if (this.portion === 'large') {
                    notesParts.push('Portion: Large (+5k)');
                }
                
                // Add-ons
                if (this.addOns.length > 0) {
                    const addonLabels = {
                        'extra-cheese': 'Extra Cheese +5k',
                        'extra-egg': 'Extra Egg +3k',
                        'extra-rice': 'Extra Rice +5k'
                    };
                    const addonsText = this.addOns.map(a => addonLabels[a] || a).join(', ');
                    notesParts.push(`Add-ons: ${addonsText}`);
                }
            } else if (this.selectedMenu.type === 'snack') {
                // Size
                if (this.size !== 'regular') {
                    const sizeLabels = {'small': 'Small (-5k)', 'large': 'Large (+5k)'};
                    notesParts.push(`Size: ${sizeLabels[this.size] || this.size}`);
                }
                
                // Sauces
                if (this.sauces.length > 0) {
                    const sauceLabels = {
                        'ketchup': 'Ketchup',
                        'mayo': 'Mayonnaise',
                        'chili': 'Chili Sauce',
                        'bbq': 'BBQ Sauce +2k'
                    };
                    const saucesText = this.sauces.map(s => sauceLabels[s] || s).join(', ');
                    notesParts.push(`Sauces: ${saucesText}`);
                }
            } else if (this.selectedMenu.type === 'dessert') {
                // Portion
                if (this.portion === 'large') {
                    notesParts.push('Portion: Large (+5k)');
                }
                
                // Toppings
                if (this.toppings.length > 0) {
                    const toppingLabels = {
                        'whipped-cream': 'Whipped Cream +3k',
                        'chocolate-chips': 'Chocolate Chips +4k',
                        'caramel-drizzle': 'Caramel Drizzle +3k'
                    };
                    const toppingsText = this.toppings.map(t => toppingLabels[t] || t).join(', ');
                    notesParts.push(`Toppings: ${toppingsText}`);
                }
            }
            
            // Add special instructions if any
            if (this.modalNotes.trim()) {
                notesParts.push(`📝 ${this.modalNotes.trim()}`);
            }
            
            return notesParts.join(' | ');
        },

        addItemFromModal() {
            const itemPrice = this.calculateItemPrice();
            const itemNotes = this.formatItemNotes();
            
            // Check if same item with same options exists
            const existingIndex = this.items.findIndex(item => 
                item.menu_id === this.selectedMenu.id && 
                item.notes === itemNotes
            );
            
            if (existingIndex !== -1) {
                this.items[existingIndex].quantity += this.modalQuantity;
            } else {
                this.items.push({
                    menu_id: this.selectedMenu.id,
                    name: this.selectedMenu.name,
                    price: itemPrice,
                    category: this.selectedMenu.category,
                    quantity: this.modalQuantity,
                    notes: itemNotes
                });
            }
            
            this.closeMenuDetail();
        },

        addItem(menuId, name, price, category) {
            const existingIndex = this.items.findIndex(item => item.menu_id === menuId && !item.notes);
            
            if (existingIndex !== -1) {
                this.items[existingIndex].quantity++;
            } else {
                this.items.push({
                    menu_id: menuId,
                    name: name,
                    price: price,
                    category: category,
                    quantity: 1,
                    notes: ''
                });
            }
        },

        increaseQuantity(index) {
            this.items[index].quantity++;
        },

        decreaseQuantity(index) {
            if (this.items[index].quantity > 1) {
                this.items[index].quantity--;
            }
        },

        removeItem(index) {
            this.items.splice(index, 1);
        },

        calculateSubtotal() {
            return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
        },

        calculateTax() {
            return this.calculateSubtotal() * 0.05;
        },

        calculateTotal() {
            return this.calculateSubtotal() + this.calculateTax();
        },

        formatPrice(price) {
            return new Intl.NumberFormat('id-ID').format(Math.round(price));
        },

        async submitOrder() {
            this.errorMessage = '';

            if (this.items.length === 0) {
                this.errorMessage = 'Silakan tambahkan minimal 1 item';
                return;
            }

            if (!this.tableNumber) {
                this.errorMessage = 'Silakan pilih nomor meja';
                return;
            }

            if (!this.paymentMethod) {
                this.errorMessage = 'Silakan pilih metode pembayaran';
                return;
            }

            // Show order confirmation modal first
            window.dispatchEvent(new CustomEvent('show-order-confirm-modal', {
                detail: {
                    orderDetails: this.items,
                    totalAmount: this.calculateTotal(),
                    itemCount: this.items.length,
                    tableNumber: this.tableNumber,
                    paymentMethod: this.paymentMethod,
                    customerName: this.customerName,
                    confirmAction: () => {
                        this.submitOrderToServer();
                    }
                }
            }));
        },

        async submitOrderToServer() {
            console.log('Submitting order to server:', {
                table_number: this.tableNumber,
                customer_name: this.customerName,
                payment_method: this.paymentMethod,
                items: this.items
            });

            try {
                const response = await fetch('/cashier/manual-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        table_number: this.tableNumber,
                        customer_name: this.customerName,
                        payment_method: this.paymentMethod,
                        items: this.items.map(item => ({
                            menu_id: item.menu_id,
                            quantity: item.quantity,
                            price: item.price,
                            notes: item.notes || ''
                        }))
                    })
                });

                console.log('Response status:', response.status);
                const data = await response.json();
                console.log('Response data:', data);

                if (response.ok && data.success) {
                    // Show success modal after order created
                    window.dispatchEvent(new CustomEvent('show-success-modal', {
                        detail: {
                            title: '✅ Order Berhasil!',
                            message: 'Order berhasil dibuat dan pembayaran telah tercatat. Order siap diproses oleh dapur.',
                            primaryLabel: 'Kembali ke Incoming Orders',
                            secondaryLabel: 'Buat Order Baru',
                            primaryAction: () => {
                                window.location.href = '/cashier/incoming-orders';
                            }
                        }
                    }));
                    
                    // Reset form for new order (if user clicks secondary button)
                    setTimeout(() => {
                        this.items = [];
                        this.tableNumber = '';
                        this.customerName = 'Guest';
                        this.paymentMethod = '';
                        this.orderNumber = Math.floor(Math.random() * 10000);
                    }, 500);
                } else {
                    this.errorMessage = data.message || 'Gagal membuat order';
                    if (data.errors) {
                        console.error('Validation errors:', data.errors);
                        this.errorMessage += ': ' + Object.values(data.errors).flat().join(', ');
                    }
                }
            } catch (error) {
                console.error('Error creating order:', error);
                this.errorMessage = 'Terjadi kesalahan: ' + error.message;
            }
        }
    }
}
</script>
@endpush
