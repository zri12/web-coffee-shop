@extends('layouts.dashboard')

@section('title', 'Payment Settings')

@section('content')
<div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-1">
        <h1 class="text-xl sm:text-2xl font-bold text-[#181411] dark:text-white">Payment Settings</h1>
        <p class="text-xs sm:text-sm text-[#897561] dark:text-[#a89c92]">Konfigurasi metode pembayaran</p>
    </div>

    <form action="{{ route('admin.payment-settings.update') }}" method="POST" class="space-y-4 sm:space-y-6">
        @csrf

        <!-- Payment Methods -->
        <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] p-4 sm:p-6">
            <h2 class="text-base sm:text-lg font-bold text-[#181411] dark:text-white mb-4 sm:mb-6">Metode Pembayaran</h2>
            
            <div class="space-y-3 sm:space-y-4">
                <!-- Cash -->
                <div class="flex items-center justify-between p-3 sm:p-4 bg-[#faf8f6] dark:bg-[#0f0d0b] rounded-lg">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-50 dark:bg-green-900/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-green-600">payments</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-sm sm:text-base text-[#181411] dark:text-white">Cash</h3>
                            <p class="text-xs sm:text-sm text-[#897561] dark:text-[#a89c92]">Pembayaran tunai</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer touch-manipulation">
                        <input type="checkbox" name="cash_enabled" value="1" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 dark:peer-focus:ring-primary/40 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                    </label>
                </div>

                <!-- QRIS -->
                <div class="flex items-center justify-between p-3 sm:p-4 bg-[#faf8f6] dark:bg-[#0f0d0b] rounded-lg">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-blue-600">qr_code</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-sm sm:text-base text-[#181411] dark:text-white">QRIS</h3>
                            <p class="text-xs sm:text-sm text-[#897561] dark:text-[#a89c92]">Quick Response Code Indonesian Standard</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer touch-manipulation">
                        <input type="checkbox" name="qris_enabled" value="1" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 dark:peer-focus:ring-primary/40 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                    </label>
                </div>

                <!-- Card -->
                <div class="flex items-center justify-between p-3 sm:p-4 bg-[#faf8f6] dark:bg-[#0f0d0b] rounded-lg">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-50 dark:bg-purple-900/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-purple-600">credit_card</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-sm sm:text-base text-[#181411] dark:text-white">Card</h3>
                            <p class="text-xs sm:text-sm text-[#897561] dark:text-[#a89c92]">Debit/Credit Card</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer touch-manipulation">
                        <input type="checkbox" name="card_enabled" value="1" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 dark:peer-focus:ring-primary/40 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Midtrans Configuration -->
        <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] p-4 sm:p-6">
            <h2 class="text-base sm:text-lg font-bold text-[#181411] dark:text-white mb-4 sm:mb-6">Konfigurasi Midtrans</h2>
            
            <div class="p-4 sm:p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 mb-4">
                <p class="text-sm text-blue-800 dark:text-blue-200">
                    <strong>Catatan:</strong> Dapatkan Merchant ID, Server Key, dan Client Key dari <a href="https://dashboard.midtrans.com/settings/access-keys" target="_blank" class="underline font-semibold">dashboard.midtrans.com → Settings → Access Keys</a>
                </p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Merchant ID</label>
                    <input type="text" name="midtrans_merchant_id" placeholder="G696628577" value="{{ $midtransMerchantId }}"
                           class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Server Key</label>
                    <input type="password" name="midtrans_server_key" placeholder="SB-Mid-server-xxxxxxxx" value="{{ $midtransServerKey }}"
                           class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                    <p class="text-xs text-[#897561] dark:text-[#a89c92] mt-1">Gunakan password field untuk keamanan kunci sensitif</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Client Key</label>
                    <input type="text" name="midtrans_client_key" placeholder="SB-Mid-client-xxxxxxxx" value="{{ $midtransClientKey }}"
                           class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="midtrans_production" id="midtrans_production" value="1"
                           @if($midtransProduction) checked @endif
                           class="w-5 h-5 text-primary border-[#e6e0db] dark:border-[#3d362e] rounded focus:ring-primary rounded touch-manipulation">
                    <label for="midtrans_production" class="text-sm font-medium text-[#181411] dark:text-white">Mode Produksi
                        <span class="text-xs text-[#897561] dark:text-[#a89c92] ml-1">(Jika tidak dicentang, akan menggunakan mode sandbox)</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end pt-2">
            <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium touch-manipulation">
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
     class="fixed bottom-4 right-4 bg-green-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg shadow-lg z-50 text-sm sm:text-base">
    {{ session('success') }}
</div>
@endif
@endsection
