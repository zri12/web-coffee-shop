<nav class="flex-1 overflow-y-auto px-4 py-4 flex flex-col gap-2">
    <!-- Dashboard -->
    <a class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors group {{ request()->routeIs('manager.dashboard') ? 'bg-primary/10 text-primary dark:text-primary' : 'text-[#5c4d40] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#2c241b]' }}" href="{{ route('manager.dashboard') }}">
        <span class="material-symbols-outlined {{ request()->routeIs('manager.dashboard') ? '' : 'group-hover:text-[#181411] dark:group-hover:text-white' }}">dashboard</span>
        <p class="text-sm font-medium leading-normal {{ request()->routeIs('manager.dashboard') ? '' : 'group-hover:text-[#181411] dark:group-hover:text-white' }}">Dashboard</p>
    </a>

    <!-- Orders -->
    <a class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors group {{ request()->routeIs('manager.orders*') ? 'bg-primary/10 text-primary dark:text-primary' : 'text-[#5c4d40] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#2c241b]' }}" href="{{ route('manager.orders') }}">
        <span class="material-symbols-outlined {{ request()->routeIs('manager.orders*') ? '' : 'group-hover:text-[#181411] dark:group-hover:text-white' }}">receipt_long</span>
        <p class="text-sm font-medium leading-normal {{ request()->routeIs('manager.orders*') ? '' : 'group-hover:text-[#181411] dark:group-hover:text-white' }}">Orders</p>
    </a>

    <!-- Reports -->
    <a class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors group {{ request()->routeIs('manager.reports*') ? 'bg-primary/10 text-primary dark:text-primary' : 'text-[#5c4d40] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#2c241b]' }}" href="{{ route('manager.reports') }}">
        <span class="material-symbols-outlined {{ request()->routeIs('manager.reports*') ? '' : 'group-hover:text-[#181411] dark:group-hover:text-white' }}">bar_chart</span>
        <p class="text-sm font-medium leading-normal {{ request()->routeIs('manager.reports*') ? '' : 'group-hover:text-[#181411] dark:group-hover:text-white' }}">Reports</p>
    </a>

    <!-- Menu Monitoring -->
    <a class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors group {{ request()->routeIs('manager.menus*') ? 'bg-primary/10 text-primary dark:text-primary' : 'text-[#5c4d40] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#2c241b]' }}" href="{{ route('manager.menus') }}">
        <span class="material-symbols-outlined {{ request()->routeIs('manager.menus*') ? '' : 'group-hover:text-[#181411] dark:group-hover:text-white' }}">restaurant_menu</span>
        <p class="text-sm font-medium leading-normal {{ request()->routeIs('manager.menus*') ? '' : 'group-hover:text-[#181411] dark:group-hover:text-white' }}">Menu Monitoring</p>
    </a>

    <!-- Table / QR Monitoring -->
    <a class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors group {{ request()->routeIs('manager.tables*') ? 'bg-primary/10 text-primary dark:text-primary' : 'text-[#5c4d40] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#2c241b]' }}" href="{{ route('manager.tables') }}">
        <span class="material-symbols-outlined {{ request()->routeIs('manager.tables*') ? '' : 'group-hover:text-[#181411] dark:group-hover:text-white' }}">qr_code_scanner</span>
        <p class="text-sm font-medium leading-normal {{ request()->routeIs('manager.tables*') ? '' : 'group-hover:text-[#181411] dark:group-hover:text-white' }}">Table / QR Monitoring</p>
    </a>

    <!-- Storage & Inventory -->
    <a class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors group {{ request()->routeIs('admin.ingredients*') ? 'bg-primary/10 text-primary dark:text-primary' : 'text-[#5c4d40] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#2c241b]' }}" href="{{ route('admin.ingredients.index') }}">
        <span class="material-symbols-outlined {{ request()->routeIs('admin.ingredients*') ? '' : 'group-hover:text-[#181411] dark:group-hover:text-white' }}">inventory_2</span>
        <p class="text-sm font-medium leading-normal {{ request()->routeIs('admin.ingredients*') ? '' : 'group-hover:text-[#181411] dark:group-hover:text-white' }}">Storage & Inventory</p>
    </a>

    <!-- Inventory Analytics -->
    <a class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors group {{ request()->routeIs('admin.analytics*') ? 'bg-primary/10 text-primary dark:text-primary' : 'text-[#5c4d40] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#2c241b]' }}" href="{{ route('admin.analytics.inventory') }}">
        <span class="material-symbols-outlined {{ request()->routeIs('admin.analytics*') ? '' : 'group-hover:text-[#181411] dark:group-hover:text-white' }}">analytics</span>
        <p class="text-sm font-medium leading-normal {{ request()->routeIs('admin.analytics*') ? '' : 'group-hover:text-[#181411] dark:group-hover:text-white' }}">Inventory Analytics</p>
    </a>

    <!-- Staff Monitoring -->
    <a class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors group {{ request()->routeIs('manager.staff*') ? 'bg-primary/10 text-primary dark:text-primary' : 'text-[#5c4d40] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#2c241b]' }}" href="{{ route('manager.staff') }}">
        <span class="material-symbols-outlined {{ request()->routeIs('manager.staff*') ? '' : 'group-hover:text-[#181411] dark:group-hover:text-white' }}">group</span>
        <p class="text-sm font-medium leading-normal {{ request()->routeIs('manager.staff*') ? '' : 'group-hover:text-[#181411] dark:group-hover:text-white' }}">Staff Monitoring</p>
    </a>

    <!-- Profile -->
    <a class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors group {{ request()->routeIs('manager.profile*') ? 'bg-primary/10 text-primary dark:text-primary' : 'text-[#5c4d40] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#2c241b]' }}" href="{{ route('manager.profile') }}">
        <span class="material-symbols-outlined {{ request()->routeIs('manager.profile*') ? '' : 'group-hover:text-[#181411] dark:group-hover:text-white' }}">account_circle</span>
        <p class="text-sm font-medium leading-normal {{ request()->routeIs('manager.profile*') ? '' : 'group-hover:text-[#181411] dark:group-hover:text-white' }}">Profile</p>
    </a>
</nav>
