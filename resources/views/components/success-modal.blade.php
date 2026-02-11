<!-- Success Confirmation Modal Component -->
<!-- Reusable modal for success confirmations -->
<div 
    x-data="{ show: false, title: '', message: '', primaryAction: null, primaryLabel: 'OK', secondaryLabel: 'Cancel' }"
    x-show="show"
    x-cloak
    @show-success-modal.window="
        show = true; 
        title = $event.detail.title; 
        message = $event.detail.message;
        primaryAction = $event.detail.primaryAction;
        primaryLabel = $event.detail.primaryLabel || 'OK';
        secondaryLabel = $event.detail.secondaryLabel || 'Cancel';
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
            <!-- Success Icon -->
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-lg">
                    <span class="material-symbols-outlined text-white text-[32px] font-bold">check_circle</span>
                </div>
            </div>

            <!-- Title -->
            <h3 class="text-xl font-bold text-center text-[#181411] dark:text-white mb-2" x-text="title"></h3>

            <!-- Message -->
            <p class="text-center text-[#897561] dark:text-[#a89c92] mb-6 leading-relaxed" x-text="message"></p>

            <!-- Actions -->
            <div class="flex gap-3">
                <!-- Primary Button -->
                <button 
                    @click="if (primaryAction) primaryAction(); show = false;"
                    class="flex-1 px-4 py-3 bg-primary hover:bg-primary-dark text-white font-semibold rounded-xl transition-all transform hover:scale-105 active:scale-95 shadow-md hover:shadow-lg"
                >
                    <span x-text="primaryLabel"></span>
                </button>

                <!-- Secondary Button -->
                <button 
                    @click="show = false"
                    class="px-6 py-3 bg-[#f4f2f0] dark:bg-[#221910] hover:bg-[#e8e4df] dark:hover:bg-[#2c241b] text-[#181411] dark:text-white font-semibold rounded-xl transition-all transform hover:scale-105 active:scale-95"
                >
                    <span x-text="secondaryLabel"></span>
                </button>
            </div>
        </div>
    </div>
</div>
