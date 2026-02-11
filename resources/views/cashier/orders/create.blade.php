@extends('layouts.dashboard')

@section('title', 'Manual Order')

@section('content')
<div class="h-[calc(100vh-theme('spacing.20'))] flex flex-col md:flex-row gap-6 p-6" x-data="posSystem()">
    
    <!-- LEFT: Menu Grid -->
    <div class="flex-1 flex flex-col min-h-0 bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm overflow-hidden">
        <!-- Header & Search -->
        <div class="p-4 border-b border-[#e6e0db] dark:border-[#3d362e] space-y-4">
            <h2 class="text-xl font-bold text-[#181411] dark:text-white">Menu</h2>
            
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#897561]">search</span>
                <input 
                    type="text" 
                    x-model="search" 
                    placeholder="Search menu..." 
                    class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 text-[#181411] dark:text-white placeholder-[#897561]"
                >
            </div>

            <!-- Categories -->
            <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                <button 
                    @click="activeCategory = 'all'" 
                    :class="activeCategory === 'all' ? 'bg-primary text-white' : 'bg-gray-50 dark:bg-[#2c241b] text-[#5c4d40] dark:text-[#a89c92] hover:bg-gray-100 dark:hover:bg-[#3d362e]'"
                    class="px-4 py-1.5 rounded-full text-sm font-medium whitespace-nowrap transition-colors"
                >
                    All Items
                </button>
                @foreach($categories as $category)
                <button 
                    @click="activeCategory = '{{ $category->id }}'" 
                    :class="activeCategory === '{{ $category->id }}' ? 'bg-primary text-white' : 'bg-gray-50 dark:bg-[#2c241b] text-[#5c4d40] dark:text-[#a89c92] hover:bg-gray-100 dark:hover:bg-[#3d362e]'"
                    class="px-4 py-1.5 rounded-full text-sm font-medium whitespace-nowrap transition-colors"
                >
                    {{ $category->name }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- Grid Content -->
        <div class="flex-1 overflow-y-auto p-4">
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($categories as $category)
                    @foreach($category->menus as $menu)
                    <div 
                        x-show="(activeCategory === 'all' || activeCategory === '{{ $category->id }}') && ('{{ strtolower($menu->name) }}'.includes(search.toLowerCase()))"
                        class="bg-white dark:bg-[#1a1612] border border-[#e6e0db] dark:border-[#3d362e] rounded-xl overflow-hidden hover:shadow-md transition-shadow cursor-pointer group"
                        @click="addToCart({{ $menu }})"
                    >
                        <div class="aspect-square bg-gray-100 dark:bg-[#2c241b] relative overflow-hidden">
                            @if($menu->image)
                                <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="flex items-center justify-center h-full text-[#897561]">
                                    <span class="material-symbols-outlined text-4xl">restaurant</span>
                                </div>
                            @endif
                            <button class="absolute bottom-2 right-2 bg-white dark:bg-[#1a1612] p-2 rounded-full shadow-lg text-primary hover:bg-primary hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-[20px]">add</span>
                            </button>
                        </div>
                        <div class="p-3">
                            <h3 class="font-bold text-[#181411] dark:text-white text-sm line-clamp-1 truncate" title="{{ $menu->name }}">{{ $menu->name }}</h3>
                            <p class="text-primary font-bold text-sm mt-1">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

    <!-- RIGHT: Current Order (Cart) -->
    <div class="w-full md:w-96 flex flex-col bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm h-full max-h-full">
        <div class="p-4 border-b border-[#e6e0db] dark:border-[#3d362e] flex justify-between items-center">
            <h2 class="text-xl font-bold text-[#181411] dark:text-white">Current Order</h2>
            <button @click="clearCart" class="text-red-500 hover:text-red-600 transition-colors" title="Clear Cart">
                <span class="material-symbols-outlined">delete</span>
            </button>
        </div>

        <!-- Customer Info -->
        <div class="p-4 bg-gray-50 dark:bg-[#2c241b]/50 border-b border-[#e6e0db] dark:border-[#3d362e] grid grid-cols-2 gap-2">
            <div>
                <label class="text-xs font-bold text-[#5c4d40] dark:text-[#a89c92] mb-1 block">Customer Name</label>
                <input type="text" x-model="customerName" class="w-full px-3 py-1.5 text-sm rounded bg-white dark:bg-[#1a1612] border border-[#e6e0db] dark:border-[#3d362e] focus:outline-none focus:border-primary" placeholder="Guest">
            </div>
            <div>
                <label class="text-xs font-bold text-[#5c4d40] dark:text-[#a89c92] mb-1 block">Table No</label>
                <input type="text" x-model="tableNumber" class="w-full px-3 py-1.5 text-sm rounded bg-white dark:bg-[#1a1612] border border-[#e6e0db] dark:border-[#3d362e] focus:outline-none focus:border-primary" placeholder="-">
            </div>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            <template x-if="cart.length === 0">
                <div class="h-full flex flex-col items-center justify-center text-center text-[#897561] py-8">
                    <span class="material-symbols-outlined text-4xl mb-2 opacity-50">shopping_cart_off</span>
                    <p class="text-sm">No items added yet</p>
                </div>
            </template>
            
            <template x-for="(item, index) in cart" :key="index">
                <div class="flex gap-3">
                    <div class="h-12 w-12 rounded-lg bg-gray-100 dark:bg-[#2c241b] overflow-hidden shrink-0">
                         <template x-if="item.image">
                            <img :src="'/storage/' + item.image" class="w-full h-full object-cover">
                         </template>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <h4 class="font-bold text-sm text-[#181411] dark:text-white" x-text="item.name"></h4>
                            <span class="font-bold text-sm text-[#181411] dark:text-white" x-text="formatCurrency(item.price * item.quantity)"></span>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <p class="text-xs text-[#897561]" x-text="'Rp ' + formatCurrency(item.price)"></p>
                            
                            <div class="flex items-center gap-3 bg-gray-50 dark:bg-[#2c241b] rounded-lg px-2 py-1">
                                <button @click="updateQuantity(index, -1)" class="w-5 h-5 flex items-center justify-center rounded bg-white dark:bg-[#1a1612] shadow-sm text-[#181411] dark:text-white hover:bg-gray-100 transition">-</button>
                                <span class="text-sm font-bold w-4 text-center" x-text="item.quantity"></span>
                                <button @click="updateQuantity(index, 1)" class="w-5 h-5 flex items-center justify-center rounded bg-white dark:bg-[#1a1612] shadow-sm text-[#181411] dark:text-white hover:bg-gray-100 transition">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer / Checkout -->
        <div class="p-4 bg-gray-50 dark:bg-[#2c241b]/50 border-t border-[#e6e0db] dark:border-[#3d362e] space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-[#5c4d40] dark:text-[#a89c92]">Subtotal</span>
                <span class="font-bold text-[#181411] dark:text-white" x-text="formatCurrency(subtotal)"></span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-[#5c4d40] dark:text-[#a89c92]">Tax (10%)</span>
                <span class="font-bold text-[#181411] dark:text-white" x-text="formatCurrency(tax)"></span>
            </div>
            <div class="flex justify-between text-xl font-bold pt-2 border-t border-[#e6e0db] dark:border-[#3d362e]">
                <span class="text-[#181411] dark:text-white">Total</span>
                <span class="text-primary" x-text="formatCurrency(total)"></span>
            </div>

            <!-- Payment Method -->
            <div class="grid grid-cols-3 gap-2 py-2">
                <button @click="paymentMethod = 'cash'" :class="paymentMethod === 'cash' ? 'bg-orange-100 border-orange-200 text-orange-700' : 'bg-white border-[#e6e0db] text-[#5c4d40]'" class="py-2 rounded-lg border text-xs font-bold flex flex-col items-center gap-1 transition">
                    <span class="material-symbols-outlined text-lg">payments</span>
                    CASH
                </button>
                <button @click="paymentMethod = 'card'" :class="paymentMethod === 'card' ? 'bg-orange-100 border-orange-200 text-orange-700' : 'bg-white border-[#e6e0db] text-[#5c4d40]'" class="py-2 rounded-lg border text-xs font-bold flex flex-col items-center gap-1 transition">
                    <span class="material-symbols-outlined text-lg">credit_card</span>
                    CARD
                </button>
                <button @click="paymentMethod = 'qris'" :class="paymentMethod === 'qris' ? 'bg-orange-100 border-orange-200 text-orange-700' : 'bg-white border-[#e6e0db] text-[#5c4d40]'" class="py-2 rounded-lg border text-xs font-bold flex flex-col items-center gap-1 transition">
                    <span class="material-symbols-outlined text-lg">qr_code_scanner</span>
                    QRIS
                </button>
            </div>

            <button 
                @click="submitOrder" 
                :disabled="cart.length === 0 || isProcessing"
                class="w-full bg-primary hover:bg-primary-dark disabled:bg-gray-300 disabled:cursor-not-allowed text-white py-3 rounded-xl font-bold shadow-lg shadow-primary/20 transition flex items-center justify-center gap-2"
            >
                <span x-show="!isProcessing">Place Order</span>
                <span x-show="!isProcessing" class="material-symbols-outlined">arrow_forward</span>
                <span x-show="isProcessing" class="animate-spin material-symbols-outlined">progress_activity</span>
            </button>
        </div>
    </div>
</div>

<script>
    function posSystem() {
        return {
            search: '',
            activeCategory: 'all',
            cart: [],
            customerName: '',
            tableNumber: '',
            paymentMethod: 'cash',
            isProcessing: false,
            
            get subtotal() {
                return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            },
            
            get tax() {
                return this.subtotal * 0.1;
            },
            
            get total() {
                return this.subtotal + this.tax;
            },

            addToCart(menu) {
                const existingItem = this.cart.find(item => item.id === menu.id);
                if (existingItem) {
                    existingItem.quantity++;
                } else {
                    this.cart.push({
                        id: menu.id,
                        name: menu.name,
                        price: menu.price,
                        image: menu.image,
                        quantity: 1
                    });
                }
            },

            updateQuantity(index, change) {
                if (this.cart[index].quantity + change > 0) {
                    this.cart[index].quantity += change;
                } else {
                    this.cart.splice(index, 1);
                }
            },

            clearCart() {
                if(confirm('Are you sure you want to clear the cart?')) {
                    this.cart = [];
                }
            },

            formatCurrency(value) {
                return new Intl.NumberFormat('id-ID').format(value);
            },

            submitOrder() {
                if (this.cart.length === 0) return;
                
                this.isProcessing = true;

                // Prepare data
                const orderData = {
                    customer_name: this.customerName || 'Guest',
                    table_number: this.tableNumber,
                    items: this.cart.map(item => ({
                        menu_id: item.id,
                        quantity: item.quantity,
                        notes: '' // Future enhancement
                    })),
                    payment_method: this.paymentMethod,
                    total_amount: this.total // For verification
                };

                // Send to backend
                fetch('{{ route('dashboard.orders.store') }}', { // Reusing dashboard store
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(orderData)
                })
                .then(response => {
                    if (response.ok) {
                        window.location.href = "{{ route('cashier.incoming-orders') }}"; // Redirect to new orders
                    } else {
                        alert('Error placing order. Please try again.');
                        this.isProcessing = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Network error. Please try again.');
                    this.isProcessing = false;
                });
            }
        }
    }
</script>
@endsection
