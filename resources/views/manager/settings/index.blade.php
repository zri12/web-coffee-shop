@extends('layouts.dashboard')

@section('title', 'System Settings')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-2">
        <div>
            <h1 class="text-2xl font-bold text-[#181411] dark:text-white">System Settings</h1>
            <p class="text-[#897561] text-sm">Configure your cafe's public identity and internal operations.</p>
        </div>
        <button class="text-[#897561] hover:text-orange-600 font-medium text-sm">Discard All Changes</button>
    </div>

    <!-- Cafe Profile -->
    <div class="bg-white dark:bg-[#1a1612] p-8 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm">
        <div class="flex justify-between items-start mb-6">
            <h3 class="flex items-center gap-2 text-lg font-bold text-[#181411] dark:text-white">
                <span class="material-symbols-outlined text-orange-600">storefront</span>
                Cafe Profile
            </h3>
            <button class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-primary/90 transition-colors">Save Changes</button>
        </div>
        
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Logo Upload -->
            <div class="flex flex-col items-center gap-3">
                <div class="relative w-32 h-32 rounded-full bg-yellow-100 flex items-center justify-center border-2 border-dashed border-orange-200">
                    <img src="{{ asset('logo/logo.png') }}" class="w-20 opacity-80" alt="Logo">
                    <button class="absolute bottom-0 right-0 bg-white p-2 rounded-full shadow-md border border-gray-200 text-orange-600 hover:bg-orange-50">
                        <span class="material-symbols-outlined text-sm">photo_camera</span>
                    </button>
                </div>
                <p class="text-xs text-center text-[#897561]">SVG, PNG, or JPG.<br>Max size 2MB.</p>
            </div>

            <!-- Profile Form -->
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-[#897561] uppercase tracking-wider">Cafe Name</label>
                    <input type="text" value="Bean & Brew Cafe" class="w-full px-4 py-2.5 bg-white dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 text-[#181411] dark:text-white">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-[#897561] uppercase tracking-wider">Contact Number</label>
                    <input type="text" value="+62 21 555 0123" class="w-full px-4 py-2.5 bg-white dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 text-[#181411] dark:text-white">
                </div>
                <div class="col-span-1 md:col-span-2 space-y-2">
                    <label class="text-xs font-bold text-[#897561] uppercase tracking-wider">Address</label>
                    <textarea class="w-full px-4 py-2.5 bg-white dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 text-[#181411] dark:text-white resize-none" rows="2">Jl. Senopati No. 45, Kebayoran Baru, Jakarta Selatan, 12190</textarea>
                </div>
                <div class="col-span-1 md:col-span-2 space-y-2">
                    <label class="text-xs font-bold text-[#897561] uppercase tracking-wider">Email Address</label>
                    <input type="email" value="hello@beanandbrew.com" class="w-full px-4 py-2.5 bg-white dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 text-[#181411] dark:text-white">
                </div>
            </div>
        </div>
    </div>

    <!-- Operating Hours -->
    <div class="bg-white dark:bg-[#1a1612] p-8 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm">
        <div class="flex justify-between items-start mb-6">
            <h3 class="flex items-center gap-2 text-lg font-bold text-[#181411] dark:text-white">
                <span class="material-symbols-outlined text-orange-600">schedule</span>
                Operating Hours
            </h3>
            <button class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-primary/90 transition-colors">Save Changes</button>
        </div>

        <div class="space-y-6">
            <!-- Weekdays -->
            <div class="flex items-center justify-between py-2">
                <div class="w-32 font-bold text-[#181411] dark:text-white">Monday</div>
                <div class="flex items-center gap-4">
                     <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" checked>
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-orange-500"></div>
                        <span class="ms-3 text-sm font-medium text-[#5c4d40] dark:text-[#a89c92]">Open</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="time" value="08:00" class="px-3 py-2 border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm bg-white dark:bg-[#2c241b] dark:text-white">
                        <span class="text-[#897561]">to</span>
                        <input type="time" value="21:00" class="px-3 py-2 border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm bg-white dark:bg-[#2c241b] dark:text-white">
                    </div>
                </div>
            </div>
             <!-- Weekends -->
            <div class="flex items-center justify-between py-2 border-t border-[#e6e0db] dark:border-[#3d362e]">
                <div class="w-32 font-bold text-[#181411] dark:text-white">Sat - Sun</div>
                <div class="flex items-center gap-4">
                     <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" checked>
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-orange-500"></div>
                        <span class="ms-3 text-sm font-medium text-[#5c4d40] dark:text-[#a89c92]">Open</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="time" value="09:00" class="px-3 py-2 border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm bg-white dark:bg-[#2c241b] dark:text-white">
                        <span class="text-[#897561]">to</span>
                        <input type="time" value="23:00" class="px-3 py-2 border border-[#e6e0db] dark:border-[#3d362e] rounded-lg text-sm bg-white dark:bg-[#2c241b] dark:text-white">
                    </div>
                </div>
            </div>
            <p class="text-xs text-[#897561] italic">* Holiday hours can be set separately in the Special Events tab.</p>
        </div>
    </div>

    <!-- Payment Systems -->
    <div class="bg-white dark:bg-[#1a1612] p-8 rounded-xl border border-[#e6e0db] dark:border-[#3d362e] shadow-sm">
        <div class="flex justify-between items-start mb-6">
            <h3 class="flex items-center gap-2 text-lg font-bold text-[#181411] dark:text-white">
                <span class="material-symbols-outlined text-orange-600">payments</span>
                Payment Systems
            </h3>
            <button class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-primary/90 transition-colors">Save Changes</button>
        </div>

        <div>
            <div class="flex items-center gap-3 mb-4">
                <h4 class="font-bold text-orange-500 uppercase text-xs tracking-wider">QRIS Settings</h4>
                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-bold uppercase">Active</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-[#897561] uppercase tracking-wider">Merchant ID</label>
                        <input type="text" value="MID-9928110822" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 text-[#181411] dark:text-white font-mono text-sm" readonly>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-[#897561] uppercase tracking-wider">Settlement Account</label>
                        <input type="text" value="Bank Mandiri **** 1290" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-[#2c241b] border border-[#e6e0db] dark:border-[#3d362e] rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 text-[#181411] dark:text-white text-sm" readonly>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#1a1612] border border-[#e6e0db] dark:border-[#3d362e] border-dashed rounded-xl p-4 flex items-center gap-4">
                    <div class="w-24 h-24 bg-gray-800 rounded-lg flex items-center justify-center text-white text-xs">QR Code</div>
                    <div>
                        <p class="font-bold text-[#181411] dark:text-white text-sm">Standard QRIS Code</p>
                        <button class="mt-2 px-3 py-1.5 border border-orange-200 text-orange-600 rounded-lg text-xs font-bold hover:bg-orange-50 transition-colors">Update QR Code</button>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-[#e6e0db] dark:border-[#3d362e]">
                <h4 class="font-bold text-[#181411] dark:text-white text-sm uppercase mb-4">Cashier & Checkout</h4>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-bold text-[#181411] dark:text-white text-sm">Accept Cash Payments</p>
                            <p class="text-xs text-[#897561]">Allow customers to pay at the counter.</p>
                        </div>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-orange-500"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                         <div>
                            <p class="font-bold text-[#181411] dark:text-white text-sm">Automatic Receipt Printing</p>
                            <p class="text-xs text-[#897561]">Print physical receipt immediately after payment.</p>
                        </div>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-orange-500"></div>
                        </label>
                    </div>
                     <div class="flex items-center justify-between">
                         <div>
                            <p class="font-bold text-[#181411] dark:text-white text-sm">Digital Invoice via Email</p>
                            <p class="text-xs text-[#897561]">Send PDF invoice to customer's registered email.</p>
                        </div>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-orange-500"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
