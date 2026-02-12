@extends('layouts.customer')

@php
    // Precompute counts to avoid N+1 calls in blade
    $categoryMenuCounts = $categories->mapWithKeys(fn($c) => [$c->id => $c->menus->count()]);
@endphp

@section('content')
<div
    x-data="menuPage()"
    x-init="init()"
    class="min-h-screen bg-[#faf8f6] text-[#1f160f] flex flex-col pb-28"
>
    <!-- Top bar -->
    <header class="px-5 pt-6 pb-3 flex items-center justify-between">
        <div>
            <p class="text-xs text-[#9b9b9b]">Table</p>
            <div class="text-xl font-semibold text-[#2f2d2c]">
                #{{ $table->table_number }}
            </div>
        </div>
        <div class="flex items-center gap-2 text-sm">
            <span class="material-symbols-outlined text-[#c67c4e]">wifi</span>
            <span class="text-[#c67c4e] font-medium">Order Online</span>
        </div>
    </header>

    <!-- Category chips -->
    <section class="px-5 pb-4">
        <div class="flex gap-2 overflow-x-auto hide-scrollbar" id="category-chips">
            @foreach($categories as $category)
                <button
                    type="button"
                    @click="scrollToSection('cat-{{ $category->slug }}')"
                    class="whitespace-nowrap px-4 py-2 rounded-full border border-[#e6dcd2] bg-white text-sm font-medium text-[#3e2f24] hover:border-[#c67c4e] hover:text-[#c67c4e] transition"
                >
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </section>

    <!-- Menu sections -->
    <section class="flex-1 space-y-6 px-5 pb-8">
        @foreach($categories as $category)
            <div id="cat-{{ $category->slug }}" class="space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-[#2f2d2c]">{{ $category->name }}</h2>
                    </div>
                    <span class="text-xs px-3 py-1 rounded-full bg-[#f3e8df] text-[#7c5b3a]">
                        {{ $categoryMenuCounts[$category->id] ?? 0 }} items
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($category->menus as $menu)
                        @php
                            $menuPayload = [
                                'id' => $menu->id,
                                'name' => $menu->name,
                                'price' => (float) $menu->price,
                                'image' => $menu->display_image_url,
                                'category' => $menu->category->slug ?? $category->slug ?? ''
                            ];
                        @endphp
                        <article class="bg-white rounded-2xl shadow-sm border border-[#f0e7df] overflow-hidden flex">
                            <div class="w-28 h-28 shrink-0 bg-[#f7f2ec]">
                                <img
                                    src="{{ $menu->display_image_url }}"
                                    alt="{{ $menu->name }}"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                    onerror="this.src='{{ $menu->placeholder_image_url }}'"
                                >
                            </div>

                            <div class="flex-1 p-3 flex flex-col gap-2">
                                <div>
                                    <h3 class="text-base font-semibold text-[#2f2d2c] leading-tight">{{ $menu->name }}</h3>
                                    @if($menu->description)
                                        <p class="text-xs text-[#8c7a6b] line-clamp-2">{{ $menu->description }}</p>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-semibold text-[#c67c4e]">{{ $menu->formatted_price }}</div>
                                    @if($menu->is_available)
                                        <button
                                            type="button"
                                            class="px-4 py-2 rounded-full bg-[#c67c4e] text-white text-sm font-semibold hover:bg-[#b06b3e] active:scale-95 transition disabled:opacity-60 disabled:cursor-not-allowed js-add-menu"
                                            @click.prevent.stop="showProductDetail({
                                                id: {{ $menu->id }},
                                                name: @js($menu->name),
                                                priceRaw: {{ (float) $menu->price }},
                                                price: @js($menu->formatted_price),
                                                description: @js($menu->description),
                                                image: @js($menu->display_image_url),
                                                isFeatured: {{ $menu->is_featured ? 'true' : 'false' }},
                                                category: @js($menu->category->slug ?? $category->slug ?? '')
                                            })"
                                            :disabled="busy"
                                        >
                                            <span class="material-symbols-outlined align-middle text-base">add</span>
                                            <span class="align-middle">Add</span>
                                        </button>
                                    @else
                                        <span class="text-xs text-red-500 font-semibold">Sold out</span>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="text-sm text-[#9b9b9b]">Belum ada menu pada kategori ini.</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </section>

    <!-- Floating cart bar -->
    <div
        class="fixed left-0 right-0 bottom-4 px-5"
        x-show="cartCount > 0"
        x-transition.opacity
        x-cloak
    >
        <div class="bg-[#2f2d2c] text-white rounded-2xl shadow-xl flex items-center justify-between px-4 py-3">
            <div>
                <div class="text-xs text-white/70">Items</div>
                <div class="text-lg font-semibold" x-text="cartCount + ' • ' + formatRupiah(cartTotal)"></div>
            </div>
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="clearCart()"
                        class="text-white/70 hover:text-white transition"
                        >
                    <span class="material-symbols-outlined">delete</span>
                </button>
                <button
                    type="button"
                    @click="goToCart()"
                    class="bg-white text-[#2f2d2c] px-4 py-2 rounded-full text-sm font-semibold hover:bg-[#f6f2ed] transition"
                >
                    View Cart
                </button>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div
        x-show="toast.show"
        x-transition
        x-cloak
        class="fixed inset-x-0 bottom-20 px-5"
    >
        <div class="bg-white border border-[#f0e7df] shadow-xl rounded-xl px-4 py-3 flex items-start gap-3">
            <div class="shrink-0">
                <span class="material-symbols-outlined" :class="toast.type === 'error' ? 'text-red-500' : 'text-emerald-600'" x-text="toast.icon"></span>
            </div>
            <div>
                <p class="text-sm font-semibold text-[#2f2d2c]" x-text="toast.title"></p>
                <p class="text-xs text-[#8c7a6b]" x-text="toast.message"></p>
            </div>
            <button class="ml-auto text-[#9b9b9b] hover:text-[#c67c4e]" @click="toast.show = false">
                <span class="material-symbols-outlined text-base">close</span>
            </button>
        </div>
    </div>

    @include('components.product-detail-modal')
