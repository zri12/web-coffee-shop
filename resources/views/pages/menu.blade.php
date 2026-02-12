@extends('layouts.app')

@section('title', 'Menu')

@section('content')
<!-- Responsive Container -->
<div class="w-full min-h-screen bg-white" x-data="menuHandler()">
    <div class="flex flex-col min-h-screen w-full max-w-md md:max-w-7xl mx-auto relative md:px-6 lg:px-8 md:py-8">
        
        <!-- HEADER -->
        <div class="px-4 md:px-0 pt-4 md:pt-0 pb-3 bg-white shadow-sm md:shadow-none sticky top-[72px] md:top-0 z-20">
            <!-- Greeting Text -->
            <p class="text-[#9B9B9B] text-[11px] md:text-[13px] font-medium tracking-wide mb-1 uppercase">Good Morning</p>
            
            <!-- Title Section -->
            <div class="mb-3 md:mb-6">
                 <h2 class="text-[28px] md:text-[40px] font-bold text-[#2F2D2C] leading-tight">Our Menu</h2>
            </div>

            <!-- CATEGORIES (Horizontal Scroll) -->
            <div class="flex gap-3 overflow-x-auto hide-scrollbar pb-2" id="category-nav">
                @php
                    $categoryLabels = [
                        'kopi' => 'Coffee',
                        'non-kopi' => 'Non-Coffee',
                        'makanan-ringan' => 'Snack',
                        'dessert' => 'Dessert',
                    ];
                @endphp

                <!-- All Category -->
                <button @click="scrollToCategory('all')"
                        class="px-5 py-2.5 rounded-full text-[14px] font-semibold whitespace-nowrap transition-all duration-300 shrink-0"
                        :class="activeCategory === 'all' ? 'bg-[#C67C4E] text-white' : 'bg-[#EDEDED] text-[#313131]'">
                    All
                </button>

                @foreach($categories as $category)
                <button @click="scrollToCategory('{{ $category->id }}')"
                        class="px-5 py-2.5 rounded-full text-[14px] font-semibold whitespace-nowrap transition-all duration-300 shrink-0"
                        :class="activeCategory === '{{ $category->id }}' ? 'bg-[#C67C4E] text-white' : 'bg-[#EDEDED] text-[#313131]'">
                    {{ $categoryLabels[$category->slug] ?? $category->name }}
                </button>
                @endforeach
            </div>
        </div>

        @if(session('error'))
        <div class="mx-4 md:mx-0 mt-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-medium">
            {{ session('error') }}
        </div>
        @endif

        <!-- MAIN CONTENT (Scrollable) -->
        <div class="flex-1 overflow-y-auto px-4 md:px-0 pb-24 md:pb-8 hide-scrollbar" id="menu-content" @scroll.passive="onScroll">
            
            <!-- Popular Menu Section -->
            <div id="cat-all" class="category-section mb-8 mt-4 md:mt-6" data-id="all">
                <h2 class="text-[16px] md:text-[20px] font-semibold text-[#2F2D2C] mb-4">Popular Menu</h2>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3.5 md:gap-5">
                    @foreach($categories as $category)
                        @foreach($category->menus->where('is_featured', true)->take(2) as $menu)
                        <div class="bg-white rounded-2xl overflow-hidden flex flex-col shadow-sm hover:shadow-md active:scale-[0.98] transition-all">
                            <div class="w-full aspect-square bg-[#F9F9F9] relative overflow-hidden">
                                <img src="{{ $menu->display_image_url }}" alt="{{ $menu->name }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" onerror="this.onerror=null;this.src='{{ $menu->placeholder_image_url }}'">

                                @if($menu->is_featured)
                                <div class="absolute top-2 left-2 bg-[#ED5151] text-white text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wide">
                                    Best Seller
                                </div>
                                @endif
                                
                                @if(!$menu->is_available)
                                <div class="absolute inset-0 bg-black/60 flex items-center justify-center backdrop-blur-[1px]">
                                    <span class="bg-red-500 text-white px-3 py-1 rounded-lg font-bold text-xs uppercase tracking-wider">Sold Out</span>
                                </div>
                                @endif
                            </div>

                            <div class="p-3 md:p-4 flex flex-col flex-1">
                                <h3 class="font-semibold text-[#2F2D2C] text-[14px] md:text-[15px] leading-tight mb-1">{{ $menu->name }}</h3>
                                <p class="text-[11px] md:text-[12px] text-[#9B9B9B] line-clamp-2 mb-3">{{ $menu->description }}</p>

                                <div class="flex justify-between items-center mt-auto">
                                    <span class="font-bold text-[#2F4B4E] text-[18px] md:text-[20px]">
                                        {{ $menu->formatted_price }}
                                    </span>
                                    
                                    @if($menu->is_available)
                                    <button @click="showProductDetail({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, '{{ addslashes($menu->description) }}', {{ \Illuminate\Support\Js::from($menu->display_image_url) }}, {{ $menu->is_featured ? 'true' : 'false' }}, '{{ $menu->category->slug }}', '{{ $menu->formatted_price }}')"
                                            class="w-8 h-8 md:w-9 md:h-9 bg-[#C67C4E] text-white rounded-lg flex items-center justify-center hover:bg-[#A05E35] active:bg-[#A05E35] transition-colors shadow-md">
                                        <span class="material-symbols-outlined text-[20px] md:text-[22px]">add</span>
                                    </button>
                                    @else
                                    <button disabled class="w-8 h-8 md:w-9 md:h-9 bg-gray-300 text-gray-500 rounded-lg flex items-center justify-center cursor-not-allowed opacity-50">
                                        <span class="material-symbols-outlined text-[20px] md:text-[22px]">block</span>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
            
            <!-- Category Sections -->
            @foreach($categories as $category)
            <div id="cat-{{ $category->id }}" class="category-section mb-8 mt-6" data-id="{{ $category->id }}">
                <h2 class="text-[16px] md:text-[20px] font-semibold text-[#2F2D2C] mb-4">
                    {{ $categoryLabels[$category->slug] ?? $category->name }}
                </h2>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3.5 md:gap-5">
                    @foreach($category->menus as $menu)
                    <div class="bg-white rounded-2xl overflow-hidden flex flex-col shadow-sm hover:shadow-md active:scale-[0.98] transition-all">
                        <div class="w-full aspect-square bg-[#F9F9F9] relative overflow-hidden">
                            <img src="{{ $menu->display_image_url }}" alt="{{ $menu->name }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" onerror="this.onerror=null;this.src='{{ $menu->placeholder_image_url }}'">

                            @if($menu->is_featured)
                            <div class="absolute top-2 left-2 bg-[#ED5151] text-white text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wide">
                                Best Seller
                            </div>
                            @endif
                            
                            @if(!$menu->is_available)
                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center backdrop-blur-[1px]">
                                <span class="bg-red-500 text-white px-3 py-1 rounded-lg font-bold text-xs uppercase tracking-wider">Sold Out</span>
                            </div>
                            @endif
                        </div>

                        <div class="p-3 md:p-4 flex flex-col flex-1">
                            <h3 class="font-semibold text-[#2F2D2C] text-[14px] md:text-[15px] leading-tight mb-1">{{ $menu->name }}</h3>
                            <p class="text-[11px] md:text-[12px] text-[#9B9B9B] line-clamp-2 mb-3">{{ $menu->description }}</p>

                            <div class="flex justify-between items-center mt-auto">
                                <span class="font-bold text-[#2F4B4E] text-[18px] md:text-[20px]">
                                    {{ $menu->formatted_price }}
                                </span>
                                
                                @if($menu->is_available)
                                <button @click="showProductDetail({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, '{{ addslashes($menu->description) }}', {{ \Illuminate\Support\Js::from($menu->display_image_url) }}, {{ $menu->is_featured ? 'true' : 'false' }}, '{{ $menu->category->slug }}', '{{ $menu->formatted_price }}')"
                                        class="w-8 h-8 md:w-9 md:h-9 bg-[#C67C4E] text-white rounded-lg flex items-center justify-center hover:bg-[#A05E35] active:bg-[#A05E35] transition-colors shadow-md">
                                    <span class="material-symbols-outlined text-[20px] md:text-[22px]">add</span>
                                </button>
                                @else
                                <button disabled class="w-8 h-8 md:w-9 md:h-9 bg-gray-300 text-gray-500 rounded-lg flex items-center justify-center cursor-not-allowed opacity-50">
                                    <span class="material-symbols-outlined text-[20px] md:text-[22px]">block</span>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

            <!-- Bottom Spacer -->
            <div class="h-4 md:h-8"></div>
        </div>

        <!-- BOTTOM FLOATING CART (Fixed position, only on mobile) -->
        <div x-show="cartCount > 0" 
             x-transition:enter="transition ease-out duration-300 transform" 
             x-transition:enter-start="translate-y-20 opacity-0" 
             x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-y-0 opacity-100"
             x-transition:leave-end="translate-y-20 opacity-0"
             class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-[#F1F1F1]">
             
            <div class="max-w-md mx-auto px-4 pb-5 pt-3">
                <button @click="goToCheckout()" class="w-full bg-[#C67C4E] text-white px-5 py-3.5 rounded-2xl flex items-center justify-between shadow-lg active:scale-[0.99] transition-transform">
                    <div class="flex items-center gap-3">
                        <div class="bg-[#2F4B4E] w-6 h-6 rounded-md flex items-center justify-center text-[13px] font-bold text-white" x-text="cartCount">0</div>
                        <span class="font-semibold text-[15px]">View Cart</span>
                    </div>
                    
                    <span class="font-bold text-[15px]" x-text="cartTotal">Rp 0</span>
                </button>
            </div>
        </div>

    </div>

    <!-- Product Detail Modal -->
    @include('components.product-detail-modal')
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    // Inject Table Number if available
    @if(isset($table))
        window.appTableNumber = "{{ $table->table_number }}";
        window.checkoutUrl = "{{ route('customer.checkout', ['tableNumber' => $table->table_number]) }}";
    @endif

    Alpine.data('menuHandler', () => ({
        activeCategory: 'all',
        cartCount: 0,
        cartTotal: 'Rp 0',
        sections: [],
        
        // Product Detail Modal
        showDetail: false,
        selectedProduct: {
            id: null,
            name: '',
            price: '',
            priceRaw: 0,
            description: '',
            image: '',
            isFeatured: false,
            type: 'beverage'
        },
        // Options
        temperature: 'ice',
        iceLevel: 'normal',
        sugarLevel: 'normal',
        size: 'regular',
        spiceLevel: 'mild',
        portion: 'regular',
        toppings: [],
        addOns: [],
        sauces: [],
        specialRequest: '',
        
        init() {
            // Update cart info from window.Cart
            this.updateCartInfo();
            
            // Initialize sections
            this.$nextTick(() => {
                this.sections = Array.from(document.querySelectorAll('.category-section'));
            });
            
            // Listen for global cart updates
            window.addEventListener('cart-updated', (e) => {
                this.updateCartInfo(e.detail);
            });
        },

        showProductDetail(id, name, price, description, image, isFeatured, categorySlug, formattedPrice) {
            this.selectedProduct = {
                id: id,
                name: name,
                price: formattedPrice,
                priceRaw: price,
                description: description,
                image: image,
                isFeatured: isFeatured,
                type: this.getProductType(categorySlug)
            };
            
            // Reset options
            this.resetOptions();
            
            this.showDetail = true;
            document.body.style.overflow = 'hidden';
        },

        resetOptions() {
            this.temperature = 'ice';
            this.iceLevel = 'normal';
            this.sugarLevel = 'normal';
            this.size = 'regular';
            this.spiceLevel = 'mild';
            this.portion = 'regular';
            this.toppings = [];
            this.addOns = [];
            this.sauces = [];
            this.specialRequest = '';
        },

        getProductType(categorySlug) {
            const beverageCategories = ['kopi', 'non-kopi', 'coffee', 'non-coffee'];
            const foodCategories = ['makanan', 'food', 'makanan-berat'];
            const snackCategories = ['makanan-ringan', 'snack'];
            const dessertCategories = ['dessert'];
            
            if (beverageCategories.includes(categorySlug)) return 'beverage';
            if (foodCategories.includes(categorySlug)) return 'food';
            if (snackCategories.includes(categorySlug)) return 'snack';
            if (dessertCategories.includes(categorySlug)) return 'dessert';
            return 'beverage';
        },

        async addToCartWithOptions() {
            const options = this.buildOptions();
            options.type = this.selectedProduct.type;
            
            // Use unified Cart API
            await window.Cart.add(
                this.selectedProduct.id, 
                this.selectedProduct.name, 
                this.selectedProduct.priceRaw, 
                this.selectedProduct.image, 
                1, 
                options
            );
            
            // Close modal
            this.showDetail = false;
            document.body.style.overflow = '';
        },

        buildOptions() {
            const options = {};
            
            if (this.selectedProduct.type === 'beverage') {
                options.temperature = this.temperature;
                if (this.temperature === 'ice') options.iceLevel = this.iceLevel;
                options.sugarLevel = this.sugarLevel;
                options.size = this.size;
                if (this.addOns.length > 0) options.addOns = this.addOns;
            } else if (this.selectedProduct.type === 'food') {
                options.spiceLevel = this.spiceLevel;
                options.portion = this.portion;
                if (this.addOns.length > 0) options.addOns = this.addOns;
            } else if (this.selectedProduct.type === 'snack') {
                options.portion = this.portion;
                if (this.sauces.length > 0) options.sauces = this.sauces;
            } else if (this.selectedProduct.type === 'dessert') {
                options.portion = this.portion;
                if (this.toppings.length > 0) options.toppings = this.toppings;
            }
            
            if (this.specialRequest && this.specialRequest.trim() !== '') {
                options.specialRequest = this.specialRequest;
            }
            
            return options;
        },

        calculateItemPrice() {
            let total = Number(this.selectedProduct.priceRaw) || 0;
            
            // Add-ons pricing logic
            // ... (Keep existing logic or ensure it matches backend)
            // Ideally backend handles this, but frontend needs it for display
             if (this.addOns.length > 0) {
                const addonPrices = { 'extra-shot': 5000, 'whipped-cream': 3000, 'caramel-syrup': 3000, 'extra-cheese': 5000, 'extra-egg': 3000, 'extra-rice': 5000 };
                this.addOns.forEach(addon => total += addonPrices[addon] || 0);
            }
            if (this.toppings.length > 0) {
               const toppingPrices = { 'chocolate': 3000, 'caramel': 3000, 'whipped': 5000, 'ice-cream': 8000 };
               this.toppings.forEach(topping => total += toppingPrices[topping] || 0);
            }
            if (this.sauces.length > 0 && this.sauces.includes('bbq')) total += 2000;
            
            if (this.selectedProduct.type === 'beverage' && this.size === 'large') total += 8000;
            
             if (this.portion === 'large') {
                if(this.selectedProduct.type === 'dessert') total += 8000;
                else if (['food', 'snack'].includes(this.selectedProduct.type)) total += 5000;
            } else if (this.portion === 'small' && this.selectedProduct.type === 'snack') {
                total -= 5000;
            }
            
            return total;
        },

        scrollToCategory(id) {
            this.activeCategory = id;
            setTimeout(() => {
                const element = document.getElementById('cat-' + id);
                if (element) {
                    const headerOffset = window.innerWidth >= 768 ? 100 : 180;
                    const elementPosition = element.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                    window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
                }
            }, 50);
        },

        onScroll(e) {
            const container = e.target;
            const scrollPosition = container.scrollTop + (window.innerWidth >= 768 ? 100 : 180); 
            for (const section of this.sections) {
                const top = section.offsetTop;
                const height = section.offsetHeight;
                if (scrollPosition >= top && scrollPosition < top + height) {
                    this.activeCategory = section.dataset.id;
                    break;
                }
            }
        },

        goToCheckout() {
            if (window.checkoutUrl) {
                window.location.href = window.checkoutUrl;
            } else {
                window.location.href = '/cart';
            }
        },

        updateCartInfo(cartData = null) {
            const cart = cartData || window.Cart.items || [];
            this.cartCount = cart.reduce((sum, item) => sum + (Math.max(1, parseInt(item.quantity || 1, 10) || 1)), 0);
            const total = cart.reduce((sum, item) => {
                const quantity = Math.max(1, parseInt(item.quantity || 1, 10) || 1);
                const itemTotal = Number(item.subtotal ?? ((item.total_price ?? item.finalPrice ?? item.final_price ?? item.price ?? 0) * quantity)) || 0;
                return sum + itemTotal;
            }, 0);
            this.cartTotal = window.Cart.formatPrice(total);
        }
    }));
});

