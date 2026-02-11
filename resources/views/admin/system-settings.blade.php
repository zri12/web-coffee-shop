@extends('layouts.dashboard')

@section('title', 'System Settings')

@section('content')
<div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-1">
        <h1 class="text-xl sm:text-2xl font-bold text-[#181411] dark:text-white">System Settings</h1>
        <p class="text-xs sm:text-sm text-[#897561] dark:text-[#a89c92]">Pengaturan sistem cafe</p>
    </div>

    <form action="{{ route('admin.system-settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4 sm:space-y-6">
        @csrf

        <!-- Cafe Information -->
        <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] p-4 sm:p-6">
            <h2 class="text-base sm:text-lg font-bold text-[#181411] dark:text-white mb-4 sm:mb-6">Informasi Cafe</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Nama Cafe</label>
                    <input type="text" name="cafe_name" value="{{ $cafe_name }}" required
                           class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Alamat</label>
                    <textarea name="address" rows="3"
                              class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">{{ $address }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Nomor Telepon</label>
                    <input type="tel" name="phone" value="{{ $phone }}"
                           class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Logo Cafe</label>
                    <input type="file" name="logo" accept="image/*"
                           class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                    <p class="text-xs text-[#897561] dark:text-[#a89c92] mt-1">Ukuran maksimal 2MB, format: JPG, PNG</p>
                </div>
            </div>
        </div>

        <!-- Operating Hours -->
        <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] p-4 sm:p-6">
            <h2 class="text-base sm:text-lg font-bold text-[#181411] dark:text-white mb-4 sm:mb-6">Jam Operasional</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Jam Buka</label>
                    <input type="time" name="opening_time" value="{{ $opening_time }}"
                           class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Jam Tutup</label>
                    <input type="time" name="closing_time" value="{{ $closing_time }}"
                           class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-[#181411] dark:text-white mb-3">Hari Libur</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach(['senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu', 'kamis' => 'Kamis', 'jumat' => 'Jumat', 'sabtu' => 'Sabtu', 'minggu' => 'Minggu'] as $dayValue => $dayLabel)
                    <label class="flex items-center gap-2 p-3 bg-[#faf8f6] dark:bg-[#0f0d0b] rounded-lg cursor-pointer hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23] transition-colors touch-manipulation">
                        <input type="checkbox" name="closed_days[]" value="{{ $dayValue }}"
                               @if(in_array($dayValue, $closed_days ?? [])) checked @endif
                               class="w-4 h-4 text-primary border-[#e6e0db] dark:border-[#3d362e] rounded focus:ring-primary">
                        <span class="text-sm text-[#181411] dark:text-white">{{ $dayLabel }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Social Media -->
        <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] p-4 sm:p-6">
            <h2 class="text-base sm:text-lg font-bold text-[#181411] dark:text-white mb-4 sm:mb-6">Media Sosial</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Instagram</label>
                    <input type="text" name="instagram" placeholder="@beanandbrew" value="{{ $instagram }}"
                           class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Facebook</label>
                    <input type="text" name="facebook" placeholder="facebook.com/beanandbrew" value="{{ $facebook }}"
                           class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">WhatsApp</label>
                    <input type="tel" name="whatsapp" placeholder="+62 812-3456-7890" value="{{ $whatsapp }}"
                           class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
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
