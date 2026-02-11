<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed lg:static inset-y-0 left-0 z-50 w-64 flex flex-col h-full bg-white dark:bg-[#1a1612] border-r border-[#e6e0db] dark:border-[#3d362e] transition-transform duration-300 lg:translate-x-0 shrink-0">
    
    <!-- Logo Section -->
    <div class="p-6 flex items-center gap-3">
        <div class="bg-center bg-no-repeat bg-cover rounded-full size-10 shadow-sm bg-primary/10 flex items-center justify-center text-primary font-bold">
            {{ substr(auth()->user()->name, 0, 1) }}
        </div>
        <div class="flex flex-col">
                <h1 class="text-[#181411] dark:text-white text-base font-bold leading-normal">{{ $systemSettings['cafe_name'] ?? config('app.name') }}</h1>
            <p class="text-[#897561] text-xs font-normal leading-normal">
                @if(auth()->user()->isAdmin())
                    Admin Portal
                @elseif(auth()->user()->isManager())
                    Manager Portal
                @else
                    Cashier Portal
                @endif
            </p>
        </div>
        <!-- Close Button Mobile -->
        <button @click="sidebarOpen = false" class="lg:hidden ml-auto text-text-sec-light hover:text-text-main-light">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    <!-- Navigation -->
    @if(auth()->user()->isAdmin())
        @include('layouts.partials.sidebars.admin')
    @elseif(auth()->user()->isManager())
        @include('layouts.partials.sidebars.manager')
    @else
        @include('layouts.partials.sidebars.cashier')
    @endif


        <!-- Bottom Section -->
        <div class="mt-auto pt-4 px-4 pb-4 border-t border-[#e6e0db] dark:border-[#3d362e]">
            <a class="flex items-center gap-3 px-3 py-3 rounded-lg text-[#5c4d40] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#2c241b] transition-colors group" href="{{ route('home') }}" target="_blank">
                <span class="material-symbols-outlined group-hover:text-[#181411] dark:group-hover:text-white">public</span>
                <p class="text-sm font-medium leading-normal group-hover:text-[#181411] dark:group-hover:text-white">View Site</p>
            </a>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-3 rounded-lg text-[#5c4d40] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#2c241b] transition-colors group text-left">
                    <span class="material-symbols-outlined group-hover:text-[#181411] dark:group-hover:text-white">logout</span>
                    <p class="text-sm font-medium leading-normal group-hover:text-[#181411] dark:group-hover:text-white">Logout</p>
                </button>
            </form>
        </div>

</aside>
