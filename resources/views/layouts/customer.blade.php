<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Welcome') | {{ $systemSettings['cafe_name'] ?? config('app.name') }}</title>
    @include('layouts.partials.favicon')
    
    <!-- Preconnect (harus di atas stylesheet agar lebih cepat) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <!-- Fonts: gabung 2 request jadi 1, tambah display=swap -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <script>
        // Lightweight Cart shim (matches signature used by main app Cart)
        (function () {
            const key = 'cart';
            const tableKey = 'table_number';
            const uid = () => Date.now() + Math.random();

            function loadCart() {
                try {
                    const raw = JSON.parse(localStorage.getItem(key) || '[]');
                    return Array.isArray(raw) ? raw : [];
                } catch (e) {
                    return [];
                }
            }

            function saveCart(cart) {
                localStorage.setItem(key, JSON.stringify(cart));
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: cart }));
            }

            function normalize(item) {
                const qty = Math.max(1, parseInt(item.quantity ?? 1, 10) || 1);
                const base = Number(item.base_price ?? item.price ?? item.total_price ?? item.final_price ?? 0) || 0;
                const totalPerItem = Number(item.final_price_per_item ?? item.final_price ?? item.total_price ?? base) || 0;
                return {
                    ...item,
                    id: item.id,
                    product_id: item.id,
                    name: item.name,
                    product_name: item.name,
                    quantity: qty,
                    base_price: base,
                    price: base,
                    total_price: totalPerItem,
                    final_price: totalPerItem,
                    final_price_per_item: totalPerItem,
                    final_price_total: totalPerItem * qty,
                    subtotal: totalPerItem * qty,
                    cartItemId: item.cartItemId || uid(),
                };
            }

            if (!window.Cart) window.Cart = {};

            window.Cart.add = function (id, name, basePrice, image = null, quantity = 1, options = {}) {
                const cart = loadCart();
                const normalized = normalize({
                    id,
                    name,
                    image,
                    quantity,
                    type: options.type || 'beverage',
                    options,
                    base_price: basePrice,
                });
                cart.push(normalized);
                saveCart(cart);
                return normalized;
            };

            window.Cart.remove = function (identifier) {
                const cart = loadCart().filter(item => item.cartItemId !== identifier && item.id !== identifier);
                saveCart(cart);
            };

            window.Cart.clear = function () {
                saveCart([]);
            };

            window.Cart.items = loadCart();
            window.Cart.getItems = () => loadCart();
            window.Cart.getCount = () => loadCart().reduce((s, i) => s + (parseInt(i.quantity || 1, 10) || 1), 0);
            window.Cart.getTotal = () => loadCart().reduce((s, i) => s + (Number(i.final_price_total ?? i.subtotal ?? 0) || 0), 0);
            window.Cart.save = saveCart;
            window.Cart.setTable = function (number) { localStorage.setItem(tableKey, number); };
            window.Cart.tableNumber = () => localStorage.getItem(tableKey);
            window.Cart.formatPrice = v => 'Rp ' + (Number(v) || 0).toLocaleString('id-ID');
            window.Cart.updateBadge = function () {}; // no-op here
        })();
    </script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        /* Sembunyikan teks fallback Material Symbols saat font belum load */
        .material-symbols-outlined {
            visibility: hidden !important;
            font-size: 0 !important;
            line-height: 0 !important;
            width: 0 !important;
            height: 0 !important;
            overflow: hidden !important;
            display: inline-block !important;
        }
        .fonts-loaded .material-symbols-outlined {
            visibility: visible !important;
            font-size: inherit !important;
            line-height: normal !important;
            width: auto !important;
            height: auto !important;
            overflow: visible !important;
            display: inline !important;
        }
    </style>
    <script>
        var _fontFallback = setTimeout(function() {
            document.documentElement.classList.add('fonts-loaded');
        }, 3000);
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(function() {
                clearTimeout(_fontFallback);
                document.documentElement.classList.add('fonts-loaded');
            });
        }
    </script>
</head>
<body class="bg-[#faf8f6] text-[#181411] min-h-screen relative overflow-x-hidden selection:bg-orange-100 selection:text-orange-900">

    <main class="w-full max-w-md mx-auto min-h-screen bg-[#faf8f6] shadow-2xl relative">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
