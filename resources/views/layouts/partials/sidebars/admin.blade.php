<nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
    <!-- Dashboard -->
    <a href="{{ route('admin.dashboard') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white' : 'text-[#897561] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23]' }}">
        <span class="material-symbols-outlined text-[24px]">dashboard</span>
        <span class="font-medium">Dashboard</span>
    </a>

    <!-- Menu Management -->
    <a href="{{ route('admin.menus') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.menus*') ? 'bg-primary text-white' : 'text-[#897561] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23]' }}">
        <span class="material-symbols-outlined text-[24px]">restaurant_menu</span>
        <span class="font-medium">Menu Management</span>
    </a>

    <!-- Category Management -->
    <a href="{{ route('admin.categories') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.categories*') ? 'bg-primary text-white' : 'text-[#897561] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23]' }}">
        <span class="material-symbols-outlined text-[24px]">category</span>
        <span class="font-medium">Category Management</span>
    </a>

    <!-- Orders -->
    <a href="{{ route('admin.orders') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.orders*') ? 'bg-primary text-white' : 'text-[#897561] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23]' }}">
        <span class="material-symbols-outlined text-[24px]">receipt_long</span>
        <span class="font-medium">Orders</span>
    </a>

    <!-- Table Management -->
    <a href="{{ route('admin.tables') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.tables*') ? 'bg-primary text-white' : 'text-[#897561] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23]' }}">
        <span class="material-symbols-outlined text-[24px]">table_restaurant</span>
        <span class="font-medium">Table Management</span>
    </a>

    <!-- User Management -->
    <a href="{{ route('admin.users') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.users*') ? 'bg-primary text-white' : 'text-[#897561] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-#3e2d23]' }}">
        <span class="material-symbols-outlined text-[24px]">group</span>
        <span class="font-medium">User Management</span>
    </a>

    <!-- Divider -->
    <div class="my-2 border-t border-[#f4f2f0] dark:border-[#3e2d23]"></div>

    <!-- Storage & Inventory -->
    <a href="{{ route('admin.ingredients.index') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.ingredients*') ? 'bg-primary text-white' : 'text-[#897561] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23]' }}">
        <span class="material-symbols-outlined text-[24px]">inventory_2</span>
        <span class="font-medium">Storage & Inventory</span>
    </a>

    <!-- Analytics -->
    <a href="{{ route('admin.analytics.inventory') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.analytics*') ? 'bg-primary text-white' : 'text-[#897561] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23]' }}">
        <span class="material-symbols-outlined text-[24px]">analytics</span>
        <span class="font-medium">Inventory Analytics</span>
    </a>

    <!-- Divider -->
    <div class="my-2 border-t border-[#f4f2f0] dark:border-[#3e2d23]"></div>

    <!-- Payment Settings -->
    <a href="{{ route('admin.payment-settings') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.payment-settings') ? 'bg-primary text-white' : 'text-[#897561] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23]' }}">
        <span class="material-symbols-outlined text-[24px]">payment</span>
        <span class="font-medium">Payment Settings</span>
    </a>

    <!-- System Settings -->
    <a href="{{ route('admin.system-settings') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.system-settings') ? 'bg-primary text-white' : 'text-[#897561] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23]' }}">
        <span class="material-symbols-outlined text-[24px]">settings</span>
        <span class="font-medium">System Settings</span>
    </a>

    <!-- Logs & Audit -->
    <a href="{{ route('admin.logs') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.logs*') ? 'bg-primary text-white' : 'text-[#897561] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23]' }}">
        <span class="material-symbols-outlined text-[24px]">history</span>
        <span class="font-medium">Logs & Audit</span>
    </a>

    <!-- Divider -->
    <div class="my-2 border-t border-[#f4f2f0] dark:border-[#3e2d23]"></div>

    <!-- Profile -->
    <a href="{{ route('admin.profile') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.profile') ? 'bg-primary text-white' : 'text-[#897561] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23]' }}">
        <span class="material-symbols-outlined text-[24px]">person</span>
        <span class="font-medium">Profile</span>
    </a>
</nav>
