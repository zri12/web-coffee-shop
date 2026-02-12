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
    <section class="flex-1 space-y-6 px-5 pb-8" x-init="initClickFallbacks()">
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
                                            data-add-menu="1"
                                            data-payload='@js($menuPayload)'
                                            @click.prevent.stop='addToCart(@js($menuPayload))'
                                            onclick='return window.addMenuItem && addMenuItem(@js($menuPayload), {{ (int) $table->table_number }});'
                                            class="px-4 py-2 rounded-full bg-[#c67c4e] text-white text-sm font-semibold hover:bg-[#b06b3e] active:scale-95 transition disabled:opacity-60 disabled:cursor-not-allowed"
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
                    @click="refreshCart()"
                    class="text-white/70 hover:text-white transition"
                >
                    <span class="material-symbols-outlined">refresh</span>
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
            initClickFallbacks() {
                const tableNum = this.tableNumber;
                document.querySelectorAll('[data-add-menu]').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        try {
                            e.preventDefault();
                            e.stopPropagation();
                            let payload = btn.__payload;
                            if (!payload) {
                                const raw = btn.getAttribute('data-payload');
                                payload = raw ? JSON.parse(raw) : null;
                                btn.__payload = payload;
                            }
                            addMenuItem(payload, tableNum);
                            this.refreshCart();
                        } catch (err) {
                            console.error('addMenuItem fallback error', err);
                        }
                    }, { passive: false });
                });
            },
            addToCart(menu) {
                if (this.busy) return;
                this.busy = true;
                try {
                    window.Cart?.setTable?.(this.tableNumber);
                    addMenuItem(menu, this.tableNumber);
                    this.refreshCart();
                    this.showToast('Ditambahkan', `${menu.name} masuk keranjang.`, 'check_circle', 'success');
                    setTimeout(() => this.goToCart(), 150);
                } catch (error) {
                    this.showToast('Error', 'Terjadi kendala. Coba lagi.', 'error', 'error');
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
<script>
    // Global fallback so click still works if Alpine fails
    window.addMenuItem = function(menu, tableNumber) {
        try {
            // Normalize payload
            if (typeof menu === 'string') {
                menu = JSON.parse(menu);
            }
            if (!menu || typeof menu !== 'object') throw new Error('Invalid payload');

            if (tableNumber) {
                const prev = localStorage.getItem('table_number');
                if (prev && prev !== String(tableNumber)) {
                    localStorage.removeItem('cart');
                }
                localStorage.setItem('table_number', tableNumber);
            }
            const cat = (menu.category || '').toLowerCase();
            const type = cat.includes('coffee') || cat.includes('kopi') ? 'beverage'
                       : cat.includes('snack') ? 'snack'
                       : cat.includes('dessert') ? 'dessert'
                       : 'food';

            const add = window.Cart && typeof window.Cart.add === 'function'
                ? window.Cart.add.bind(window.Cart)
                : null;

            if (add) {
                window.Cart.setTable?.(tableNumber);
                add(menu.id, menu.name, menu.price, menu.image, 1, { type });
            } else {
                const cart = JSON.parse(localStorage.getItem('cart') || '[]');
                cart.push({
                    id: menu.id,
                    name: menu.name,
                    price: menu.price,
                    base_price: menu.price,
                    final_price: menu.price,
                    final_price_per_item: menu.price,
                    final_price_total: menu.price,
                    quantity: 1,
                    image: menu.image,
                    type
                });
                localStorage.setItem('cart', JSON.stringify(cart));
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: cart }));
            }
            // navigate to cart page
            window.location.href = "{{ route('cart') }}";
            return false;
        } catch (e) {
            console.error(e);
            alert('Tidak bisa menambah item, coba lagi.');
            return false;
        }
    }
</script>
@endpush

