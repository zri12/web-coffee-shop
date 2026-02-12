<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Welcome') | {{ $systemSettings['cafe_name'] ?? config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <!-- Tailwind CSS CDN (serverless-safe fallback for Vercel) -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#d47311",
                        "primary-dark": "#b05d0d",
                        "background-light": "#FDFBF7",
                        "background-dark": "#221910",
                        "text-main": "#3E2723",
                        "text-subtle": "#897561",
                        "surface-light": "#FFFFFF",
                        "surface-dark": "#2D2115",
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                        lg: "0.75rem",
                        xl: "1rem",
                        full: "9999px"
                    },
                },
            },
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        // Lightweight cart helper for QR customer flow (localStorage only)
        (function () {
            const key = 'cart';
            const tableKey = 'table_number';
            const uid = () => 'cart_' + Math.random().toString(36).slice(2);

            const state = {
                items: [],
                tableNumber: null,
            };

            function load() {
                try {
                    state.items = JSON.parse(localStorage.getItem(key) || '[]');
                } catch (e) {
                    state.items = [];
                }
                state.items = state.items.map(normalize);
                state.tableNumber = localStorage.getItem(tableKey) || null;
            }

            function persist() {
                localStorage.setItem(key, JSON.stringify(state.items));
                if (state.tableNumber) {
                    localStorage.setItem(tableKey, state.tableNumber);
                }
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: state.items }));
            }

            function normalize(item) {
                if (!item.cartItemId) item.cartItemId = uid();
                if (!item.quantity || item.quantity < 1) item.quantity = 1;
                if (item.price === undefined) item.price = item.final_price ?? item.finalPrice ?? 0;
                if (item.final_price === undefined) item.final_price = item.price;
                if (item.finalPrice === undefined) item.finalPrice = item.final_price;
                return item;
            }

            load();

            window.Cart = {
                setTable(number) {
                    state.tableNumber = number;
                    persist();
                },
                save: persist,
                add(item) {
                    const normalized = normalize({ ...item });
                    const idx = state.items.findIndex(i => (i.id ?? i.menu_id) === normalized.id);
                    if (idx >= 0) {
                        state.items[idx].quantity = (parseInt(state.items[idx].quantity || 1, 10) || 1) + 1;
                    } else {
                        state.items.push(normalized);
                    }
                    persist();
                },
                remove(cartItemId) {
                    state.items = state.items.filter(i => (i.cartItemId ?? '') !== cartItemId);
                    persist();
                },
                clear() {
                    state.items = [];
                    persist();
                },
                items: state.items,
                getItems() {
                    return state.items;
                },
                getCount() {
                    return state.items.reduce((s, i) => s + (parseInt(i.quantity || 1, 10) || 1), 0);
                },
                getTotal() {
                    return state.items.reduce((s, i) => {
                        const qty = parseInt(i.quantity || 1, 10) || 1;
                        const price = Number(i.final_price ?? i.finalPrice ?? i.price ?? 0) || 0;
                        return s + price * qty;
                    }, 0);
                },
                formatPrice(v) {
                    const n = Number(v) || 0;
                    return 'Rp ' + n.toLocaleString('id-ID');
                },
                recalculateItem(item) {
                    return item;
                },
                updateBadge() {}, // no-op in mobile layout
                tableNumber: () => state.tableNumber,
            };
        })();
    </script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#faf8f6] text-[#181411] min-h-screen relative overflow-x-hidden selection:bg-orange-100 selection:text-orange-900">

    <main class="w-full max-w-md mx-auto min-h-screen bg-[#faf8f6] shadow-2xl relative">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
