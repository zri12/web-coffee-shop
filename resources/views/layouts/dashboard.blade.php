<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ $systemSettings['cafe_name'] ?? config('app.name') }}</title>
    @include('layouts.partials.favicon')

    <!-- Anti-FOUC guard: hide until critical assets are ready -->
    <style id="fouc-guard">html.fouc-prep, html.fouc-prep body { visibility: hidden; }</style>

    <!-- Preload & preconnect for icon/fonts to avoid text fallbacks -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" as="style">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" as="style">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
       tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#d47311",
                        "background-light": "#f8f7f6",
                        "background-dark": "#221910",
                        "card-light": "#ffffff",
                        "card-dark": "#2c241b",
                        "text-main-light": "#181411",
                        "text-main-dark": "#f4f2f0",
                        "text-sec-light": "#897561",
                        "text-sec-dark": "#b0a090",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                },
            },
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        body.loading { visibility: hidden; opacity: 0; }
        body:not(.loading) { opacity: 1; transition: opacity 180ms ease; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d47311; border-radius: 3px; opacity: 0.5; }
    </style>

    <script>
        // Prepare to hide until CSS & JS are ready
        document.documentElement.classList.add('fouc-prep');
        document.addEventListener('DOMContentLoaded', () => {
            document.body.classList.add('loading');
        });
        window.addEventListener('load', () => {
            document.documentElement.classList.remove('fouc-prep');
            document.body.classList.remove('loading');
        });
    </script>
    
    @stack('styles')
</head>
<body class="loading bg-background-light dark:bg-background-dark text-text-main-light dark:text-text-main-dark overflow-hidden" x-data="{ sidebarOpen: false }">
<div class="flex h-screen w-full relative">
    <!-- Backdrop for Mobile -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

    <!-- Side Navigation -->
    <!-- Side Navigation -->
    @include('layouts.partials.sidebar')
    
    <!-- Main Content Area -->
    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col h-full overflow-hidden bg-[#fcfbf9] dark:bg-background-dark relative w-full">
        <!-- Header -->
        <header class="h-16 flex items-center justify-between px-4 lg:px-8 bg-white dark:bg-[#1a1612] border-b border-[#e6e0db] dark:border-[#3d362e] shrink-0 z-10 transition-colors">
            <div class="flex items-center gap-4">
                <!-- Mobile Toggle -->
                <button @click="sidebarOpen = true" class="lg:hidden text-text-main-light dark:text-white hover:bg-gray-100 dark:hover:bg-primary/10 p-2 rounded-lg transition-colors">
                    <span class="material-symbols-outlined">menu</span>
                </button>
            </div>

            <div class="flex items-center gap-4">
                <!-- Actions -->
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3 cursor-pointer">
                        <div class="bg-center bg-no-repeat bg-cover rounded-full size-9 border-2 border-[#e6e0db] dark:border-[#3d362e] bg-primary/10 flex items-center justify-center text-primary font-bold shadow-sm" style="background-image: url('https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=d47311&color=fff');">
                        </div>
                        <div class="hidden lg:flex flex-col">
                            <span class="text-sm font-medium text-[#181411] dark:text-white leading-tight">{{ auth()->user()->name }}</span>
                            <span class="text-xs text-[#897561]">{{ ucfirst(auth()->user()->role) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Scrollable Dashboard Content -->
        <div class="flex-1 overflow-y-auto bg-[#fcfbf9] dark:bg-background-dark">
            @yield('content')
        </div>
    </main>
</div>

<script>
    window.menuAddonsForm = function(initial = []) {
        const list = Array.isArray(initial) ? initial : [];
        return {
            addons: JSON.parse(JSON.stringify(list)),
            addRow() {
                this.addons.push({ name: '', price: '' });
            },
            removeRow(index) {
                this.addons.splice(index, 1);
            },
        };
    };
</script>
@stack('scripts')
</body>
</html>
