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
