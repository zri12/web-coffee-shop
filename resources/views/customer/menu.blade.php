@extends('layouts.customer')

@section('title', 'Menu - ' . $table->table_number)

@section('content')
<div class="flex flex-col h-screen bg-white overflow-hidden" x-data="menuHandler()">
    
    <!-- NAVBAR -->
    <nav class="px-4 py-3 bg-white border-b border-[#F1F1F1] sticky top-0 z-30 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#C67C4E] text-[28px]">local_cafe</span>
                <div class="flex flex-col">
                    <h1 class="text-[16px] font-bold text-[#2F2D2C]">CafeOS</h1>
                    <p class="text-[10px] text-[#9B9B9B]">Table {{ $table->table_number }}</p>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- HEADER -->
    <div class="px-4 pt-4 pb-3 bg-white shadow-sm sticky top-[70px] z-20">
        <!-- Greeting Text -->
        <p class="text-[#9B9B9B] text-[11px] font-medium tracking-wide mb-1 uppercase">Good Morning</p>
        
        <!-- Title Section -->
        <div class="mb-3">
             <h2 class="text-[28px] font-bold text-[#2F2D2C] leading-tight">Our Menu</h2>
        </div>

        <!-- CATEGORIES (Horizontal Scroll) -->
        <div class="flex gap-3 overflow-x-auto hide-scrollbar pb-2" id="category-nav">
            @php
                $categoryNames = [
                    'coffee' => 'Coffee',
                    'non-coffee' => 'Non-Coffee',
                    'snack' => 'Snack',
                    'dessert' => 'Dessert',
                    'food' => 'Food',
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
                {{ $categoryNames[$category->slug] ?? $category->name }}
            </button>
            @endforeach
        </div>
    </div>

    <!-- MAIN CONTENT (Scrollable) -->
    <div class="flex-1 overflow-y-auto px-4 pb-32 hide-scrollbar w-full" id="menu-content" @scroll.passive="onScroll">
        
        <!-- Popular Menu Section -->
        <div id="cat-all" class="category-section mb-8 mt-4" data-id="all">
            <h2 class="text-[16px] font-semibold text-[#2F2D2C] mb-4">Popular Menu</h2>

            <div class="grid grid-cols-2 gap-3.5">
                @foreach($categories as $category)
                    @foreach($category->menus->where('is_featured', true)->take(2) as $menu)
                    <div class="bg-white rounded-2xl overflow-hidden flex flex-col shadow-sm hover:shadow-md active:scale-[0.98] transition-all">
                        <div class="w-full aspect-square bg-[#F9F9F9] relative overflow-hidden">
                            @if($menu->image_url)
                                <img src="{{ asset('storage/' . $menu->image_url) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            @elseif($menu->image)
                                <img src="{{ asset('images/menus/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-[#E0E0E0]">
                                    <span class="material-symbols-outlined text-[50px]">local_cafe</span>
                                </div>
                            @endif

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

                        <div class="p-3 flex flex-col flex-1">
                            <h3 class="font-semibold text-[#2F2D2C] text-[14px] leading-tight mb-1">{{ $menu->name }}</h3>
                            <p class="text-[11px] text-[#9B9B9B] line-clamp-2 mb-3">{{ $menu->description }}</p>

                            <div class="flex justify-between items-center mt-auto">
                                <span class="font-bold text-[#2F4B4E] text-[18px]">
                                    Rp {{ number_format($menu->price, 0, ',', '.') }}
                                </span>
                                
                                @if($menu->is_available)
                                <button @click="showProductDetail({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, '{{ addslashes($menu->description) }}', '{{ $menu->image_url ? asset('storage/' . $menu->image_url) : ($menu->image ? asset('images/menus/' . $menu->image) : '') }}', {{ $menu->is_featured ? 'true' : 'false' }}, '{{ ($menu->category->slug ?? $category->slug) }}', 'Rp {{ number_format($menu->price, 0, ',', '.') }}')" 
                                        class="w-8 h-8 bg-[#C67C4E] text-white rounded-lg flex items-center justify-center hover:bg-[#A05E35] active:bg-[#A05E35] transition-colors shadow-md">
                                    <span class="material-symbols-outlined text-[20px]">add</span>
                                </button>
                                @else
                                <button disabled class="w-8 h-8 bg-gray-300 text-gray-500 rounded-lg flex items-center justify-center cursor-not-allowed opacity-50">
                                    <span class="material-symbols-outlined text-[20px]">block</span>
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
            <h2 class="text-[16px] font-semibold text-[#2F2D2C] mb-4">
                {{ $categoryNames[$category->slug] ?? $category->name }}
            </h2>

            <div class="grid grid-cols-2 gap-3.5">
                @foreach($category->menus as $menu)
                <div class="bg-white rounded-2xl overflow-hidden flex flex-col shadow-sm hover:shadow-md active:scale-[0.98] transition-all">
                    <div class="w-full aspect-square bg-[#F9F9F9] relative overflow-hidden">
                        @if($menu->image_url)
                            <img src="{{ asset('storage/' . $menu->image_url) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        @elseif($menu->image)
                            <img src="{{ asset('images/menus/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-[#E0E0E0]">
                                <span class="material-symbols-outlined text-[50px]">local_cafe</span>
                            </div>
                        @endif

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

                    <div class="p-3 flex flex-col flex-1">
                        <h3 class="font-semibold text-[#2F2D2C] text-[14px] leading-tight mb-1">{{ $menu->name }}</h3>
                        <p class="text-[11px] text-[#9B9B9B] line-clamp-2 mb-3">{{ $menu->description }}</p>

                        <div class="flex justify-between items-center mt-auto">
                            <span class="font-bold text-[#2F4B4E] text-[18px]">
                                Rp {{ number_format($menu->price, 0, ',', '.') }}
                            </span>
                            
                                @if($menu->is_available)
                                <button @click="showProductDetail({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, '{{ addslashes($menu->description) }}', '{{ $menu->image_url ? asset('storage/' . $menu->image_url) : ($menu->image ? asset('images/menus/' . $menu->image) : '') }}', {{ $menu->is_featured ? 'true' : 'false' }}, '{{ ($menu->category->slug ?? $category->slug) }}', 'Rp {{ number_format($menu->price, 0, ',', '.') }}')" 
                                        class="w-8 h-8 bg-[#C67C4E] text-white rounded-lg flex items-center justify-center hover:bg-[#A05E35] active:bg-[#A05E35] transition-colors shadow-md">
                                    <span class="material-symbols-outlined text-[20px]">add</span>
                                </button>
                                @else
                                <button disabled class="w-8 h-8 bg-gray-300 text-gray-500 rounded-lg flex items-center justify-center cursor-not-allowed opacity-50">
                                    <span class="material-symbols-outlined text-[20px]">block</span>
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
        <div class="h-4"></div>
    </div>
    </div>

    <!-- BOTTOM FLOATING CART -->
    <div x-show="cartCount > 0" 
         x-transition:enter="transition ease-out duration-300 transform" 
         x-transition:enter-start="translate-y-20 opacity-0" 
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-20 opacity-0"
         class="fixed bottom-0 left-0 right-0 z-40 px-4 pb-6 pt-3 bg-white border-t border-[#F1F1F1] md:hidden">
         
        <a href="{{ route('customer.cart.add', $table->table_number) }}" class="w-full bg-[#C67C4E] text-white px-6 py-4 rounded-2xl flex items-center justify-between shadow-lg active:scale-[0.99] transition-transform">
            <div class="flex items-center gap-3">
                <div class="bg-[#2F4B4E] w-6 h-6 rounded-md flex items-center justify-center text-[13px] font-bold text-white" x-text="cartCount">0</div>
                <span class="font-semibold text-[16px]">View Cart</span>
            </div>
            
            <span class="font-bold text-[16px]" x-text="formatPrice(cartTotal)">Rp 0</span>
        </a>
    </div>

    <!-- Product Detail Modal -->
    @include('components.product-detail-modal')
</div>

@push('scripts')
<script>
// Define Cart object
window.Cart = {
    add: function(itemId, price, name) {
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        
        let existingItem = cart.find(item => item.id === itemId);
        if (existingItem) {
            existingItem.quantity++;
            existingItem.subtotal = existingItem.price * existingItem.quantity;
        } else {
            cart.push({
                id: itemId,
                name: name,
                price: price,
                quantity: 1,
                subtotal: price
            });
        }
        
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Update badge
        const badge = document.getElementById('cart-badge');
        if (badge) {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            badge.textContent = totalItems;
            badge.classList.remove('hidden');
        }
        
        return true;
    }
};

document.addEventListener('alpine:init', () => {
    Alpine.data('menuHandler', () => ({
        activeCategory: 'all',
        cartCount: 0,
        cartTotal: 0,
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
        quantity: 1,
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
            this.updateCartFromServer();
            
            this.$nextTick(() => {
                this.sections = Array.from(document.querySelectorAll('.category-section'));
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
            this.quantity = 1;
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
            this.size = 'M';
            this.spiceLevel = 'mild';
            this.portion = 'regular';
            this.toppings = [];
            this.specialRequest = '';
            
            this.showDetail = true;
            document.body.style.overflow = 'hidden';
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

        addToCartWithOptions() {
            // Only save options relevant to product type
            const options = {};
            
            if (this.selectedProduct.type === 'beverage') {
                options.temperature = this.temperature;
                if (this.temperature === 'ice') {
                    options.iceLevel = this.iceLevel;
                }
                options.sugarLevel = this.sugarLevel;
                options.size = this.size;
                if (this.addOns.length > 0) {
                    options.addOns = this.addOns;
                }
            } else if (this.selectedProduct.type === 'food') {
                options.spiceLevel = this.spiceLevel;
                options.portion = this.portion;
                if (this.addOns.length > 0) {
                    options.addOns = this.addOns;
                }
            } else if (this.selectedProduct.type === 'snack') {
                options.portion = this.portion;
                if (this.sauces.length > 0) {
                    options.sauces = this.sauces;
                }
            } else if (this.selectedProduct.type === 'dessert') {
                options.portion = this.portion;
                if (this.toppings.length > 0) {
                    options.toppings = this.toppings;
                }
            }
            
            // Always save special request if provided
            if (this.specialRequest && this.specialRequest.trim() !== '') {
                options.specialRequest = this.specialRequest;
            }
            
            // Send to backend API
            fetch(`/order/{{ $table->table_number }}/cart`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    menu_id: this.selectedProduct.id,
                    quantity: this.quantity,
                    options: options
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count
                    this.cartCount = data.cart_count;
                    this.cartTotal = data.cart_total;
                    
                    // Close modal
                    this.showDetail = false;
                    document.body.style.overflow = '';
                    
                    // Show success message
                    console.log('Item added to cart!');
                    
                    // Vibration feedback
                    if (window.navigator && window.navigator.vibrate) {
                        window.navigator.vibrate(50);
                    }
                } else {
                    console.error('Error adding to cart:', data.message);
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Network error:', error);
                alert('Network error. Please try again.');
            });
            
            if (existingIndex > -1) {
                cart[existingIndex].quantity += this.quantity;
                // Recalculate subtotal based on options
                const itemTotal = this.calculateTotalPrice();
                cart[existingIndex].subtotal += itemTotal;
            } else {
                cart.push(cartItem);
            }
            
            // Save to localStorage
            localStorage.setItem('cart', JSON.stringify(cart));
            
            // Trigger update event
            window.dispatchEvent(new Event('storage'));
            
            // Close modal
            this.showDetail = false;
            document.body.style.overflow = '';
            
            // Update cart from server (no longer needed since addToCartWithOptions handles it)
            // this.updateCartFromServer();
        },

        calculateItemPrice() {
            let total = this.selectedProduct.priceRaw;
            
            // Add-ons pricing (beverages & food)
            if (this.addOns.length > 0) {
                const addonPrices = {
                    'extra-shot': 5000,
                    'whipped-cream': 3000,
                    'caramel-syrup': 3000,
                    'extra-cheese': 5000,
                    'extra-egg': 3000,
                    'extra-rice': 5000
                };
                this.addOns.forEach(addon => {
                    total += addonPrices[addon] || 0;
                });
            }
            
            // Toppings pricing (dessert)
            if (this.toppings.length > 0) {
                const toppingPrices = {
                    'chocolate': 3000,
                    'caramel': 3000,
                    'whipped': 5000,
                    'ice-cream': 8000
                };
                this.toppings.forEach(topping => {
                    total += toppingPrices[topping] || 0;
                });
            }
            
            // Sauces pricing (snack)
            if (this.sauces.length > 0) {
                const saucePrices = {
                    'ketchup': 0,
                    'mayonnaise': 0,
                    'chili': 0,
                    'bbq': 2000
                };
                this.sauces.forEach(sauce => {
                    total += saucePrices[sauce] || 0;
                });
            }
            
            // Size pricing for beverages
            if (this.selectedProduct.type === 'beverage' && this.size === 'large') {
                total += 8000;
            }
            
            // Portion/Size pricing difference
            if (this.portion === 'large' && (this.selectedProduct.type === 'food' || this.selectedProduct.type === 'snack')) {
                total += 5000;
            } else if (this.portion === 'large' && this.selectedProduct.type === 'dessert') {
                total += 8000;
            } else if (this.portion === 'small' && this.selectedProduct.type === 'snack') {
                total -= 5000;
            }
            
            return total;
        },

        calculateTotalPrice() {
            return this.calculateItemPrice() * this.quantity;
        },

        formatPrice(price) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
        },

        updateCartFromServer() {
            fetch(`/order/{{ $table->table_number }}/cart`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.cartCount = data.cart_count;
                    this.cartTotal = data.cart_total;
                }
            })
            .catch(error => {
                console.error('Error loading cart:', error);
                this.cartCount = 0;
                this.cartTotal = 0;
            });
        },

        scrollToCategory(id) {
            this.activeCategory = id;
            
            // Small delay to ensure Alpine has updated
            setTimeout(() => {
                const element = document.getElementById('cat-' + id);
                const container = document.getElementById('menu-content');
                
                if (element && container) {
                    const elementPosition = element.offsetTop;
                    const headerOffset = 160;
                    
                    container.scrollTo({
                        top: elementPosition - headerOffset,
                        behavior: 'smooth'
                    });
                }
            }, 50);
        },
        
        onScroll(e) {
            const container = e.target;
            const scrollPosition = container.scrollTop + 150; 

            for (const section of this.sections) {
                const top = section.offsetTop;
                const height = section.offsetHeight;
                
                if (scrollPosition >= top && scrollPosition < top + height) {
                    const id = section.dataset.id;
                    if (this.activeCategory !== id) {
                        this.activeCategory = id;
                    }
                    break;
                }
            }
        },

        // Legacy method - not used anymore since we have addToCartWithOptions
        addToCart(id, price, name) {
            // Cart.add(id, price, name);
            // this.updateCartFromServer();
            
            if (window.navigator && window.navigator.vibrate) {
                window.navigator.vibrate(50);
            }
        },

        formatPrice(price) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
        }
    }));
});
</script>
@endpush
@endsection
