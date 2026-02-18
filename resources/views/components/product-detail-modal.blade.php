<!-- Product Detail Modal -->
<div x-show="showDetail" 
     x-cloak
     @click.self="showDetail = false; document.body.style.overflow = ''"
     class="fixed inset-0 bg-black/50 z-50 flex md:items-center md:justify-center"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div @click.stop
         class="bg-white w-full h-full md:h-auto md:max-w-md md:max-h-[85vh] md:rounded-2xl flex flex-col overflow-hidden"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 md:scale-95"
         x-transition:enter-end="opacity-100 md:scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 md:scale-100"
         x-transition:leave-end="opacity-0 md:scale-95">
        
        <!-- Header with Image -->
        <div class="relative bg-[#F3F3F3]">
            <!-- Close Button -->
            <button @click="showDetail = false; document.body.style.overflow = ''" 
                    class="absolute top-4 left-4 w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg z-10 hover:bg-white transition-colors">
                <span class="material-symbols-outlined text-[#2F2D2C]">arrow_back</span>
            </button>

            <!-- Product Image with rounded bottom -->
            <div class="w-full h-[300px] md:h-[320px] bg-[#F9F9F9] relative">
                <img x-show="selectedProduct.image" 
                     :src="selectedProduct.image" 
                     :alt="selectedProduct.name" 
                     class="w-full h-full object-cover">
                <div x-show="!selectedProduct.image" class="w-full h-full flex items-center justify-center text-[#E0E0E0]">
                    <span class="material-symbols-outlined text-[80px]">local_cafe</span>
                </div>
            </div>
            
            <!-- White curved overlay -->
            <div class="absolute bottom-0 left-0 right-0 h-10 bg-white rounded-t-[32px]"></div>
        </div>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto px-5 pt-3 pb-6">
            <!-- Product Info -->
            <div class="mb-5">
                <h2 class="text-[20px] font-bold text-[#2F2D2C] mb-2" x-text="selectedProduct.name"></h2>
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-[28px] font-bold text-[#C67C4E]" x-text="(typeof formatPrice === 'function' ? formatPrice(calculateItemPrice()) : calculateItemPrice())"></span>
                    <span x-show="selectedProduct.isFeatured" 
                          class="bg-[#ED5151] text-white text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wide">
                        Best Seller
                    </span>
                </div>
                <p class="text-[14px] text-[#9B9B9B] leading-relaxed" x-text="selectedProduct.description"></p>
            </div>

            <!-- Options -->
            <div class="space-y-4">
                <template x-if="loadingOptions">
                    <div class="text-sm text-[#7c7a74]">Loading options...</div>
                </template>
                <template x-if="!loadingOptions && optionGroups.length === 0">
                    <div class="text-sm text-[#7c7a74]">No custom options for this product.</div>
                </template>
                <template x-for="group in optionGroups" :key="group.id">
                    <div class="rounded-2xl border border-[#E8E8E8] bg-white shadow-sm p-4 space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <h3 class="text-[16px] font-semibold text-[#2F2D2C]" x-text="group.name"></h3>
                                <span class="text-[11px] px-2 py-0.5 rounded-full bg-[#f3e8df] text-[#7c5b3a]" x-text="group.type === 'single' ? 'Single' : 'Multiple'"></span>
                                <template x-if="group.is_required">
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Required</span>
                                </template>
                            </div>
                            <template x-if="!group.values.length">
                                <span class="text-xs text-[#c94e4e]">No active values</span>
                            </template>
                        </div>
                        <div class="space-y-3">
                            <template x-for="value in group.values" :key="value.id">
                                <label class="flex items-center gap-3 p-3 rounded-xl border-2 transition-all"
                                       :class="[
                                            isSelected(group, value.id) ? 'border-[#C67C4E] bg-[#FFF5F0]' : 'border-[#E8E8E8] hover:border-[#C67C4E]',
                                            !value.is_available ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'
                                       ].join(' ')"
                                       :aria-disabled="!value.is_available">
                                    <input
                                        :type="group.type === 'single' ? 'radio' : 'checkbox'"
                                        class="w-5 h-5 text-[#C67C4E] rounded focus:ring-[#C67C4E]"
                                        :name="'option-' + group.id"
                                        :value="value.id"
                                        :disabled="!value.is_available"
                                        @change="toggleOption(group, value)"
                                        :checked="isSelected(group, value.id)"
                                    >
                                    <div class="flex-1 space-y-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-[14px] font-semibold text-[#2F2D2C]" x-text="value.name"></span>
                                            <span class="text-[12px] text-[#9B9B9B]" x-text="
                                                value.price_adjustment === 0
                                                    ? 'Free'
                                                    : `${value.price_adjustment > 0 ? '+ ' : '- '}${formatPrice(Math.abs(value.price_adjustment))}`
                                            "></span>
                                        </div>
                                        <div class="flex items-center gap-2 text-[12px]">
                                            <template x-if="!value.is_available">
                                                <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700">Unavailable</span>
                                            </template>
                                            <template x-if="value.is_available && value.stock !== null">
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700" x-text="'Stock: '+value.stock"></span>
                                            </template>
                                        </div>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Special Request -->
            <div class="mt-5">
                <h3 class="text-[16px] font-semibold text-[#2F2D2C] mb-3">Special Request</h3>
                <textarea 
                    x-model="specialRequest"
                    placeholder="Add any special instructions..."
                    class="w-full p-4 border-2 border-[#E8E8E8] rounded-xl text-[14px] text-[#2F2D2C] resize-none focus:border-[#C67C4E] focus:outline-none transition-all"
                    rows="3"></textarea>
            </div>

            <!-- Bottom spacing for fixed button -->
            <div class="h-24"></div>
        </div>

        <!-- Fixed Bottom Action -->
        <div class="bg-white border-t border-[#F1F1F1] px-5 py-4 pb-safe">
            <!-- Add to Cart Button (Qty always 1) -->
            <button @click="addToCartWithOptions()"
                    :disabled="!canAddToCart()"
                    class="w-full py-4 rounded-2xl font-bold text-[16px] shadow-lg transition-all flex items-center justify-center gap-2"
                    :class="canAddToCart() ? 'bg-[#C67C4E] text-white hover:bg-[#A05E35] active:scale-[0.99]' : 'bg-gray-300 text-gray-500 cursor-not-allowed'">
                <span class="material-symbols-outlined">add_shopping_cart</span>
                <span x-text="canAddToCart() ? `Add to Cart - ${(typeof formatPrice === 'function' ? formatPrice(calculateItemPrice()) : calculateItemPrice())}` : 'Pilih varian dulu'"></span>
            </button>
            <p class="text-[12px] text-center text-[#9B9B9B] mt-2">Each item added individually with chosen options</p>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    
    .pb-safe {
        padding-bottom: calc(1rem + env(safe-area-inset-bottom));
    }
</style>

