<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Brew & Bean') - Cafe Web Ordering</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#d47311",
                        "primary-dark": "#b05d0d",
                        "background-light": "#FDFBF7",
                        "background-dark": "#221910",
                        "text-main": "#3E2723",
                        "text-subtle": "#897561",
                        "surface-light": "#FFFFFF",
                        "surface-dark": "#2D2115",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
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
    @stack('styles')
    
    <style>
        /* Prevent horizontal scroll globally */
        html, body {
            overflow-x: hidden;
            max-width: 100vw;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-text-main dark:text-white font-display antialiased" x-data="{ mobileMenuOpen: false }">
    
    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-50 bg-surface-light/95 dark:bg-surface-dark/95 backdrop-blur-sm border-b border-[#f4f2f0] dark:border-[#3e2d23] px-6 py-3 transition-colors">
        <div class="flex items-center justify-between max-w-7xl mx-auto w-full">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-4 text-text-main dark:text-white cursor-pointer hover:opacity-80 transition">
                <div class="size-8 text-primary">
                    <svg class="w-full h-full" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path d="M42.1739 20.1739L27.8261 5.82609C29.1366 7.13663 28.3989 10.1876 26.2002 13.7654C24.8538 15.9564 22.9595 18.3449 20.6522 20.6522C18.3449 22.9595 15.9564 24.8538 13.7654 26.2002C10.1876 28.3989 7.13663 29.1366 5.82609 27.8261L20.1739 42.1739C21.4845 43.4845 24.5355 42.7467 28.1133 40.548C30.3042 39.2016 32.6927 37.3073 35 35C37.3073 32.6927 39.2016 30.3042 40.548 28.1133C42.7467 24.5355 43.4845 21.4845 42.1739 20.1739Z" fill="currentColor"></path>
                        <path clip-rule="evenodd" d="M7.24189 26.4066C7.31369 26.4411 7.64204 26.5637 8.52504 26.3738C9.59462 26.1438 11.0343 25.5311 12.7183 24.4963C14.7583 23.2426 17.0256 21.4503 19.238 19.238C21.4503 17.0256 23.2426 14.7583 24.4963 12.7183C25.5311 11.0343 26.1438 9.59463 26.3738 8.52504C26.5637 7.64204 26.4411 7.31369 26.4066 7.24189C26.345 7.21246 26.143 7.14535 25.6664 7.1918C24.9745 7.25925 23.9954 7.5498 22.7699 8.14278C20.3369 9.32007 17.3369 11.4915 14.4142 14.4142C11.4915 17.3369 9.32007 20.3369 8.14278 22.7699C7.5498 23.9954 7.25925 24.9745 7.1918 25.6664C7.14534 26.143 7.21246 26.345 7.24189 26.4066ZM29.9001 10.7285C29.4519 12.0322 28.7617 13.4172 27.9042 14.8126C26.465 17.1544 24.4686 19.6641 22.0664 22.0664C19.6641 24.4686 17.1544 26.465 14.8126 27.9042C13.4172 28.7617 12.0322 29.4519 10.7285 29.9001L21.5754 40.747C21.6001 40.7606 21.8995 40.931 22.8729 40.7217C23.9424 40.4916 25.3821 39.879 27.0661 38.8441C29.1062 37.5904 31.3734 35.7982 33.5858 33.5858C35.7982 31.3734 37.5904 29.1062 38.8441 27.0661C39.879 25.3821 40.4916 23.9425 40.7216 22.8729C40.931 21.8995 40.7606 21.6001 40.747 21.5754L29.9001 10.7285ZM29.2403 4.41187L43.5881 18.7597C44.9757 20.1473 44.9743 22.1235 44.6322 23.7139C44.2714 25.3919 43.4158 27.2666 42.252 29.1604C40.8128 31.5022 38.8165 34.012 36.4142 36.4142C34.012 38.8165 31.5022 40.8128 29.1604 42.252C27.2666 43.4158 25.3919 44.2714 23.7139 44.6322C22.1235 44.9743 20.1473 44.9757 18.7597 43.5881L4.41187 29.2403C3.29027 28.1187 3.08209 26.5973 3.21067 25.2783C3.34099 23.9415 3.8369 22.4852 4.54214 21.0277C5.96129 18.0948 8.43335 14.7382 11.5858 11.5858C14.7382 8.43335 18.0948 5.9613 21.0277 4.54214C22.4852 3.8369 23.9415 3.34099 25.2783 3.21067C26.5973 3.08209 28.1187 3.29028 29.2403 4.41187Z" fill="currentColor" fill-rule="evenodd"></path>
                    </svg>
                </div>
                <h2 class="text-lg font-bold leading-tight tracking-[-0.015em]">Cafe Aroma</h2>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center gap-9">
                <a class="text-sm font-medium hover:text-primary transition-colors hover:font-bold {{ request()->routeIs('home') ? 'text-primary' : 'text-text-main dark:text-gray-200' }}" href="{{ route('home') }}">Home</a>
                <a class="text-sm font-bold text-primary" href="{{ route('menu.index') }}">Menu</a>
                <a class="text-sm font-medium hover:text-primary transition-colors" href="{{ route('track') }}">Order Status</a>
                @auth
                <a class="text-sm font-medium hover:text-primary transition-colors font-bold" href="@if(auth()->user()->role === 'cashier'){{ route('cashier.dashboard') }}@elseif(auth()->user()->role === 'manager'){{ route('manager.dashboard') }}@else{{ route('admin.dashboard') }}@endif">Dashboard</a>
                @else
                <!-- <a class="text-sm font-medium hover:text-primary transition-colors" href="{{ route('login') }}">Staff</a> -->
                @endauth
            </nav>

            <!-- Actions -->
            <div class="flex gap-3">
                <a href="{{ route('cart') }}" class="relative flex size-10 items-center justify-center rounded-lg bg-background-light dark:bg-background-dark/50 hover:bg-primary/10 transition-colors text-text-main dark:text-white group">
                    <span class="material-symbols-outlined text-[20px] group-hover:text-primary transition-colors">shopping_cart</span>
                    <span id="cart-badge" class="absolute -top-1 -right-1 bg-primary text-white text-[10px] font-bold h-4 min-w-[16px] px-1 rounded-full flex items-center justify-center hidden border-2 border-white dark:border-[#221910]">0</span>
                </a>
                
                @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex size-10 items-center justify-center rounded-lg bg-background-light dark:bg-background-dark/50 hover:bg-red-50 hover:text-red-500 transition-colors text-text-main dark:text-white" title="Logout">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                    </button>
                </form>
                @else
                <a href="{{ route('login') }}" class="flex size-10 items-center justify-center rounded-lg bg-background-light dark:bg-background-dark/50 hover:bg-primary/10 transition-colors text-text-main dark:text-white">
                    <span class="material-symbols-outlined text-[20px]">person</span>
                </a>
                @endauth

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden flex size-10 items-center justify-center rounded-lg bg-background-light dark:bg-background-dark/50 hover:bg-primary/10 transition-colors text-text-main dark:text-white">
                    <span class="material-symbols-outlined text-[20px]">menu</span>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu Overlay -->
        <div x-show="mobileMenuOpen" x-transition class="md:hidden border-t border-[#f4f2f0] dark:border-[#3e2d23] py-4 bg-surface-light dark:bg-surface-dark absolute left-0 right-0 top-full shadow-lg">
            <nav class="flex flex-col gap-2 px-6">
                <a href="{{ route('home') }}" class="py-2 text-sm font-medium text-text-main dark:text-gray-200">Home</a>
                <a href="{{ route('menu.index') }}" class="py-2 text-sm font-bold text-primary">Menu</a>
                <a href="{{ route('track') }}" class="py-2 text-sm font-medium text-text-main dark:text-gray-200">Order Status</a>
            </nav>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-surface-light dark:bg-surface-dark border-t border-gray-200 dark:border-[#3e2d23] py-10 px-6 mt-auto">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-2">
                <div class="size-6 text-primary">
                    <svg class="w-full h-full" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path d="M42.1739 20.1739L27.8261 5.82609C29.1366 7.13663 28.3989 10.1876 26.2002 13.7654C24.8538 15.9564 22.9595 18.3449 20.6522 20.6522C18.3449 22.9595 15.9564 24.8538 13.7654 26.2002C10.1876 28.3989 7.13663 29.1366 5.82609 27.8261L20.1739 42.1739C21.4845 43.4845 24.5355 42.7467 28.1133 40.548C30.3042 39.2016 32.6927 37.3073 35 35C37.3073 32.6927 39.2016 30.3042 40.548 28.1133C42.7467 24.5355 43.4845 21.4845 42.1739 20.1739Z" fill="currentColor"></path>
                    </svg>
                </div>
                <span class="font-bold text-text-main dark:text-white">Cafe Aroma</span>
            </div>
            <div class="flex gap-8 flex-wrap justify-center">
                <a class="text-sm text-text-muted hover:text-primary transition-colors" href="#">Privacy Policy</a>
                <a class="text-sm text-text-muted hover:text-primary transition-colors" href="#">Terms of Service</a>
                <a class="text-sm text-text-muted hover:text-primary transition-colors" href="#">Contact</a>
            </div>
            <div class="flex gap-4">
                <a class="text-text-muted hover:text-primary transition-colors" href="#"><span class="material-symbols-outlined">public</span></a>
                <a class="text-text-muted hover:text-primary transition-colors" href="#"><span class="material-symbols-outlined">alternate_email</span></a>
            </div>
        </div>
    </footer>
    
    @stack('scripts')
    
    <script>
        // Global Cart Functionality
        window.Cart = {
            get items() {
                return JSON.parse(localStorage.getItem('cart') || '[]');
            },
            
            add(id, name, price, image = null) {
                let cart = this.items;
                const existingItem = cart.find(item => item.id === id);
                
                if (existingItem) {
                    existingItem.quantity++;
                } else {
                    cart.push({ id, name, price, image, quantity: 1 });
                }
                
                this.save(cart);
                this.showToast('Item ditambahkan ke keranjang');
            },
            
            remove(id) {
                let cart = this.items.filter(item => item.id !== id);
                this.save(cart);
            },
            
            clear() {
                this.save([]);
            },
            
            updateQuantity(id, quantity) {
                let cart = this.items;
                const item = cart.find(item => item.id === id);
                
                if (item) {
                    item.quantity = parseInt(quantity);
                    if (item.quantity <= 0) {
                        this.remove(id);
                        return;
                    }
                }
                
                this.save(cart);
            },
            
            save(cart) {
                localStorage.setItem('cart', JSON.stringify(cart));
                this.updateBadge();
                window.dispatchEvent(new CustomEvent('cart-updated'));
            },
            
            updateBadge() {
                const cart = this.items;
                const badge = document.getElementById('cart-badge');
                if (!badge) return;
                
                const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
                
                if (totalItems > 0) {
                    badge.classList.remove('hidden');
                    badge.innerText = totalItems > 99 ? '99+' : totalItems; // Optional: Add count
                } else {
                    badge.classList.add('hidden');
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
            Cart.updateBadge();
        });
    </script>
</body>
</html>
