<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS') - Bean & Brew</title>
    
    <!-- Tailwind CSS -->
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
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1cbc5; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #b0a8a0; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-text-main-light dark:text-text-main-dark overflow-hidden" x-data="{ sidebarOpen: false }">
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

        <!-- Side Navigation (Identical to Dashboard) -->
        <!-- Side Navigation (Identical to Dashboard) -->
        @include('layouts.partials.sidebar')

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col h-full overflow-hidden bg-background-light dark:bg-background-dark relative w-full">
            <!-- Mobile Header Toggle (Visible only on mobile) -->
            <div class="lg:hidden p-4 bg-white dark:bg-card-dark border-b border-[#e6e0db] dark:border-[#3e342b] flex items-center gap-4 shrink-0">
                <button @click="sidebarOpen = true" class="text-text-main-light dark:text-white">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <h1 class="font-bold text-lg dark:text-white">@yield('title', 'POS')</h1>
            </div>
            
            @yield('content')
        </main>
    </div>
</body>
</html>
