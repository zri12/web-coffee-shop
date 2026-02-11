<!-- Order Confirmation Modal Component -->
<!-- Modal untuk konfirmasi order sebelum submit -->
<div 
    x-data="{ show: false, orderDetails: {}, totalAmount: 0, itemCount: 0, tableNumber: '', paymentMethod: '', customerName: '', confirmAction: null }"
    x-show="show"
    x-cloak
    @show-order-confirm-modal.window="
        show = true;
        orderDetails = $event.detail.orderDetails;
        totalAmount = $event.detail.totalAmount;
        itemCount = $event.detail.itemCount; 
        tableNumber = $event.detail.tableNumber;
        paymentMethod = $event.detail.paymentMethod;
        customerName = $event.detail.customerName;
        confirmAction = $event.detail.confirmAction;
    "
    @keydown.escape.window="show = false"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <!-- Backdrop -->
    <div 
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm"
        @click="show = false"
    ></div>

    <!-- Modal Container -->
    <div class="flex min-h-screen items-center justify-center p-4">
        <!-- Modal Content -->
        <div 
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-90"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-90"
            @click.stop
            class="relative w-full max-w-md bg-white dark:bg-[#2d2115] rounded-2xl shadow-2xl p-6"
        >
            <!-- Confirmation Icon -->
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center shadow-lg">
                    <span class="material-symbols-outlined text-white text-[32px] font-bold">shopping_cart_checkout</span>
                </div>
            </div>

            <!-- Title -->
            <h3 class="text-xl font-bold text-center text-[#181411] dark:text-white mb-2">Konfirmasi Order</h3>

            <!-- Order Details -->
            <div class="mb-6 space-y-3">
                <!-- Table & Customer -->
                <div class="bg-[#f8f6f3] dark:bg-[#1a1410] rounded-lg p-3">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-[#897561] dark:text-[#a89c92]">Meja:</span>
                        <span class="text-sm font-bold text-[#181411] dark:text-white" x-text="tableNumber"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-[#897561] dark:text-[#a89c92]">Customer:</span>
                        <span class="text-sm font-bold text-[#181411] dark:text-white" x-text="customerName || 'Guest'"></span>
                    </div>
                </div>

                <!-- Items Summary -->
                <div class="bg-[#f8f6f3] dark:bg-[#1a1410] rounded-lg p-3">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-[#897561] dark:text-[#a89c92]">Total Items:</span>
                        <span class="text-sm font-bold text-[#181411] dark:text-white" x-text="itemCount + ' items'"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-[#897561] dark:text-[#a89c92]">Total Amount:</span>
                        <span class="text-lg font-bold text-primary" x-text="'Rp ' + totalAmount.toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-[#f8f6f3] dark:bg-[#1a1410] rounded-lg p-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-[#897561] dark:text-[#a89c92]">Payment Method:</span>
                        <span class="text-sm font-bold text-[#181411] dark:text-white uppercase" x-text="paymentMethod"></span>
                    </div>
                </div>
            </div>

            <!-- Confirmation Message -->
            <p class="text-center text-[#897561] dark:text-[#a89c92] mb-6 leading-relaxed">
                Pastikan detail order sudah benar sebelum melanjutkan.
            </p>

            <!-- Actions -->
            <div class="flex gap-3">
                <!-- Cancel Button -->
                <button 
                    @click="show = false"
                    class="px-6 py-3 bg-[#f4f2f0] dark:bg-[#221910] hover:bg-[#e8e4df] dark:hover:bg-[#2c241b] text-[#181411] dark:text-white font-semibold rounded-xl transition-all transform hover:scale-105 active:scale-95"
                >
                    Batal
                </button>

                <!-- Confirm Button -->
                <button 
                    @click="if (confirmAction) confirmAction(); show = false;"
                    class="flex-1 px-4 py-3 bg-gradient-to-r from-primary to-orange-600 hover:from-orange-600 hover:to-primary text-white font-bold rounded-xl transition-all transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl flex items-center justify-center gap-2"
                >
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    <span>Konfirmasi Order</span>
                </button>
            </div>
        </div>
    </div>
</div>