</div>
@endsection

@push('scripts')
<script>
    function menuPage() {
        return {
            tableNumber: @json($table->table_number),
            cartCount: 0,
            cartTotal: 0,
            busy: false,
            toast: {
                show: false,
                type: 'success',
                title: '',
                message: '',
                icon: 'check_circle'
            },
            init() {
                const previousTable = localStorage.getItem('table_number');
                if (previousTable && previousTable !== String(this.tableNumber)) {
                    localStorage.removeItem('cart'); // clear cart when switching tables
                }
                localStorage.setItem('table_number', this.tableNumber);
                window.Cart?.setTable?.(this.tableNumber);
                this.refreshCart();
            },
            scrollToSection(id) {
                const el = document.getElementById(id);
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            },
            formatRupiah(value) {
                const number = Number(value || 0);
                return 'Rp ' + number.toLocaleString('id-ID');
            },
            resolveType(slug = '') {
                const s = (slug || '').toLowerCase();
                if (s.includes('coffee') || s.includes('kopi') || s.includes('drink')) return 'beverage';
                if (s.includes('snack')) return 'snack';
                if (s.includes('dessert')) return 'dessert';
                return 'food';
            },
            loadCart() {
                if (window.Cart?.getItems) return window.Cart.getItems();
                try {
                    return JSON.parse(localStorage.getItem('cart') || '[]');
                } catch (e) {
                    return [];
                }
            },
            saveCart(cart) {
                if (window.Cart?.save) {
                    window.Cart.items = cart;
                    window.Cart.save();
                } else {
                    localStorage.setItem('cart', JSON.stringify(cart));
                    window.dispatchEvent(new CustomEvent('cart-updated', { detail: cart }));
                }
            },
            syncCartTotals(cart) {
                const safeCart = cart || [];
                this.cartCount = safeCart.reduce((sum, item) => sum + (parseInt(item.quantity ?? 1, 10) || 1), 0);
                this.cartTotal = safeCart.reduce((sum, item) => {
                    const qty = parseInt(item.quantity ?? 1, 10) || 1;
                    const price = Number(
                        item.total_price ?? item.totalPrice ?? item.final_price ?? item.finalPrice ?? item.price ?? item.base_price ?? 0
                    ) || 0;
                    return sum + price * qty;
                }, 0);
            },
            refreshCart() {
                const cart = this.loadCart();
                this.syncCartTotals(cart);
            },
            clearCart() {
                if (window.Cart?.clear) {
                    window.Cart.clear();
                } else {
                    localStorage.removeItem('cart');
                    window.dispatchEvent(new CustomEvent('cart-cleared'));
                }
                this.refreshCart();
            },
            showProductDetail(payload) {
                const type = (() => {
                    const s = (payload.category || '').toLowerCase();
                    if (s.includes('coffee') || s.includes('kopi') || s.includes('drink')) return 'beverage';
                    if (s.includes('snack')) return 'snack';
                    if (s.includes('dessert')) return 'dessert';
                    if (s.includes('food') || s.includes('makanan')) return 'food';
                    return 'food';
                })();
                this.selectedProduct = {
                    id: payload.id,
                    name: payload.name,
                    priceRaw: payload.priceRaw,
                    price: payload.price,
                    description: payload.description || '',
                    image: payload.image || '',
                    isFeatured: payload.isFeatured || false,
                    type
                };
                this.resetOptions();
                this.showDetail = true;
                document.body.style.overflow = 'hidden';
            },
            resetOptions() {
                this.temperature = 'ice';
                this.iceLevel = 'normal';
                this.sugarLevel = 'normal';
                this.size = null;
                this.spiceLevel = 'mild';
                this.portion = null;
                this.toppings = [];
                this.addOns = [];
                this.sauces = [];
                this.specialRequest = '';
            },
            buildOptions() {
                const opts = {};
                if (this.selectedProduct.type === 'beverage') {
                    opts.temperature = this.temperature;
                    if (this.temperature === 'ice') opts.iceLevel = this.iceLevel;
                    opts.sugarLevel = this.sugarLevel;
                    if (this.size) opts.size = this.size;
                    if (this.addOns.length) opts.addOns = this.addOns;
                } else if (this.selectedProduct.type === 'food') {
                    opts.spiceLevel = this.spiceLevel;
                    if (this.portion) opts.portion = this.portion;
                    if (this.addOns.length) opts.addOns = this.addOns;
                } else if (this.selectedProduct.type === 'snack') {
                    if (this.portion) opts.portion = this.portion;
                    if (this.sauces.length) opts.sauces = this.sauces;
                } else if (this.selectedProduct.type === 'dessert') {
                    if (this.portion) opts.portion = this.portion;
                    if (this.toppings.length) opts.toppings = this.toppings;
                }
                if (this.specialRequest.trim()) opts.specialRequest = this.specialRequest.trim();
                return opts;
            },
            calculatePriceWithOptions(base, type, options) {
                let total = Number(base) || 0;
                if (type === 'beverage') {
                    if (options.size === 'large') total += 8000;
                    (options.addOns || []).forEach(a => {
                        if (a === 'extra-shot') total += 5000;
                        if (a === 'whipped-cream' || a === 'caramel-syrup') total += 3000;
                    });
                } else if (type === 'food' || type === 'snack' || type === 'dessert') {
                    if (options.portion === 'large') total += 5000;
                    if (options.portion === 'small') total -= 5000;
                    (options.addOns || []).forEach(a => {
                        if (a === 'extra-cheese' || a === 'extra-rice') total += 5000;
                        if (a === 'extra-egg') total += 3000;
                    });
                    (options.toppings || []).forEach(t => {
                        if (t === 'chocolate' || t === 'caramel') total += 3000;
                        if (t === 'whipped') total += 5000;
                        if (t === 'ice-cream') total += 8000;
                    });
                    (options.sauces || []).forEach(s => { if (s === 'bbq') total += 2000; });
                }
                return total;
            },
            canAddToCart() {
                if (!this.showDetail) return true;
                const type = this.selectedProduct.type;
                if (type === 'beverage') return !!this.size;
                if (['food','snack','dessert'].includes(type)) return !!this.portion;
                return true;
            },
            calculateItemPrice() {
                const opts = this.buildOptions();
                return this.calculatePriceWithOptions(this.selectedProduct.priceRaw, this.selectedProduct.type, opts);
            },
            async addToCartWithOptions() {
                if (!this.canAddToCart()) return;
                this.busy = true;
                try {
                    window.Cart?.setTable?.(this.tableNumber);
                    localStorage.setItem('table_number', this.tableNumber);

                    const options = this.buildOptions();
                    options.type = this.selectedProduct.type;
                    const finalPrice = this.calculatePriceWithOptions(this.selectedProduct.priceRaw, this.selectedProduct.type, options);

                    const cart = this.loadCart();
                    cart.push({
                        id: this.selectedProduct.id,
                        name: this.selectedProduct.name,
                        price: this.selectedProduct.priceRaw,
                        base_price: this.selectedProduct.priceRaw,
                        final_price: finalPrice,
                        final_price_per_item: finalPrice,
                        final_price_total: finalPrice,
                        quantity: 1,
                        image: this.selectedProduct.image,
                        type: this.selectedProduct.type,
                        options
                    });
                    this.saveCart(cart);
                    this.refreshCart();
                    this.showDetail = false;
                    document.body.style.overflow = '';
                    this.showToast('Ditambahkan', `${this.selectedProduct.name} masuk keranjang.`, 'check_circle', 'success');
                } catch (e) {
                    console.error(e);
                    this.showToast('Error', 'Tidak bisa menambah item.', 'error', 'error');
                } finally {
                    this.busy = false;
                }
            },
            goToCart() {
                window.location.href = "{{ route('cart') }}";
            },
            showToast(title, message, icon = 'check_circle', type = 'success') {
                this.toast = { show: true, title, message, icon, type };
                setTimeout(() => this.toast.show = false, 2000);
            }
        };
    }
</script>
@endpush

@push('scripts')
<!-- no extra scripts -->
@endpush

