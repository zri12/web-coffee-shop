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
                    <span class="text-[28px] font-bold text-[#C67C4E]" x-text="selectedProduct.price"></span>
                    <span x-show="selectedProduct.isFeatured" 
                          class="bg-[#ED5151] text-white text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wide">
                        Best Seller
                    </span>
                </div>
                <p class="text-[14px] text-[#9B9B9B] leading-relaxed" x-text="selectedProduct.description"></p>
            </div>

            <!-- Options Container -->
            <div x-show="selectedProduct.type === 'beverage'" class="space-y-5">
                
                <!-- Temperature Option -->
                <div>
                    <h3 class="text-[16px] font-semibold text-[#2F2D2C] mb-3">Temperature</h3>
                    <div class="flex gap-3">
                        <button @click="temperature = 'ice'" 
                                :class="temperature === 'ice' ? 'bg-[#C67C4E] text-white' : 'bg-[#F9F9F9] text-[#2F2D2C]'"
                                class="flex-1 py-3 rounded-xl font-semibold text-[14px] transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">ac_unit</span>
                            <span>Ice</span>
                        </button>
                        <button @click="temperature = 'hot'" 
                                :class="temperature === 'hot' ? 'bg-[#C67C4E] text-white' : 'bg-[#F9F9F9] text-[#2F2D2C]'"
                                class="flex-1 py-3 rounded-xl font-semibold text-[14px] transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">local_fire_department</span>
                            <span>Hot</span>
                        </button>
                    </div>
                </div>

                <!-- Ice Level Option (only show when temperature is ice) -->
                <div x-show="temperature === 'ice'">
                    <h3 class="text-[16px] font-semibold text-[#2F2D2C] mb-3">Ice Level</h3>
                    <div class="flex gap-3">
                        <button @click="iceLevel = 'normal'" 
                                :class="iceLevel === 'normal' ? 'bg-[#C67C4E] text-white' : 'bg-[#F9F9F9] text-[#2F2D2C]'"
                                class="flex-1 py-3 rounded-xl font-semibold text-[14px] transition-all">
                            Normal
                        </button>
                        <button @click="iceLevel = 'less'" 
                                :class="iceLevel === 'less' ? 'bg-[#C67C4E] text-white' : 'bg-[#F9F9F9] text-[#2F2D2C]'"
                                class="flex-1 py-3 rounded-xl font-semibold text-[14px] transition-all">
                            Less
                        </button>
                        <button @click="iceLevel = 'no-ice'" 
                                :class="iceLevel === 'no-ice' ? 'bg-[#C67C4E] text-white' : 'bg-[#F9F9F9] text-[#2F2D2C]'"
                                class="flex-1 py-3 rounded-xl font-semibold text-[14px] transition-all">
                            No Ice
                        </button>
                    </div>
                </div>

                <!-- Sugar Level Option -->
                <div>
                    <h3 class="text-[16px] font-semibold text-[#2F2D2C] mb-3">Sugar Level</h3>
                    <div class="flex gap-3">
                        <button @click="sugarLevel = 'normal'" 
                                :class="sugarLevel === 'normal' ? 'bg-[#C67C4E] text-white' : 'bg-[#F9F9F9] text-[#2F2D2C]'"
                                class="flex-1 py-3 rounded-xl font-semibold text-[14px] transition-all">
                            Normal
                        </button>
                        <button @click="sugarLevel = 'less'" 
                                :class="sugarLevel === 'less' ? 'bg-[#C67C4E] text-white' : 'bg-[#F9F9F9] text-[#2F2D2C]'"
                                class="flex-1 py-3 rounded-xl font-semibold text-[14px] transition-all">
                            Less
                        </button>
                        <button @click="sugarLevel = 'no-sugar'" 
                                :class="sugarLevel === 'no-sugar' ? 'bg-[#C67C4E] text-white' : 'bg-[#F9F9F9] text-[#2F2D2C]'"
                                class="flex-1 py-3 rounded-xl font-semibold text-[14px] transition-all">
                            No Sugar
                        </button>
                    </div>
                </div>

                <!-- Size Option -->
                <div>
                    <h3 class="text-[16px] font-semibold text-[#2F2D2C] mb-3">Size</h3>
                    <div class="flex gap-3">
                        <button @click="size = 'regular'" 
                                :class="size === 'regular' ? 'border-2 border-[#C67C4E] bg-[#FFF5F0]' : 'border-2 border-[#E8E8E8] bg-white'"
                                class="flex-1 py-4 rounded-xl transition-all">
                            <div class="text-center">
                                <div class="text-[18px] font-bold text-[#2F2D2C] mb-1">Regular</div>
                                <div class="text-[12px] text-[#9B9B9B]">Standard</div>
                            </div>
                        </button>
                        <button @click="size = 'large'" 
                                :class="size === 'large' ? 'border-2 border-[#C67C4E] bg-[#FFF5F0]' : 'border-2 border-[#E8E8E8] bg-white'"
                                class="flex-1 py-4 rounded-xl transition-all">
                            <div class="text-center">
                                <div class="text-[18px] font-bold text-[#2F2D2C] mb-1">Large</div>
                                <div class="text-[12px] text-[#9B9B9B]">+Rp 8.000</div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Add-Ons for Beverages -->
                <div>
                    <h3 class="text-[16px] font-semibold text-[#2F2D2C] mb-3">Add-Ons (Optional)</h3>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-[#E8E8E8] cursor-pointer hover:border-[#C67C4E] transition-all">
                            <input type="checkbox" x-model="addOns" value="extra-shot" class="w-5 h-5 text-[#C67C4E] rounded">
                            <span class="flex-1 text-[14px] font-medium text-[#2F2D2C]">Extra Shot</span>
                            <span class="text-[12px] text-[#9B9B9B]">+Rp 5.000</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-[#E8E8E8] cursor-pointer hover:border-[#C67C4E] transition-all">
                            <input type="checkbox" x-model="addOns" value="whipped-cream" class="w-5 h-5 text-[#C67C4E] rounded">
                            <span class="flex-1 text-[14px] font-medium text-[#2F2D2C]">Whipped Cream</span>
                            <span class="text-[12px] text-[#9B9B9B]">+Rp 3.000</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-[#E8E8E8] cursor-pointer hover:border-[#C67C4E] transition-all">
                            <input type="checkbox" x-model="addOns" value="caramel-syrup" class="w-5 h-5 text-[#C67C4E] rounded">
                            <span class="flex-1 text-[14px] font-medium text-[#2F2D2C]">Caramel Syrup</span>
                            <span class="text-[12px] text-[#9B9B9B]">+Rp 3.000</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Options for Food/Snack -->
            <div x-show="selectedProduct.type === 'food'" class="space-y-5">
                <!-- Spice Level -->
                <div>
                    <h3 class="text-[16px] font-semibold text-[#2F2D2C] mb-3">Spice Level</h3>
                    <div class="flex gap-3">
                        <button @click="spiceLevel = 'mild'" 
                                :class="spiceLevel === 'mild' ? 'bg-[#C67C4E] text-white' : 'bg-[#F9F9F9] text-[#2F2D2C]'"
                                class="flex-1 py-3 rounded-xl font-semibold text-[14px] transition-all">
                            Mild
                        </button>
                        <button @click="spiceLevel = 'medium'" 
                                :class="spiceLevel === 'medium' ? 'bg-[#C67C4E] text-white' : 'bg-[#F9F9F9] text-[#2F2D2C]'"
                                class="flex-1 py-3 rounded-xl font-semibold text-[14px] transition-all">
                            Medium
                        </button>
                        <button @click="spiceLevel = 'spicy'" 
                                :class="spiceLevel === 'spicy' ? 'bg-[#C67C4E] text-white' : 'bg-[#F9F9F9] text-[#2F2D2C]'"
                                class="flex-1 py-3 rounded-xl font-semibold text-[14px] transition-all">
                            Spicy
                        </button>
                    </div>
                </div>

                <!-- Portion -->
                <div>
                    <h3 class="text-[16px] font-semibold text-[#2F2D2C] mb-3">Portion</h3>
                    <div class="flex gap-3">
                        <button @click="portion = 'regular'" 
                                :class="portion === 'regular' ? 'border-2 border-[#C67C4E] bg-[#FFF5F0]' : 'border-2 border-[#E8E8E8] bg-white'"
                                class="flex-1 py-4 rounded-xl transition-all">
                            <div class="text-center">
                                <div class="text-[16px] font-bold text-[#2F2D2C] mb-1">Regular</div>
                                <div class="text-[12px] text-[#9B9B9B]">Standard</div>
                            </div>
                        </button>
                        <button @click="portion = 'large'" 
                                :class="portion === 'large' ? 'border-2 border-[#C67C4E] bg-[#FFF5F0]' : 'border-2 border-[#E8E8E8] bg-white'"
                                class="flex-1 py-4 rounded-xl transition-all">
                            <div class="text-center">
                                <div class="text-[16px] font-bold text-[#2F2D2C] mb-1">Large</div>
                                <div class="text-[12px] text-[#9B9B9B]">+Rp 5.000</div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Add-Ons for Food -->
                <div>
                    <h3 class="text-[16px] font-semibold text-[#2F2D2C] mb-3">Add-Ons (Optional)</h3>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-[#E8E8E8] cursor-pointer hover:border-[#C67C4E] transition-all">
                            <input type="checkbox" x-model="addOns" value="extra-cheese" class="w-5 h-5 text-[#C67C4E] rounded">
                            <span class="flex-1 text-[14px] font-medium text-[#2F2D2C]">Extra Cheese</span>
                            <span class="text-[12px] text-[#9B9B9B]">+Rp 5.000</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-[#E8E8E8] cursor-pointer hover:border-[#C67C4E] transition-all">
                            <input type="checkbox" x-model="addOns" value="extra-egg" class="w-5 h-5 text-[#C67C4E] rounded">
                            <span class="flex-1 text-[14px] font-medium text-[#2F2D2C]">Extra Egg</span>
                            <span class="text-[12px] text-[#9B9B9B]">+Rp 3.000</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-[#E8E8E8] cursor-pointer hover:border-[#C67C4E] transition-all">
                            <input type="checkbox" x-model="addOns" value="extra-rice" class="w-5 h-5 text-[#C67C4E] rounded">
                            <span class="flex-1 text-[14px] font-medium text-[#2F2D2C]">Extra Rice</span>
                            <span class="text-[12px] text-[#9B9B9B]">+Rp 5.000</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Options for Snack -->
            <div x-show="selectedProduct.type === 'snack'" class="space-y-5">
                <!-- Size/Portion for Snack -->
                <div>
                    <h3 class="text-[16px] font-semibold text-[#2F2D2C] mb-3">Size</h3>
                    <div class="flex gap-3">
                        <button @click="portion = 'small'" 
                                :class="portion === 'small' ? 'border-2 border-[#C67C4E] bg-[#FFF5F0]' : 'border-2 border-[#E8E8E8] bg-white'"
                                class="flex-1 py-4 rounded-xl transition-all">
                            <div class="text-center">
                                <div class="text-[16px] font-bold text-[#2F2D2C] mb-1">Small</div>
                                <div class="text-[12px] text-[#9B9B9B]">-Rp 5.000</div>
                            </div>
                        </button>
                        <button @click="portion = 'regular'" 
                                :class="portion === 'regular' ? 'border-2 border-[#C67C4E] bg-[#FFF5F0]' : 'border-2 border-[#E8E8E8] bg-white'"
                                class="flex-1 py-4 rounded-xl transition-all">
                            <div class="text-center">
                                <div class="text-[16px] font-bold text-[#2F2D2C] mb-1">Regular</div>
                                <div class="text-[12px] text-[#9B9B9B]">Standard</div>
                            </div>
                        </button>
                        <button @click="portion = 'large'" 
                                :class="portion === 'large' ? 'border-2 border-[#C67C4E] bg-[#FFF5F0]' : 'border-2 border-[#E8E8E8] bg-white'"
                                class="flex-1 py-4 rounded-xl transition-all">
                            <div class="text-center">
                                <div class="text-[16px] font-bold text-[#2F2D2C] mb-1">Large</div>
                                <div class="text-[12px] text-[#9B9B9B]">+Rp 5.000</div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Sauce Options for Snack -->
                <div>
                    <h3 class="text-[16px] font-semibold text-[#2F2D2C] mb-3">Sauce Options</h3>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-[#E8E8E8] cursor-pointer hover:border-[#C67C4E] transition-all">
                            <input type="checkbox" x-model="sauces" value="ketchup" class="w-5 h-5 text-[#C67C4E] rounded">
                            <span class="flex-1 text-[14px] font-medium text-[#2F2D2C]">Ketchup</span>
                            <span class="text-[12px] text-[#9B9B9B]">Free</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-[#E8E8E8] cursor-pointer hover:border-[#C67C4E] transition-all">
                            <input type="checkbox" x-model="sauces" value="mayonnaise" class="w-5 h-5 text-[#C67C4E] rounded">
                            <span class="flex-1 text-[14px] font-medium text-[#2F2D2C]">Mayonnaise</span>
                            <span class="text-[12px] text-[#9B9B9B]">Free</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-[#E8E8E8] cursor-pointer hover:border-[#C67C4E] transition-all">
                            <input type="checkbox" x-model="sauces" value="chili" class="w-5 h-5 text-[#C67C4E] rounded">
                            <span class="flex-1 text-[14px] font-medium text-[#2F2D2C]">Chili Sauce</span>
                            <span class="text-[12px] text-[#9B9B9B]">Free</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-[#E8E8E8] cursor-pointer hover:border-[#C67C4E] transition-all">
                            <input type="checkbox" x-model="sauces" value="bbq" class="w-5 h-5 text-[#C67C4E] rounded">
                            <span class="flex-1 text-[14px] font-medium text-[#2F2D2C]">BBQ Sauce</span>
                            <span class="text-[12px] text-[#9B9B9B]">+Rp 2.000</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Options for Dessert -->
            <div x-show="selectedProduct.type === 'dessert'" class="space-y-5">
                <!-- Size/Portion for Dessert -->
                <div>
                    <h3 class="text-[16px] font-semibold text-[#2F2D2C] mb-3">Size</h3>
                    <div class="flex gap-3">
                        <button @click="portion = 'regular'" 
                                :class="portion === 'regular' ? 'border-2 border-[#C67C4E] bg-[#FFF5F0]' : 'border-2 border-[#E8E8E8] bg-white'"
                                class="flex-1 py-4 rounded-xl transition-all">
                            <div class="text-center">
                                <div class="text-[16px] font-bold text-[#2F2D2C] mb-1">Regular</div>
                                <div class="text-[12px] text-[#9B9B9B]">Standard</div>
                            </div>
                        </button>
                        <button @click="portion = 'large'" 
                                :class="portion === 'large' ? 'border-2 border-[#C67C4E] bg-[#FFF5F0]' : 'border-2 border-[#E8E8E8] bg-white'"
                                class="flex-1 py-4 rounded-xl transition-all">
                            <div class="text-center">
                                <div class="text-[16px] font-bold text-[#2F2D2C] mb-1">Large</div>
                                <div class="text-[12px] text-[#9B9B9B]">+Rp 8.000</div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Topping -->
                <div>
                    <h3 class="text-[16px] font-semibold text-[#2F2D2C] mb-3">Extra Topping</h3>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-[#E8E8E8] cursor-pointer hover:border-[#C67C4E] transition-all">
                            <input type="checkbox" x-model="toppings" value="chocolate" class="w-5 h-5 text-[#C67C4E] rounded">
                            <span class="flex-1 text-[14px] font-medium text-[#2F2D2C]">Chocolate Sauce</span>
                            <span class="text-[12px] text-[#9B9B9B]">+Rp 3.000</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-[#E8E8E8] cursor-pointer hover:border-[#C67C4E] transition-all">
                            <input type="checkbox" x-model="toppings" value="caramel" class="w-5 h-5 text-[#C67C4E] rounded">
                            <span class="flex-1 text-[14px] font-medium text-[#2F2D2C]">Caramel Drizzle</span>
                            <span class="text-[12px] text-[#9B9B9B]">+Rp 3.000</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-[#E8E8E8] cursor-pointer hover:border-[#C67C4E] transition-all">
                            <input type="checkbox" x-model="toppings" value="whipped" class="w-5 h-5 text-[#C67C4E] rounded">
                            <span class="flex-1 text-[14px] font-medium text-[#2F2D2C]">Whipped Cream</span>
                            <span class="text-[12px] text-[#9B9B9B]">+Rp 5.000</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-[#E8E8E8] cursor-pointer hover:border-[#C67C4E] transition-all">
                            <input type="checkbox" x-model="toppings" value="ice-cream" class="w-5 h-5 text-[#C67C4E] rounded">
                            <span class="flex-1 text-[14px] font-medium text-[#2F2D2C]">Ice Cream</span>
                            <span class="text-[12px] text-[#9B9B9B]">+Rp 8.000</span>
                        </label>
                    </div>
                </div>
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
                <span x-text="canAddToCart() ? `Add to Cart · ${formatPrice(calculateItemPrice())}` : 'Pilih varian dulu'"></span>
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