// Cart object for compatibility
if (typeof Cart === 'undefined') {
    window.Cart = {
        items: [],
        add: function(id, name, price, image) {
            // Get existing cart
            let cart = [];
            try {
                const cartData = localStorage.getItem('cart');
                if (cartData) cart = JSON.parse(cartData);
            } catch(e) {}
            
            // Check if item exists
            const existingIndex = cart.findIndex(item => item.id === id);
            if (existingIndex > -1) {
                cart[existingIndex].quantity++;
            } else {
                cart.push({ id, name, price, image, quantity: 1 });
            }
            
            // Save to localStorage
            localStorage.setItem('cart', JSON.stringify(cart));
            this.items = cart;
            
            // Trigger update event
            window.dispatchEvent(new Event('storage'));
        }
    };
}
</script>
@endpush

@push('styles')
<style>
/* Hide scrollbar but keep functionality */
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Mobile specific styles */
@media (max-width: 768px) {
    html, body {
        overflow-x: hidden !important;
        max-width: 100vw !important;
        position: relative;
    }
    
    /* Ensure all children don't overflow */
    * {
        max-width: 100%;
    }
    
    /* Hide cart icon in navbar on mobile only */
    header a[href*="cart"] {
        display: none !important;
    }
}
</style>
@endpush
@endsection
