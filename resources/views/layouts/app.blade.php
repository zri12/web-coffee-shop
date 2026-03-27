<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $systemSettings['cafe_name'] ?? 'Cafe') - Cafe Web Ordering</title>
    @include('layouts.partials.favicon')
    
    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800;900&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-25..0&display=swap" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#cf6317",
                        "primary-dark": "#b05d0d",
                        "on-primary": "#ffffff",
                        "on-surface-variant": "#976d4e",
                        "error-container": "#ffdad6",
                        "secondary-fixed": "#ffdbca",
                        "secondary": "#f4e9df",
                        "primary-fixed": "#ffdbca",
                        "background": "#fcfaf8",
                        "background-light": "#fcfaf8",
                        "background-dark": "#221910",
                        "surface-container-high": "#f3ece7",
                        "surface-dim": "#ead6cd",
                        "primary-container": "#cf6317",
                        "on-secondary": "#1b130e",
                        "tertiary": "#006097",
                        "outline-variant": "#f3ece7",
                        "primary-fixed-dim": "#ffb68e",
                        "on-primary-fixed-variant": "#773300",
                        "surface": "#fcfaf8",
                        "surface-light": "#FFFFFF",
                        "surface-dark": "#2D2115",
                        "on-primary-container": "#ffffff",
                        "surface-container-low": "#fcfaf8",
                        "surface-tint": "#cf6317",
                        "surface-container-highest": "#dec1b3",
                        "tertiary-fixed": "#cee5ff",
                        "surface-bright": "#ffffff",
                        "outline": "#976d4e",
                        "on-error-container": "#93000a",
                        "tertiary-fixed-dim": "#96ccff",
                        "secondary-container": "#f3ece7",
                        "tertiary-container": "#cee5ff",
                        "on-tertiary": "#ffffff",
                        "on-secondary-fixed-variant": "#6a3a1e",
                        "inverse-surface": "#211811",
                        "secondary-fixed-dim": "#fdb791",
                        "on-tertiary-fixed": "#001d32",
                        "surface-variant": "#f3ece7",
                        "surface-container": "#f4e9df",
                        "surface-container-lowest": "#ffffff",
                        "on-primary-fixed": "#331200",
                        "inverse-on-surface": "#ffffff",
                        "on-secondary-fixed": "#331200",
                        "on-tertiary-container": "#001d32",
                        "on-surface": "#1b130e",
                        "text-main": "#3E2723",
                        "text-subtle": "#897561",
                        "error": "#ba1a1a",
                        "on-error": "#ffffff",
                        "on-background": "#1b130e",
                        "on-tertiary-fixed-variant": "#004a76",
                        "inverse-primary": "#ffb68e",
                        "on-secondary-container": "#976d4e"
                    },
                    fontFamily: {
                        "display": ["Plus Jakarta Sans", "sans-serif"],
                        "headline": ["Plus Jakarta Sans"],
                        "body": ["Plus Jakarta Sans"],
                        "label": ["Plus Jakarta Sans"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.5rem",
                        "lg": "0.75rem",
                        "xl": "1rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>

    {{-- Icon Guard: mencegah teks icon muncul & diterjemahkan browser --}}
    @include('layouts.partials.icon-guard')

    @stack('styles')
    
    <style>
        /* Prevent horizontal scroll and normalize body */
        html, body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            width: 100%;
            max-width: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .material-symbols-outlined { 
            font-family: 'Material Symbols Outlined' !important;
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; 
        }
        .hero-gradient { background: linear-gradient(180deg, rgba(252,250,248,0) 0%, rgba(252,250,248,1) 100%); }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-text-main dark:text-white font-display antialiased" x-data="{ mobileMenuOpen: false }">
    
    <!-- Top Navigation Bar -->
    <header class="sticky top-0 w-full z-50 bg-white/95 dark:bg-stone-900/95 backdrop-blur-md border-b border-orange-200/50 dark:border-stone-800 shadow-sm dark:shadow-none">
        <nav class="flex justify-between items-center w-full px-6 py-4 max-w-7xl mx-auto">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="text-2xl font-black text-[#1b130e] dark:text-stone-100 tracking-tighter hover:opacity-80 transition flex items-center gap-3">
                <div class="size-8 text-primary">
                    <svg class="w-full h-full" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path d="M42.1739 20.1739L27.8261 5.82609C29.1366 7.13663 28.3989 10.1876 26.2002 13.7654C24.8538 15.9564 22.9595 18.3449 20.6522 20.6522C18.3449 22.9595 15.9564 24.8538 13.7654 26.2002C10.1876 28.3989 7.13663 29.1366 5.82609 27.8261L20.1739 42.1739C21.4845 43.4845 24.5355 42.7467 28.1133 40.548C30.3042 39.2016 32.6927 37.3073 35 35C37.3073 32.6927 39.2016 30.3042 40.548 28.1133C42.7467 24.5355 43.4845 21.4845 42.1739 20.1739Z" fill="currentColor"></path>
                    </svg>
                </div>
                <span>{{ $systemSettings['cafe_name'] ?? 'Amber Roast' }}</span>
            </a>

            <!-- Desktop Nav Links -->
            <div class="hidden md:flex items-center gap-8">
                <a class="text-sm font-semibold tracking-wide transition-colors {{ request()->routeIs('home') ? 'text-primary border-b-2 border-primary pb-1' : 'text-[#1b130e] dark:text-stone-300 hover:text-primary' }}" href="{{ route('home') }}">Home</a>
                <a class="text-sm font-semibold tracking-wide transition-colors {{ request()->routeIs('menu.*') ? 'text-primary border-b-2 border-primary pb-1' : 'text-[#1b130e] dark:text-stone-300 hover:text-primary' }}" href="{{ route('menu.index') }}">Menu</a>
                <a class="text-sm font-semibold tracking-wide transition-colors {{ request()->routeIs('track') ? 'text-primary border-b-2 border-primary pb-1' : 'text-[#1b130e] dark:text-stone-300 hover:text-primary' }}" href="{{ route('track') }}">Order</a>
                @auth
                <a class="text-sm font-semibold tracking-wide text-[#1b130e] dark:text-stone-300 hover:text-primary transition-colors" href="@if(auth()->user()->role === 'cashier'){{ route('cashier.incoming-orders') }}@elseif(auth()->user()->role === 'manager'){{ route('manager.dashboard') }}@else{{ route('admin.dashboard') }}@endif">Dashboard</a>
                @endauth
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-4">
                <a href="{{ route('cart') }}" class="relative p-2 hover:bg-orange-50 dark:hover:bg-stone-800 rounded-lg transition-all text-[#1b130e] dark:text-stone-100 group">
                    <span class="material-symbols-outlined">shopping_cart</span>
                    <span id="cart-badge" class="absolute -top-1 -right-1 bg-primary text-white text-[10px] font-bold h-4 min-w-[16px] px-1 rounded-full flex items-center justify-center hidden border-2 border-[#fcfaf8] dark:border-stone-900">0</span>
                </a>
                
                @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-5 py-2 bg-stone-100 dark:bg-stone-800 text-on-background dark:text-stone-100 rounded-full font-semibold text-sm shadow-sm hover:scale-95 transition-all">
                        Logout
                    </button>
                </form>
                @else
                <a href="{{ route('login') }}" class="px-5 py-2 bg-primary text-white rounded-full font-semibold text-sm shadow-lg hover:scale-95 transition-all">
                    Login
                </a>
                @endauth

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 hover:bg-orange-50 dark:hover:bg-stone-800 rounded-lg transition-all text-[#1b130e] dark:text-stone-100">
                    <span class="material-symbols-outlined">menu</span>
                </button>
            </div>
        </nav>
        
        <!-- Mobile Menu Overlay -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="md:hidden border-t border-orange-100 dark:border-stone-800 py-4 bg-[#fcfaf8]/95 dark:bg-stone-900/95 backdrop-blur-md absolute left-0 right-0 top-full shadow-xl z-50">
            <nav class="flex flex-col gap-2 px-6 font-semibold">
                <a href="{{ route('home') }}" class="py-2 text-sm {{ request()->routeIs('home') ? 'text-primary' : 'text-[#1b130e] dark:text-stone-300' }}">Home</a>
                <a href="{{ route('menu.index') }}" class="py-2 text-sm {{ request()->routeIs('menu.*') ? 'text-primary' : 'text-[#1b130e] dark:text-stone-300' }}">Menu</a>
                <a href="{{ route('track') }}" class="py-2 text-sm {{ request()->routeIs('track') ? 'text-primary' : 'text-[#1b130e] dark:text-stone-300' }}">Order Status</a>
                <hr class="my-2 border-orange-100 dark:border-stone-800">
                @auth
                <a class="py-2 text-sm text-[#1b130e] dark:text-stone-300" href="@if(auth()->user()->role === 'cashier'){{ route('cashier.incoming-orders') }}@elseif(auth()->user()->role === 'manager'){{ route('manager.dashboard') }}@else{{ route('admin.dashboard') }}@endif">Dashboard</a>
                <form action="{{ route('logout') }}" method="POST" class="py-2">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 font-bold">Logout</button>
                </form>
                @else
                <a href="{{ route('login') }}" class="py-2 text-sm text-primary">Login</a>
                @endauth
            </nav>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="flex-grow w-full m-0 p-0 border-none">
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-[#1b130e] text-stone-300 pt-20 pb-10 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                <!-- Brand Info -->
                <div class="flex flex-col gap-6">
                    <div class="flex items-center gap-3">
                        <div class="size-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-3xl">coffee</span>
                        </div>
                        <span class="font-black text-2xl text-white tracking-tight">{{ $systemSettings['cafe_name'] ?? 'Amber Roast' }}</span>
                    </div>
                    <p class="text-stone-400 leading-relaxed text-sm">
                        Experience the art of artisanal coffee. Every bean is roasted with passion to bring you the perfect cup since 2010.
                    </p>
                </div>

                <!-- Quick Links -->
                <div class="flex flex-col gap-6">
                    <h4 class="font-extrabold text-white uppercase tracking-widest text-xs">Navigation</h4>
                    <ul class="flex flex-col gap-4">
                        <li><a href="{{ route('home') }}" class="text-stone-400 hover:text-primary hover:translate-x-1 transition-all inline-block">Home</a></li>
                        <li><a href="{{ route('menu.index') }}" class="text-stone-400 hover:text-primary hover:translate-x-1 transition-all inline-block">Our Menu</a></li>
                        <li><a href="#" class="text-stone-400 hover:text-primary hover:translate-x-1 transition-all inline-block">Orders</a></li>
                        <li><a href="#" class="text-stone-400 hover:text-primary hover:translate-x-1 transition-all inline-block">Contact</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="flex flex-col gap-6">
                    <h4 class="font-extrabold text-white uppercase tracking-widest text-xs">Reach Us</h4>
                    <ul class="flex flex-col gap-5">
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-primary text-xl">location_on</span>
                            <span class="text-stone-400 text-sm leading-relaxed">
                                123 Coffee Bean Blvd,<br>Brew City, BC 90210
                            </span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary text-xl">call</span>
                            <span class="text-stone-400 text-sm">(555) 123-4567</span>
                        </li>
                    </ul>
                </div>

                <!-- Opening Hours -->
                <div class="flex flex-col gap-6">
                    <h4 class="font-extrabold text-white uppercase tracking-widest text-xs">Brewing Hours</h4>
                    <div class="bg-white/5 p-6 rounded-3xl border border-white/5">
                        <ul class="flex flex-col gap-4">
                            <li class="flex justify-between items-center text-sm">
                                <span class="text-stone-500">Mon - Fri</span>
                                <span class="font-bold text-white">7 am - 8 pm</span>
                            </li>
                            <li class="flex justify-between items-center text-sm">
                                <span class="text-stone-500">Sat - Sun</span>
                                <span class="font-bold text-primary">8 am - 9 pm</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-xs text-stone-500 font-medium">
                    © {{ date('Y') }} {{ $systemSettings['cafe_name'] ?? 'Amber Roast' }}. All rights reserved.
                </p>
                <div class="flex gap-6">
                    <a href="#" class="text-stone-500 hover:text-primary transition-all">
                        <svg class="size-5 fill-current" viewBox="0 0 24 24"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24 l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/></svg>
                    </a>
                    <a href="#" class="text-stone-500 hover:text-primary transition-all">
                        <svg class="size-5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    
    @stack('scripts')
    
    <script>
        // Global Cart Functionality
        window.Cart = {
            optionPriceMaps: {
                addOns: {
                    'extra-shot': 5000,
                    'whipped-cream': 3000,
                    'caramel-syrup': 3000,
                    'extra-cheese': 5000,
                    'extra-egg': 3000,
                    'extra-rice': 5000
                },
                toppings: {
                    'chocolate': 3000,
                    'caramel': 3000,
                    'whipped': 5000,
                    'ice-cream': 8000
                },
                sauces: {
                    'bbq': 2000,
                    'ketchup': 0,
                    'mayonnaise': 0,
                    'chili': 0
                }
            },

            variantPriceMaps: {
                beverage: {
                    size: {
                        regular: { name: 'Regular', additional_price: 0 },
                        large: { name: 'Large', additional_price: 8000 }
                    }
                },
                food: {
                    portion: {
                        regular: { name: 'Regular', additional_price: 0 },
                        large: { name: 'Large', additional_price: 5000 }
                    }
                },
                snack: {
                    portion: {
                        small: { name: 'Small', additional_price: -5000 },
                        regular: { name: 'Regular', additional_price: 0 },
                        large: { name: 'Large', additional_price: 5000 }
                    }
                },
                dessert: {
                    portion: {
                        regular: { name: 'Regular', additional_price: 0 },
                        large: { name: 'Large', additional_price: 8000 }
                    }
                }
            },

            toNumber(value) {
                const parsed = Number(value);
                return Number.isFinite(parsed) ? parsed : 0;
            },

            formatLabel(value) {
                return String(value || '')
                    .split('-')
                    .map(word => word ? word.charAt(0).toUpperCase() + word.slice(1) : '')
                    .join(' ');
            },

            formatPrice(price) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(this.toNumber(price));
            },

            calculateAddonEntries(options = {}) {
                const entries = [];
                const addOptionEntries = (field, type) => {
                    const selected = Array.isArray(options[field]) ? options[field] : [];
                    const priceMap = this.optionPriceMaps[field] || {};
                    selected.forEach(key => {
                        entries.push({
                            type,
                            key,
                            name: this.formatLabel(key),
                            price: this.toNumber(priceMap[key])
                        });
                    });
                };

                addOptionEntries('addOns', 'add_on');
                addOptionEntries('toppings', 'topping');
                addOptionEntries('sauces', 'sauce');

                return entries;
            },

            getVariantDefinition(productType = 'beverage', options = {}) {
                const typeConfig = this.variantPriceMaps[productType] || {};
                if (typeConfig.size) {
                    const key = options.size;
                    return key && typeConfig.size[key]
                        ? { key, name: typeConfig.size[key].name, additional_price: this.toNumber(typeConfig.size[key].additional_price), field: 'size' }
                        : null;
                }

                if (typeConfig.portion) {
                    const key = options.portion;
                    return key && typeConfig.portion[key]
                        ? { key, name: typeConfig.portion[key].name, additional_price: this.toNumber(typeConfig.portion[key].additional_price), field: 'portion' }
                        : null;
                }

                return null;
            },

            calculatePriceBreakdown(basePrice, productType = 'beverage', options = {}) {
                const base = this.toNumber(basePrice);
                const selectedVariant = this.getVariantDefinition(productType, options);
                const variantAdditionalPrice = this.toNumber(selectedVariant?.additional_price || 0);
                const addonEntries = this.calculateAddonEntries(options);
                const addonsTotal = addonEntries.reduce((sum, addon) => sum + this.toNumber(addon.price), 0);
                const finalPrice = base + variantAdditionalPrice + addonsTotal;

                return {
                    base_price: base,
                    selected_variant: selectedVariant
                        ? { name: selectedVariant.name, additional_price: variantAdditionalPrice }
                        : null,
                    selected_addons: addonEntries.map(addon => ({
                        name: addon.name,
                        price: this.toNumber(addon.price)
                    })),
                    variant_additional_price: variantAdditionalPrice,
                    addons_total: addonsTotal,
                    final_price_per_item: finalPrice
                };
            },

            recalculateItem(item) {
                const normalized = { ...(item || {}) };
                const options = normalized.options && typeof normalized.options === 'object' ? normalized.options : {};
                const quantity = Math.max(1, parseInt(normalized.quantity ?? 1, 10) || 1);
                const productType = normalized.type || options.type || 'beverage';
                const basePrice = this.toNumber(
                    normalized.base_price ??
                    normalized.basePrice ??
                    normalized.price ??
                    normalized.total_price ??
                    normalized.final_price ??
                    normalized.finalPrice
                );
                const pricing = this.calculatePriceBreakdown(basePrice, productType, options);
                const selectedVariant = normalized.selected_variant ?? normalized.selectedVariant ?? pricing.selected_variant;
                const selectedAddons = Array.isArray(normalized.selected_addons) ? normalized.selected_addons : pricing.selected_addons;
                const addonEntries = Array.isArray(normalized.addons) && normalized.addons.length > 0
                    ? normalized.addons.map(addon => ({
                        name: addon.name || this.formatLabel(addon.key),
                        key: addon.key || (addon.name ? String(addon.name).toLowerCase().replace(/\s+/g, '-') : ''),
                        type: addon.type || 'add_on',
                        price: this.toNumber(addon.price)
                    }))
                    : selectedAddons.map(addon => ({
                        name: addon.name,
                        key: String(addon.name).toLowerCase().replace(/\s+/g, '-'),
                        type: 'add_on',
                        price: this.toNumber(addon.price)
                    }));

                const addonTotal = this.toNumber(
                    normalized.addon_total ??
                    normalized.addons_total ??
                    pricing.addons_total
                );
                const totalPrice = this.toNumber(
                    normalized.final_price_per_item ??
                    normalized.total_price ??
                    normalized.totalPrice ??
                    normalized.final_price ??
                    normalized.finalPrice ??
                    pricing.final_price_per_item
                );
                const subtotal = this.toNumber(
                    normalized.final_price_total ??
                    normalized.subtotal ??
                    (totalPrice * quantity)
                );

                return {
                    ...normalized,
                    id: normalized.id ?? normalized.product_id,
                    product_id: normalized.product_id ?? normalized.id,
                    name: normalized.name || 'Item',
                    product_name: normalized.product_name || normalized.name || 'Item',
                    image: normalized.image || null,
                    type: productType,
                    options,
                    quantity,
                    addons: addonEntries,
                    selected_variant: selectedVariant,
                    selected_addons: selectedAddons,
                    base_price: basePrice,
                    basePrice,
                    price: basePrice,
                    addon_total: addonTotal,
                    total_price: totalPrice,
                    totalPrice,
                    final_price: totalPrice,
                    finalPrice: totalPrice,
                    final_price_per_item: totalPrice,
                    final_price_total: subtotal,
                    subtotal,
                    cartItemId: normalized.cartItemId || normalized.cart_item_id || (Date.now() + Math.random())
                };
            },

            get items() {
                try {
                    const raw = JSON.parse(localStorage.getItem('cart') || '[]');
                    if (!Array.isArray(raw)) return [];
                    return raw.map(item => this.recalculateItem(item));
                } catch (error) {
                    return [];
                }
            },
            
            add(id, name, basePrice, image = null, quantity = 1, options = {}) {
                const cart = this.items;
                const normalizedOptions = options && typeof options === 'object' ? options : {};
                const productType = normalizedOptions.type || 'beverage';
                const pricing = this.calculatePriceBreakdown(basePrice, productType, normalizedOptions);
                const addonEntries = pricing.selected_addons.map(addon => ({
                    name: addon.name,
                    key: String(addon.name).toLowerCase().replace(/\s+/g, '-'),
                    type: 'add_on',
                    price: addon.price
                }));
                const base = this.toNumber(basePrice);
                const qty = Math.max(1, parseInt(quantity, 10) || 1);
                const totalPrice = pricing.final_price_per_item;

                const cartItem = this.recalculateItem({
                    id: id,
                    product_id: id,
                    name: name,
                    product_name: name,
                    image,
                    quantity: qty,
                    type: productType,
                    options: normalizedOptions,
                    selected_variant: pricing.selected_variant,
                    selected_addons: pricing.selected_addons,
                    addons: addonEntries,
                    base_price: base,
                    total_price: totalPrice,
                    final_price_per_item: totalPrice,
                    final_price_total: totalPrice * qty,
                    subtotal: totalPrice * qty,
                    cartItemId: Date.now() + Math.random()
                });

                console.log("Item added to cart:", cartItem);
                cart.push(cartItem);
                this.save(cart);
                this.showToast('Item ditambahkan ke keranjang');
                return cartItem;
            },
            
            remove(identifier) {
                const cart = this.items.filter(item => {
                    return item.cartItemId !== identifier && item.id !== identifier;
                });
                this.save(cart);
            },
            
            clear() {
                this.save([]);
            },
            
            updateQuantity(identifier, quantity) {
                const cart = this.items;
                const index = cart.findIndex(item => item.cartItemId === identifier || item.id === identifier);
                if (index === -1) return;

                const qty = Math.max(1, parseInt(quantity, 10) || 1);
                cart[index] = this.recalculateItem({
                    ...cart[index],
                    quantity: qty
                });

                this.save(cart);
            },
            
            removeByIndex(index) {
                const cart = this.items;
                if (index >= 0 && index < cart.length) {
                    cart.splice(index, 1);
                    this.save(cart);
                }
            },
            
            updateQuantityByIndex(index, quantity) {
                const cart = this.items;
                if (index >= 0 && index < cart.length) {
                    const qty = Math.max(1, parseInt(quantity, 10) || 1);
                    cart[index] = this.recalculateItem({
                        ...cart[index],
                        quantity: qty
                    });
                    this.save(cart);
                }
            },
            
            save(cart) {
                const normalizedCart = Array.isArray(cart) ? cart.map(item => this.recalculateItem(item)) : [];
                localStorage.setItem('cart', JSON.stringify(normalizedCart));
                this.updateBadge();
                window.dispatchEvent(new CustomEvent('cart-updated', {
                    detail: normalizedCart
                }));
            },
            
            updateBadge() {
                const cart = this.items;
                const badge = document.getElementById('cart-badge');
                const totalLabel = document.getElementById('cart-total-inline');
                if (!badge) return;
                
                const totalItems = cart.reduce((sum, item) => sum + Math.max(1, parseInt(item.quantity || 1, 10) || 1), 0);
                const totalPrice = cart.reduce((sum, item) => sum + this.toNumber(item.final_price_total ?? item.subtotal), 0);
                
                if (totalItems > 0) {
                    badge.classList.remove('hidden');
                    badge.innerText = totalItems > 99 ? '99+' : totalItems;
                    if (totalLabel) {
                        totalLabel.classList.remove('hidden');
                        totalLabel.innerText = this.formatPrice(totalPrice);
                    }
                } else {
                    badge.classList.add('hidden');
                    if (totalLabel) {
                        totalLabel.classList.add('hidden');
                        totalLabel.innerText = this.formatPrice(0);
                    }
                }
            },
            
            showToast(message) {
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-4 right-4 z-50 bg-primary text-white px-6 py-3 rounded-xl shadow-lg animate-fade-in flex items-center gap-2';
                toast.innerHTML = `<span class="material-symbols-outlined">check_circle</span><span>${message}</span>`;
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }
        };

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Sembunyikan badge & total dulu sebelum sync dengan server
            const badge = document.getElementById('cart-badge');
            const totalLabel = document.getElementById('cart-total-inline');
            if (badge) badge.classList.add('hidden');
            if (totalLabel) totalLabel.classList.add('hidden');

            // Sync badge dengan server session cart (sumber kebenaran)
            fetch('/cart/count', { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
                .then(res => res.json())
                .then(data => {
                    if (typeof data.count !== 'undefined') {
                        if (data.count > 0) {
                            // Ada item di server session — tampilkan
                            if (badge) {
                                badge.classList.remove('hidden');
                                badge.innerText = data.count > 99 ? '99+' : data.count;
                            }
                            if (totalLabel) {
                                totalLabel.classList.remove('hidden');
                                totalLabel.innerText = Cart.formatPrice(data.total || 0);
                            }
                        } else {
                            // Server session kosong — bersihkan localStorage & sembunyikan semua
                            localStorage.removeItem('cart');
                            if (badge) badge.classList.add('hidden');
                            if (totalLabel) totalLabel.classList.add('hidden');
                        }
                    }
                })
                .catch(() => {
                    // Fallback ke localStorage jika fetch gagal
                    Cart.updateBadge();
                });
        });
    </script>
</body>
</html>
