@extends('layouts.dashboard')

@section('title', 'Incoming Orders')

@section('content')
<div class="p-6 bg-gray-50 dark:bg-[#0d0b09] min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-2xl font-bold text-[#181411] dark:text-white mb-1">Incoming Orders</h1>
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-sm text-[#897561] dark:text-[#a89c92]">Kitchen Status: <span class="text-green-600 dark:text-green-500 font-semibold">Online</span></span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-[#897561]">{{ now()->format('M d, g:i A') }}</span>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Pending -->
        <div class="bg-white dark:bg-[#1a1612] rounded-xl p-4 border border-[#e6e0db] dark:border-[#3d362e] shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-[#897561] uppercase mb-1">Pending</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-black text-[#181411] dark:text-white">{{ $stats['pending'] }}</h3>
                        <span class="text-xs text-orange-600 font-semibold">+2 new</span>
                    </div>
                </div>
                <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-full">
                    <span class="material-symbols-outlined text-orange-600 text-[28px]">schedule</span>
                </div>
            </div>
        </div>

        <!-- Preparing -->
        <div class="bg-white dark:bg-[#1a1612] rounded-xl p-4 border border-[#e6e0db] dark:border-[#3d362e] shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-[#897561] uppercase mb-1">Preparing</p>
                    <h3 class="text-3xl font-black text-[#181411] dark:text-white">{{ $stats['preparing'] }}</h3>
                </div>
                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-full">
                    <span class="material-symbols-outlined text-yellow-600 text-[28px]">restaurant</span>
                </div>
            </div>
        </div>

        <!-- Completed Today -->
        <div class="bg-white dark:bg-[#1a1612] rounded-xl p-4 border border-[#e6e0db] dark:border-[#3d362e] shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-[#897561] uppercase mb-1">Completed Today</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-black text-[#181411] dark:text-white">{{ $stats['completed_today'] }}</h3>
                        <span class="text-xs text-green-600 font-semibold">+12%</span>
                    </div>
                </div>
                <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-full">
                    <span class="material-symbols-outlined text-green-600 text-[28px]">check_circle</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4">
        <!-- Pending Orders -->
        @foreach($pendingOrders as $order)
        <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm overflow-hidden hover:shadow-lg transition-shadow">
            <!-- Card Header -->
            <div class="relative">
                <!-- Status Badge -->
                <div class="absolute top-3 left-3 z-10">
                    <span class="px-2 py-1 bg-orange-500 text-white text-xs font-bold rounded-md flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">schedule</span>
                        {{ $order->created_at->diffForHumans(null, true, true) }} ago
                    </span>
                </div>
                
                <!-- Order Image -->
                <div class="h-32 bg-gradient-to-br from-[#f9f2ec] to-[#f4ebe0] dark:from-[#2c241b] dark:to-[#1a1612] flex items-center justify-center overflow-hidden">
                    @if($order->items->first() && $order->items->first()->menu && $order->items->first()->menu->image)
                        <img src="{{ asset('images/menus/' . $order->items->first()->menu->image) }}" 
                             alt="{{ $order->items->first()->menu_name }}" 
                             class="h-full w-full object-cover">
                    @else
                        <span class="material-symbols-outlined text-6xl text-[#897561]/30">local_cafe</span>
                    @endif
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-4">
                <!-- Order Number & Price -->
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="text-lg font-bold text-[#181411] dark:text-white">{{ $order->order_number }}</h3>
                        <div class="flex items-center gap-1 text-xs text-[#897561] mt-0.5">
                            @if($order->order_type === 'dine_in')
                                <span class="material-symbols-outlined text-[14px]">table_restaurant</span>
                                <span>Table {{ $order->table_number }}</span>
                            @else
                                <span class="material-symbols-outlined text-[14px]">shopping_bag</span>
                                <span>Takeaway</span>
                            @endif
                        </div>
                    </div>
                    <span class="text-lg font-black text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>

                <!-- Items List -->
                <div class="space-y-1 mb-4">
                    @foreach($order->items as $item)
                    <div>
                        <p class="text-sm text-[#181411] dark:text-white">
                            <span class="font-semibold">{{ $item->quantity }}x</span> 
                            {{ $item->menu_name ?? $item->menu->name ?? 'Menu Item' }}
                        </p>
                        @if($item->notes)
                        <p class="text-xs text-[#897561] italic ml-4">{{ $item->notes }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <button onclick="updateStatus({{ $order->id }}, 'processing')" 
                            class="flex-1 bg-primary hover:bg-primary-dark text-white font-bold py-2 rounded-lg transition-colors text-sm">
                        Start Preparing
                    </button>
                    <button onclick="printOrder({{ $order->id }})"
                            class="p-2 bg-gray-100 dark:bg-[#2c241b] hover:bg-gray-200 dark:hover:bg-[#3d362e] rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-[20px] text-[#897561]">print</span>
                    </button>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Preparing Orders -->
        @foreach($preparingOrders as $order)
        <div class="bg-white dark:bg-[#1a1612] rounded-xl border-2 border-yellow-400 shadow-sm overflow-hidden hover:shadow-lg transition-shadow">
            <!-- Card Header -->
            <div class="relative">
                <!-- Status Badge -->
                <div class="absolute top-3 left-3 z-10">
                    <span class="px-2 py-1 bg-yellow-500 text-white text-xs font-bold rounded-md uppercase flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">restaurant</span>
                        Preparing
                    </span>
                </div>
                
                <!-- Order Image -->
                <div class="h-32 bg-gradient-to-br from-[#f9f2ec] to-[#f4ebe0] dark:from-[#2c241b] dark:to-[#1a1612] flex items-center justify-center overflow-hidden">
                    @if($order->items->first() && $order->items->first()->menu && $order->items->first()->menu->image)
                        <img src="{{ asset('images/menus/' . $order->items->first()->menu->image) }}" 
                             alt="{{ $order->items->first()->menu_name }}" 
                             class="h-full w-full object-cover">
                    @else
                        <span class="material-symbols-outlined text-6xl text-[#897561]/30">local_cafe</span>
                    @endif
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-4">
                <!-- Order Number & Price -->
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="text-lg font-bold text-[#181411] dark:text-white">{{ $order->order_number }}</h3>
                        <div class="flex items-center gap-1 text-xs text-[#897561] mt-0.5">
                            @if($order->order_type === 'dine_in')
                                <span class="material-symbols-outlined text-[14px]">table_restaurant</span>
                                <span>Table {{ $order->table_number }}</span>
                            @else
                                <span class="material-symbols-outlined text-[14px]">shopping_bag</span>
                                <span>Takeaway</span>
                            @endif
                        </div>
                    </div>
                    <span class="text-lg font-black text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>

                <!-- Items List -->
                <div class="space-y-1 mb-4">
                    @foreach($order->items as $item)
                    <div>
                        <p class="text-sm text-[#181411] dark:text-white">
                            <span class="font-semibold">{{ $item->quantity }}x</span> 
                            {{ $item->menu_name ?? $item->menu->name ?? 'Menu Item' }}
                        </p>
                        @if($item->notes)
                        <p class="text-xs text-[#897561] italic ml-4">{{ $item->notes }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <button onclick="updateStatus({{ $order->id }}, 'completed')" 
                            class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 rounded-lg transition-colors text-sm">
                        Mark Ready
                    </button>
                    <button onclick="printOrder({{ $order->id }})"
                            class="p-2 bg-gray-100 dark:bg-[#2c241b] hover:bg-gray-200 dark:hover:bg-[#3d362e] rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-[20px] text-[#897561]">print</span>
                    </button>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Ready/Completed Orders -->
        @foreach($readyOrders as $order)
        <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm overflow-hidden opacity-75">
            <!-- Card Header -->
            <div class="relative">
                <!-- Status Badge -->
                <div class="absolute top-3 left-3 z-10">
                    <span class="px-2 py-1 bg-green-500 text-white text-xs font-bold rounded-md uppercase flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">check_circle</span>
                        Ready
                    </span>
                </div>
                
                <!-- Order Image -->
                <div class="h-32 bg-gradient-to-br from-[#f9f2ec] to-[#f4ebe0] dark:from-[#2c241b] dark:to-[#1a1612] flex items-center justify-center overflow-hidden">
                    @if($order->items->first() && $order->items->first()->menu && $order->items->first()->menu->image)
                        <img src="{{ asset('images/menus/' . $order->items->first()->menu->image) }}" 
                             alt="{{ $order->items->first()->menu_name }}" 
                             class="h-full w-full object-cover">
                    @else
                        <span class="material-symbols-outlined text-6xl text-[#897561]/30">local_cafe</span>
                    @endif
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-4">
                <!-- Order Number & Price -->
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="text-lg font-bold text-[#181411] dark:text-white">{{ $order->order_number }}</h3>
                        <div class="flex items-center gap-1 text-xs text-[#897561] mt-0.5">
                            @if($order->order_type === 'dine_in')
                                <span class="material-symbols-outlined text-[14px]">table_restaurant</span>
                                <span>Table {{ $order->table_number }}</span>
                            @else
                                <span class="material-symbols-outlined text-[14px]">shopping_bag</span>
                                <span>Walk-in</span>
                            @endif
                        </div>
                    </div>
                    <span class="text-lg font-black text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>

                <!-- Items List -->
                <div class="space-y-1 mb-4">
                    @foreach($order->items as $item)
                    <div>
                        <p class="text-sm text-[#181411] dark:text-white">
                            <span class="font-semibold">{{ $item->quantity }}x</span> 
                            {{ $item->menu_name ?? $item->menu->name ?? 'Menu Item' }}
                        </p>
                        @if($item->notes)
                        <p class="text-xs text-[#897561] italic ml-4">{{ $item->notes }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <button onclick="window.open('{{ route('cashier.orders.show', $order->id) }}', '_blank')" 
                            class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-2 rounded-lg transition-colors text-sm">
                        Complete Order
                    </button>
                    <button onclick="printOrder({{ $order->id }})"
                            class="p-2 bg-gray-100 dark:bg-[#2c241b] hover:bg-gray-200 dark:hover:bg-[#3d362e] rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-[20px] text-[#897561]">print</span>
                    </button>
                </div>
            </div>
        </div>
        @endforeach

        @if($pendingOrders->isEmpty() && $preparingOrders->isEmpty() && $readyOrders->isEmpty())
        <div class="col-span-full flex flex-col items-center justify-center py-16">
            <span class="material-symbols-outlined text-8xl text-[#897561]/20 mb-4">inbox</span>
            <p class="text-lg text-[#897561]">No active orders at the moment</p>
            <p class="text-sm text-[#897561]/70">New orders will appear here automatically</p>
        </div>
        @endif
    </div>

    <!-- Modern Modal -->
    <div id="customModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm transition-all duration-300">
        <div class="modal-content bg-white dark:bg-[#1a1612] rounded-2xl shadow-2xl p-6 max-w-md w-full mx-4 transform scale-95 transition-all duration-300">
            <div class="flex items-center gap-4 mb-4">
                <div id="modalIcon" class="flex items-center justify-center w-12 h-12 rounded-full">
                    <span class="material-symbols-outlined text-2xl"></span>
                </div>
                <div class="flex-1">
                    <h3 id="modalTitle" class="text-lg font-bold text-[#181411] dark:text-white"></h3>
                    <p id="modalMessage" class="text-sm text-[#897561] mt-1"></p>
                </div>
            </div>
            
            <div id="modalActions" class="flex gap-3 mt-6">
                <button id="modalCancel" class="flex-1 px-4 py-3 bg-gray-100 dark:bg-[#2c241b] hover:bg-gray-200 dark:hover:bg-[#3d362e] text-[#181411] dark:text-white font-semibold rounded-lg transition-colors">
                    Cancel
                </button>
                <button id="modalConfirm" class="flex-1 px-4 py-3 bg-primary hover:bg-orange-600 text-white font-bold rounded-lg transition-colors shadow-lg shadow-primary/20">
                    Confirm
                </button>
            </div>
            
            <button id="modalClose" class="hidden w-full px-4 py-3 bg-primary hover:bg-orange-600 text-white font-bold rounded-lg transition-colors mt-4">
                Close
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Modern Modal Functions
function showModal(type, title, message, onConfirm) {
    const modal = document.getElementById('customModal');
    const modalIcon = document.getElementById('modalIcon');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const modalActions = document.getElementById('modalActions');
    const modalClose = document.getElementById('modalClose');
    const modalConfirm = document.getElementById('modalConfirm');
    const modalCancel = document.getElementById('modalCancel');
    const iconEl = modalIcon.querySelector('.material-symbols-outlined');
    
    // Set content
    modalTitle.textContent = title;
    modalMessage.textContent = message;
    
    // Configure based on type
    if (type === 'confirm') {
        modalActions.classList.remove('hidden');
        modalClose.classList.add('hidden');
        modalIcon.className = 'flex items-center justify-center w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-900/30';
        iconEl.textContent = 'help';
        iconEl.className = 'material-symbols-outlined text-2xl text-orange-600 dark:text-orange-400';
        
        modalConfirm.onclick = () => {
            hideModal();
            if (onConfirm) onConfirm();
        };
        modalCancel.onclick = hideModal;
    } else if (type === 'success') {
        modalActions.classList.add('hidden');
        modalClose.classList.remove('hidden');
        modalIcon.className = 'flex items-center justify-center w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30';
        iconEl.textContent = 'check_circle';
        iconEl.className = 'material-symbols-outlined text-2xl text-green-600 dark:text-green-400';
        
        modalClose.onclick = hideModal;
    } else if (type === 'error') {
        modalActions.classList.add('hidden');
        modalClose.classList.remove('hidden');
        modalIcon.className = 'flex items-center justify-center w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30';
        iconEl.textContent = 'error';
        iconEl.className = 'material-symbols-outlined text-2xl text-red-600 dark:text-red-400';
        
        modalClose.onclick = hideModal;
    }
    
    // Show modal with animation
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        modal.querySelector('.modal-content').style.transform = 'scale(1)';
    }, 10);
}

function hideModal() {
    const modal = document.getElementById('customModal');
    const modalContent = modal.querySelector('.modal-content');
    modalContent.style.transform = 'scale(0.95)';
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

// Close modal on backdrop click
document.getElementById('customModal').addEventListener('click', (e) => {
    if (e.target.id === 'customModal') {
        hideModal();
    }
});

function printOrder(orderId) {
    // Open order detail in new window for printing
    const printWindow = window.open(`/cashier/orders/${orderId}`, '_blank');
    // Wait for window to load then trigger print
    if (printWindow) {
        printWindow.onload = function() {
            setTimeout(() => {
                printWindow.print();
            }, 500);
        };
    }
}

async function updateStatus(orderId, status) {
    const statusText = status === 'processing' ? 'Start Preparing' : 'Mark Ready';
    
    showModal('confirm', 'Update Order Status', `Are you sure you want to ${statusText} this order?`, async () => {
        try {
            const response = await fetch(`/cashier/orders/${orderId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: status })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showModal('success', 'Success!', 'Order status updated successfully!');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showModal('error', 'Error', 'Failed to update status: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            showModal('error', 'Error', 'An error occurred while updating the status');
        }
    });
}

// Auto refresh every 30 seconds
setInterval(() => {
    window.location.reload();
}, 30000);
</script>
@endpush
@endsection
