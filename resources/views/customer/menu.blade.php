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
        <button
            type="button"
            @click="goToCart()"
            class="w-full bg-[#c67c4e] text-white rounded-2xl shadow-xl flex items-center justify-between px-4 py-3 active:scale-[0.99] transition"
        >
            <div class="flex items-center gap-3">
                <div class="bg-[#2f2d2c] min-w-[28px] h-[28px] rounded-md text-xs font-bold flex items-center justify-center">
                    <span x-text="cartCount"></span>
                </div>
                <span class="text-sm font-semibold">View Cart</span>
            </div>
            <span class="text-sm font-semibold" x-text="formatRupiah(cartTotal)"></span>
        </button>
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
            showDetail: false,
            selectedProduct: {
                id: null,
                name: '',
                priceRaw: 0,
                price: '',
                description: '',
                image: '',
                isFeatured: false,
                type: 'food'
            },
            // Option states (DB-driven)
            optionGroups: [],
            selections: {},
            loadingOptions: false,
            specialRequest: '',
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

                window.addEventListener('cart-updated', () => this.refreshCart());
                window.addEventListener('cart-cleared', () => this.refreshCart());
                window.addEventListener('storage', (e) => {
                    if (e.key === 'cart') this.refreshCart();
                });
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
            formatPrice(value) {
                return this.formatRupiah(value);
            },
            resolveType(slug = '') {
                const s = (slug || '').toLowerCase();
                if (s.includes('coffee') || s.includes('kopi') || s.includes('drink')) return 'beverage';
                if (s.includes('snack')) return 'snack';
                if (s.includes('dessert')) return 'dessert';
                return 'food';
            },
            loadCartPreferred() {
                // 1) try localStorage (most reliable across contexts)
                try {
                    const raw = localStorage.getItem('cart');
                    if (raw) {
                        const parsed = JSON.parse(raw);
                        if (Array.isArray(parsed)) return parsed;
                    }
                } catch (e) { /* ignore */ }

                // 2) fallback to Cart.getItems
                if (window.Cart?.getItems) {
                    try {
                        const items = window.Cart.getItems();
                        if (Array.isArray(items)) return items;
                    } catch (e) { /* ignore */ }
                }
                // 3) fallback to Cart.items
                if (window.Cart?.items && Array.isArray(window.Cart.items)) {
                    return window.Cart.items;
                }
                return [];
            },
            // Backward-compat alias used by addToCartWithOptions
            loadCart() {
                return this.loadCartPreferred();
            },
            saveCart(cart) {
                if (window.Cart?.save) {
                    window.Cart.save(cart);
                    return;
                }
                localStorage.setItem('cart', JSON.stringify(cart));
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: cart }));
            },
            syncCartTotals(cart) {
                const safeCart = cart || [];
                const lsCount = safeCart.reduce((sum, item) => sum + (parseInt(item.quantity ?? 1, 10) || 1), 0);
                const lsTotal = safeCart.reduce((sum, item) => {
                    const qty = parseInt(item.quantity ?? 1, 10) || 1;
                    const price = Number(
                        item.total_price ?? item.totalPrice ?? item.final_price ?? item.finalPrice ?? item.price ?? item.base_price ?? 0
                    ) || 0;
                    return sum + price * qty;
                }, 0);

                const cartCount = window.Cart?.getCount ? window.Cart.getCount() : 0;
                const cartTotal = window.Cart?.getTotal ? window.Cart.getTotal() : 0;

                this.cartCount = Math.max(lsCount, cartCount);
                this.cartTotal = Math.max(lsTotal, cartTotal);
            },
            refreshCart() {
                const cart = this.loadCartPreferred();
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
            async showProductDetail(payload) {
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
                await this.loadOptionsForProduct(payload.id);
            },
            resetOptions() {
                this.optionGroups = [];
                this.selections = {};
                this.specialRequest = '';
            },
            async loadOptionsForProduct(menuId) {
                this.loadingOptions = true;
                try {
                    const res = await fetch(`/menu/${menuId}/options`);
                    const json = await res.json();
                    this.optionGroups = json?.data?.option_groups ?? [];
                    this.selections = {};
                } catch (e) {
                    console.error(e);
                    this.optionGroups = [];
                } finally {
                    this.loadingOptions = false;
                }
            },
            toggleOption(group, value) {
                if (group.type === 'single') {
                    this.$set ? this.$set(this.selections, group.id, value.id) : (this.selections[group.id] = value.id);
                } else {
                    const current = this.selections[group.id] || [];
                    if (current.includes(value.id)) {
                        this.selections[group.id] = current.filter(v => v !== value.id);
                    } else {
                        this.selections[group.id] = [...current, value.id];
                    }
                }
            },
            previewSelection(group) {
                const optionNames = (group.values || []).map(v => v.name).join(', ');
                this.showToast('Preview', `${group.name}: ${optionNames || 'No values'}`, 'visibility', 'info');
            },
            isSelected(group, valueId) {
                const sel = this.selections[group.id];
                return group.type === 'single' ? sel === valueId : Array.isArray(sel) && sel.includes(valueId);
            },
            hasSelection(group) {
                const sel = this.selections[group.id];
                if (group.type === 'single') return !!sel;
                return Array.isArray(sel) && sel.length > 0;
            },
            buildOptions() {
                const groups = this.optionGroups.map(group => {
                    const selected = this.selections[group.id];
                    const values = (group.values || []).filter(v => group.type === 'single'
                        ? v.id === selected
                        : Array.isArray(selected) && selected.includes(v.id)
                    ).map(v => ({ id: v.id, name: v.name, price_adjustment: v.price_adjustment }));
                    return {
                        id: group.id,
                        name: group.name,
                        type: group.type,
                        is_required: group.is_required,
                        selected_values: values,
                    };
                });
                const payload = { option_groups: groups };
                if (this.specialRequest.trim()) payload.special_request = this.specialRequest.trim();
                return payload;
            },
            calculatePriceWithOptions(base) {
                let total = Number(base) || 0;
                this.optionGroups.forEach(group => {
                    const selected = this.selections[group.id];
                    const values = group.values || [];
                    if (group.type === 'single' && selected) {
                        const val = values.find(v => v.id === selected);
                        if (val) total += Number(val.price_adjustment || 0);
                    }
                    if (group.type === 'multiple' && Array.isArray(selected)) {
                        selected.forEach(id => {
                            const val = values.find(v => v.id === id);
                            if (val) total += Number(val.price_adjustment || 0);
                        });
                    }
                });
                return total;
            },
            canAddToCart() {
                if (!this.showDetail || this.loadingOptions) return false;
                if (!this.optionGroups.length) return true;
                return this.optionGroups.every(g => !g.is_required || this.hasSelection(g));
            },
            calculateItemPrice() {
                return this.calculatePriceWithOptions(this.selectedProduct.priceRaw);
            },
            async addToCartWithOptions() {
                if (!this.canAddToCart()) return;
                this.busy = true;
                try {
                    const options = this.buildOptions();
                    options.type = this.selectedProduct.type;
                    const payload = {
                        menu_id: this.selectedProduct.id,
                        quantity: 1,
                        options,
                        order_type: 'qr',
                        table_number: this.tableNumber
                    };

                    const res = await fetch('/cart/add', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify(payload)
                    });
                    const data = await res.json();
                    if (!res.ok || !data.success) {
                        throw new Error(data.message || 'Gagal menambahkan ke keranjang');
                    }
                    this.cartCount = data.cart_count ?? this.cartCount;
                    const totalNumber = data.cart_total ?? 0;
                    this.cartTotal = totalNumber;
                    const badge = document.getElementById('cart-badge');
                    if (badge && this.cartCount > 0) {
                        badge.classList.remove('hidden');
                        badge.innerText = this.cartCount > 99 ? '99+' : this.cartCount;
                    }
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

