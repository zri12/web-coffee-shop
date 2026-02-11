@extends('layouts.pos')

@section('title', 'Manual Order')

@section('content')
<div class="flex h-full relative" x-data="posSystem()">
    <!-- Center: Menu Grid -->
    <div class="flex-1 flex flex-col h-full min-w-0 bg-background-light dark:bg-background-dark relative">
        <!-- Top Bar: Search & Title -->
        <div class="px-4 sm:px-6 pt-6 pb-2 shrink-0">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl sm:text-2xl font-bold text-text-main dark:text-white">Walk-in Order</h2>
                <div class="hidden sm:flex text-sm text-text-secondary bg-white dark:bg-surface-dark px-3 py-1 rounded-full border border-[#e6e2de] dark:border-[#3e362e] shadow-sm">
                    <span class="material-symbols-outlined align-middle text-lg mr-1">schedule</span>
                    <span x-text="new Date().toLocaleString('en-US', { month: 'short', day: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true })"></span>
                </div>
                <!-- Cart Toggle Mobile -->
                <button @click="cartOpen = !cartOpen" class="sm:hidden relative p-2 bg-primary text-white rounded-lg shadow-md">
                    <span class="material-symbols-outlined">shopping_cart</span>
                    <span x-show="cart.length > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center" x-text="cart.reduce((a, b) => a + b.quantity, 0)"></span>
                </button>
            </div>
            <!-- Search Bar -->
            <label class="flex flex-col w-full max-w-2xl h-12 shadow-sm rounded-lg relative z-10">
                <div class="flex w-full items-stretch rounded-lg h-full bg-white dark:bg-surface-dark border border-[#e6e2de] dark:border-[#3e362e] focus-within:ring-2 focus-within:ring-primary/50 transition-shadow">
                    <div class="text-text-secondary flex items-center justify-center pl-4 pr-2">
                        <span class="material-symbols-outlined text-[24px]">search</span>
                    </div>
                    <input x-model="search" class="flex w-full min-w-0 flex-1 resize-none bg-transparent border-none text-text-main dark:text-white focus:outline-0 focus:ring-0 h-full placeholder:text-text-secondary/60 px-0 pl-2 text-base font-normal" placeholder="Search menu..."/>
                </div>
            </label>
        </div>
        
        <!-- Category Chips -->
        <div class="px-4 sm:px-6 py-4 shrink-0 overflow-x-auto no-scrollbar">
            <div class="flex gap-3 min-w-max">
                <button @click="activeCategory = 'all'" 
                    :class="activeCategory === 'all' ? 'bg-primary text-white shadow-md' : 'bg-white dark:bg-surface-dark text-text-main dark:text-white border border-[#e6e2de] dark:border-[#3e362e] hover:bg-[#f4f2f0] dark:hover:bg-[#3e362e]'"
                    class="flex h-9 items-center justify-center px-4 rounded-full transition-all active:scale-95">
                    <span class="text-sm font-medium">All Items</span>
                </button>
                @foreach($categories as $category)
                <button @click="activeCategory = '{{ $category->slug }}'" 
                    :class="activeCategory === '{{ $category->slug }}' ? 'bg-primary text-white shadow-md' : 'bg-white dark:bg-surface-dark text-text-main dark:text-white border border-[#e6e2de] dark:border-[#3e362e] hover:bg-[#f4f2f0] dark:hover:bg-[#3e362e]'"
                    class="flex h-9 items-center justify-center px-4 rounded-full transition-all active:scale-95">
                    <span class="text-sm font-medium">{{ $category->name }}</span>
                </button>
                @endforeach
            </div>
        </div>

        <!-- Product Grid -->
        <div class="flex-1 overflow-y-auto px-4 sm:px-6 pb-20 sm:pb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($categories as $category)
                    @foreach($category->menus as $menu)
                    <div x-show="(activeCategory === 'all' || activeCategory === '{{ $category->slug }}') && ('{{ strtolower($menu->name) }}'.includes(search.toLowerCase()))"
                         @click="addToCart({{ $menu }})"
                         class="group flex flex-col rounded-xl bg-white dark:bg-surface-dark shadow-sm border border-[#e6e2de] dark:border-[#3e362e] overflow-hidden hover:shadow-md transition-all cursor-pointer transform active:scale-[0.98]">
                        <div class="h-32 w-full bg-center bg-cover bg-no-repeat relative bg-coffee-100" 
                             style="background-image: url('{{ $menu->image_url ? asset('storage/' . $menu->image_url) : '' }}');">
                             @if(!$menu->image_url)
                             <div class="absolute inset-0 flex items-center justify-center text-4xl">☕</div>
                             @endif
                            <div class="absolute inset-0 bg-black/5 group-hover:bg-black/0 transition-colors"></div>
                        </div>
                        <div class="p-3 flex flex-col grow gap-1">
                            <div class="flex justify-between items-start">
                                <h3 class="text-text-main dark:text-white font-bold text-sm leading-tight">{{ $menu->name }}</h3>
                            </div>
                            <p class="text-text-secondary text-xs line-clamp-2 leading-relaxed">{{ Str::limit($menu->description, 40) }}</p>
                            <div class="mt-auto pt-2 flex items-center justify-between">
                                <span class="text-text-main dark:text-white font-bold">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                <button class="w-8 h-8 flex items-center justify-center rounded-full bg-primary text-white hover:bg-primary-hover shadow-sm transition-transform active:scale-90">
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

    <!-- Right Sidebar (Cart) -->
    <!-- Mobile Backdrop -->
    <div x-show="cartOpen" 
         @click="cartOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

    <!-- Cart Sidebar -->
    <aside :class="cartOpen ? 'translate-x-0' : 'translate-x-full'"
           class="fixed inset-y-0 right-0 lg:static w-full sm:w-[400px] bg-white dark:bg-surface-dark border-l border-[#e6e2de] dark:border-[#3e362e] flex flex-col shrink-0 shadow-2xl z-50 transition-transform duration-300 lg:translate-x-0">
        
        <!-- Cart Header -->
        <div class="p-4 sm:p-6 border-b border-[#e6e2de] dark:border-[#3e362e] flex justify-between items-center bg-white dark:bg-surface-dark">
            <div class="flex items-center gap-3">
                <button @click="cartOpen = false" class="lg:hidden text-text-secondary">
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
                <div class="flex flex-col">
                    <h2 class="text-lg font-bold text-text-main dark:text-white">Current Order</h2>
                    <span class="text-xs text-text-secondary font-mono">New Order</span>
                </div>
            </div>
            <button @click="clearCart()" class="text-primary hover:bg-[#f4f2f0] dark:hover:bg-[#3e362e] p-2 rounded-lg transition-colors" title="Clear All">
                <span class="material-symbols-outlined">delete_sweep</span>
            </button>
        </div>
        
        <!-- Customer Form -->
        <div class="px-4 sm:px-6 py-4 border-b border-[#e6e2de] dark:border-[#3e362e] bg-[#faf9f8] dark:bg-[#251f18]">
            <div class="flex flex-col gap-3">
                <div class="flex gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-text-secondary mb-1">Customer Name</label>
                        <input x-model="customerName" class="w-full rounded-lg border-[#e6e2de] dark:border-[#4a423b] bg-white dark:bg-[#332b24] text-sm py-1.5 focus:border-primary focus:ring-1 focus:ring-primary placeholder:text-gray-400" placeholder="Guest" type="text"/>
                    </div>
                    <div class="w-20">
                        <label class="block text-xs font-medium text-text-secondary mb-1">Table</label>
                        <input x-model="tableNumber" class="w-full rounded-lg border-[#e6e2de] dark:border-[#4a423b] bg-white dark:bg-[#332b24] text-sm py-1.5 focus:border-primary focus:ring-1 focus:ring-primary text-center" placeholder="-" type="text"/>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Phone (Optional)</label>
                    <input x-model="customerPhone" class="w-full rounded-lg border-[#e6e2de] dark:border-[#4a423b] bg-white dark:bg-[#332b24] text-sm py-1.5 focus:border-primary focus:ring-1 focus:ring-primary" placeholder="+62" type="tel"/>
                </div>
            </div>
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            <template x-for="(item, index) in cart" :key="index">
                <div class="flex gap-3 items-center group">
                    <div class="w-12 h-12 rounded-lg bg-cover bg-center shrink-0 bg-coffee-100" 
                         :style="item.image_url ? `background-image: url('/storage/' + item.image_url)` : ''">
                         <span x-show="!item.image_url" class="block w-full h-full text-center leading-[3rem]">☕</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-text-main dark:text-white truncate" x-text="item.name"></h4>
                        <div class="flex items-center justify-between mt-1">
                             <div class="flex items-center rounded-md border border-[#e6e2de] dark:border-[#4a423b] bg-white dark:bg-[#332b24]">
                                <button @click="updateQuantity(index, -1)" class="w-6 h-6 flex items-center justify-center text-text-secondary hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">remove</span>
                                </button>
                                <span class="w-6 text-center text-xs font-medium dark:text-white" x-text="item.quantity"></span>
                                <button @click="updateQuantity(index, 1)" class="w-6 h-6 flex items-center justify-center text-text-secondary hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">add</span>
                                </button>
                            </div>
                            <span class="text-sm font-bold dark:text-white" x-text="formatPrice(item.price * item.quantity)"></span>
                        </div>
                    </div>
                </div>
            </template>
            <div x-show="cart.length === 0" class="flex flex-col items-center justify-center h-40 text-text-secondary opacity-50">
                <span class="material-symbols-outlined text-4xl mb-2">shopping_cart</span>
                <p>Cart is empty</p>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-white dark:bg-surface-dark border-t border-[#e6e2de] dark:border-[#3e362e] p-4 sm:p-6 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm text-text-secondary">
                    <span>Subtotal</span>
                    <span x-text="formatPrice(total)"></span>
                </div>
                <div class="flex justify-between text-xl font-bold text-text-main dark:text-white mt-2 pt-2 border-t border-dashed border-[#e6e2de] dark:border-[#3e3e2e]">
                    <span>Total</span>
                    <span x-text="formatPrice(total)"></span>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="grid grid-cols-3 gap-2 mb-4">
                <button @click="paymentMethod = 'cash'" :class="paymentMethod === 'cash' ? 'border-2 border-primary bg-primary/5 text-primary' : 'border border-[#e6e2de] dark:border-[#4a423b] text-text-secondary'" class="flex flex-col items-center justify-center p-2 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[20px]">payments</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider mt-1">Cash</span>
                </button>
                <button @click="paymentMethod = 'card'" :class="paymentMethod === 'card' ? 'border-2 border-primary bg-primary/5 text-primary' : 'border border-[#e6e2de] dark:border-[#4a423b] text-text-secondary'" class="flex flex-col items-center justify-center p-2 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[20px]">credit_card</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider mt-1">Card</span>
                </button>
                <button @click="paymentMethod = 'qris'" :class="paymentMethod === 'qris' ? 'border-2 border-primary bg-primary/5 text-primary' : 'border border-[#e6e2de] dark:border-[#4a423b] text-text-secondary'" class="flex flex-col items-center justify-center p-2 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[20px]">qr_code</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider mt-1">QRIS</span>
                </button>
            </div>

            <button @click="submitOrder()" 
                :disabled="cart.length === 0 || isLoading"
                class="w-full bg-primary hover:bg-primary-hover text-white h-12 rounded-xl font-bold text-lg shadow-lg shadow-primary/30 active:scale-[0.98] transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!isLoading">Place Order</span>
                <span x-show="isLoading" class="animate-spin material-symbols-outlined">progress_activity</span>
                <span x-show="!isLoading" class="material-symbols-outlined">arrow_forward</span>
            </button>
        </div>
    </aside>
</div>

<script>
    function posSystem() {
        return {
            activeCategory: 'all',
            search: '',
            cart: [],
            cartOpen: false, // Mobile Drawer State
            customerName: '',
            customerPhone: '',
            tableNumber: '',
            paymentMethod: 'cash',
            isLoading: false,

            addToCart(menu) {
                const existingItem = this.cart.find(item => item.id === menu.id);
                if (existingItem) {
                    existingItem.quantity++;
                } else {
                    this.cart.push({
                        id: menu.id,
                        name: menu.name,
                        price: Number(menu.price),
                        image_url: menu.image_url,
                        quantity: 1
                    });
                }
                // Optional: Open cart on mobile automatically when item added
                if (window.innerWidth < 1024) {
                    // this.cartOpen = true; 
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
                if(confirm('Clear current order?')) {
                    this.cart = [];
                    this.customerName = '';
                    this.customerPhone = '';
                    this.tableNumber = '';
                }
            },

            get total() {
                return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            },

            formatPrice(price) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(price);
            },

            submitOrder() {
                if (this.cart.length === 0) return;
                if (!this.customerName) {
                    alert('Please enter customer name');
                    return;
                }

                this.isLoading = true;

                fetch('{{ route("dashboard.orders.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        customer_name: this.customerName,
                        customer_phone: this.customerPhone,
                        table_number: this.tableNumber || 0,
                        items: this.cart,
                        payment_method: this.paymentMethod
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Order ' + data.order_number + ' Created Successfully!');
                        this.cart = [];
                        this.customerName = '';
                        this.customerPhone = '';
                        this.tableNumber = '';
                        this.cartOpen = false;
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Operation failed');
                    console.error(error);
                })
                .finally(() => {
                    this.isLoading = false;
                });
            }
        }
    }
</script>
@endsection
