<nav class="flex-1 px-4 py-6 space-y-1">
    <!-- Incoming Orders -->
    <a href="{{ route('cashier.incoming-orders') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('cashier.incoming-orders') ? 'bg-primary text-white' : 'text-[#897561] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23]' }}">
        <span class="material-symbols-outlined text-[24px]">notifications_active</span>
        <span class="font-medium">New Orders</span>
    </a>

    <!-- All Orders -->
    <a href="{{ route('cashier.orders') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('cashier.orders*') ? 'bg-primary text-white' : 'text-[#897561] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23]' }}">
        <span class="material-symbols-outlined text-[24px]">receipt_long</span>
        <span class="font-medium">All Orders</span>
    </a>

    <!-- Manual Order -->
    <a href="{{ route('cashier.manual-order') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('cashier.manual-order') ? 'bg-primary text-white' : 'text-[#897561] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23]' }}">
        <span class="material-symbols-outlined text-[24px]">add_shopping_cart</span>
        <span class="font-medium">Manual Order</span>
    </a>

    <!-- Order History -->
    <a href="{{ route('cashier.history') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('cashier.history') ? 'bg-primary text-white' : 'text-[#897561] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23]' }}">
        <span class="material-symbols-outlined text-[24px]">history</span>
        <span class="font-medium">Order History</span>
    </a>

    <!-- Profile -->
    <a href="{{ route('cashier.profile') }}" 
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('cashier.profile') ? 'bg-primary text-white' : 'text-[#897561] dark:text-[#a89c92] hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23]' }}">
        <span class="material-symbols-outlined text-[24px]">person</span>
        <span class="font-medium">Profile</span>
    </a>
</nav>
