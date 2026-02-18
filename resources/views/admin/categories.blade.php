@extends('layouts.dashboard')

@section('title', 'Category Management')

@section('content')
@php
    $optionGroupMap = collect($optionDefinitions)->keyBy('key');
@endphp
<div class="p-4 sm:p-6 space-y-4 sm:space-y-6" x-data="categoryPage()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#181411] dark:text-white">Category Management</h1>
            <p class="text-xs sm:text-sm text-[#897561] dark:text-[#a89c92] mt-1">Kelola kategori menu</p>
        </div>
        <button @click="openModal()" 
                class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium flex items-center justify-center gap-2 text-sm sm:text-base touch-manipulation">
            <span class="material-symbols-outlined text-[20px] sm:text-[24px]">add</span>
            <span>Tambah Kategori</span>
        </button>
    </div>

    <!-- Categories Table/Cards -->
    <div class="bg-white dark:bg-[#1a1612] rounded-xl border border-[#e6e0db] dark:border-[#3d362e] overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-[#e6e0db] dark:border-[#3d362e]">
            <h2 class="text-base sm:text-lg font-bold text-[#181411] dark:text-white">Daftar Kategori</h2>
        </div>
        
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-[#faf8f6] dark:bg-[#0f0d0b]">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#897561] dark:text-[#a89c92] uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#897561] dark:text-[#a89c92] uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#897561] dark:text-[#a89c92] uppercase tracking-wider">Option Sets</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-[#897561] dark:text-[#a89c92] uppercase tracking-wider">Jumlah Menu</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-[#897561] dark:text-[#a89c92] uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-[#897561] dark:text-[#a89c92] uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e6e0db] dark:divide-[#3d362e]">
                    @forelse($categories as $category)
                    <tr class="hover:bg-[#faf8f6] dark:hover:bg-[#0f0d0b] transition-colors">
                        @php
                            $categoryPayloadJson = json_encode(
                                $category->only(['id','name','description','is_active','option_flags']),
                                JSON_UNESCAPED_UNICODE
                            );
                        @endphp
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-primary">category</span>
                                </div>
                                <span class="font-medium text-[#181411] dark:text-white">{{ $category->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-[#897561] dark:text-[#a89c92]">
                            {{ $category->description ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $activeFlags = collect($category->option_flags_with_defaults ?? [])->filter(fn($value) => $value);
                                    $activeLabels = $activeFlags->keys()->map(fn($key) => $optionGroupMap[$key]['name'] ?? ucfirst(str_replace('_', ' ', $key)));
                                    $visibleLabels = $activeLabels->slice(0, 4);
                                    $hiddenCount = max(0, $activeLabels->count() - 4);
                                @endphp
                                @forelse($visibleLabels as $label)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border border-[#e6e0db] text-[#5c4d40] bg-[#f5f2ec]">
                                        {{ $label }}
                                    </span>
                                @empty
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border border-dashed text-[#897561]">
                                        Default
                                    </span>
                                @endforelse
                                @if($hiddenCount > 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border border-[#e6e0db] text-[#5c4d40] bg-[#f5f2ec]">
                                        +{{ $hiddenCount }} more
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-900/20 text-blue-600">
                                {{ $category->menus_count }} menu
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($category->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-50 dark:bg-green-900/20 text-green-600">
                                    <span class="w-1.5 h-1.5 bg-green-600 rounded-full mr-2"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-50 dark:bg-red-900/20 text-red-600">
                                    <span class="w-1.5 h-1.5 bg-red-600 rounded-full mr-2"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button @click='openModal({!! $categoryPayloadJson !!})'
                                        class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </button>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <span class="material-symbols-outlined text-4xl sm:text-6xl text-[#897561]/30">category</span>
                            <p class="text-sm sm:text-base text-[#897561] dark:text-[#a89c92] mt-4">Belum ada kategori</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-[#e6e0db] dark:divide-[#3d362e]">
            @forelse($categories as $category)
            <div class="p-4 hover:bg-[#faf8f6] dark:hover:bg-[#0f0d0b] transition-colors">
                @php
                    $categoryPayloadJson = json_encode(
                        $category->only(['id','name','description','is_active','option_flags']),
                        JSON_UNESCAPED_UNICODE
                    );
                @endphp
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-primary">category</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-[#181411] dark:text-white">{{ $category->name }}</h3>
                        <p class="text-xs text-[#897561] dark:text-[#a89c92] mt-1 line-clamp-2">{{ $category->description ?? 'Tidak ada deskripsi' }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-900/20 text-blue-600">
                                {{ $category->menus_count }} menu
                            </span>
                            @if($category->is_active)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-50 dark:bg-green-900/20 text-green-600">
                                    <span class="w-1.5 h-1.5 bg-green-600 rounded-full mr-1"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-50 dark:bg-red-900/20 text-red-600">
                                    <span class="w-1.5 h-1.5 bg-red-600 rounded-full mr-1"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </div>
                        @php
                            $mobileLabels = collect($category->option_flags_with_defaults ?? [])
                                ->filter(fn($value) => $value)
                                ->keys()
                                ->map(fn($key) => $optionGroupMap[$key]['name'] ?? ucfirst(str_replace('_', ' ', $key)))
                                ->slice(0, 3);
                        @endphp
                        <div class="flex flex-wrap gap-2 mt-2">
                            @forelse($mobileLabels as $label)
                                <span class="px-2 py-1 rounded-full text-[10px] font-semibold border border-[#e6e0db] text-[#5c4d40] bg-[#f5f2ec]">
                                    {{ $label }}
                                </span>
                            @empty
                                <span class="px-2 py-1 rounded-full text-[10px] font-semibold border border-dashed text-[#897561]">
                                    Default
                                </span>
                            @endforelse
                        </div>
                    </div>
                    <div class="flex gap-2 flex-shrink-0">
                        <button @click='openModal({!! $categoryPayloadJson !!})'
                                class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors touch-manipulation">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </button>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors touch-manipulation">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <span class="material-symbols-outlined text-4xl text-[#897561]/30">category</span>
                <p class="text-sm text-[#897561] dark:text-[#a89c92] mt-4">Belum ada kategori</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Add/Edit Category -->
    <div x-show="showModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm"
         @click.self="closeModal()">
        <div class="bg-white dark:bg-[#1a1612] rounded-t-2xl sm:rounded-xl w-full sm:max-w-lg max-h-[95vh] sm:max-h-[90vh] overflow-y-auto">
            <div class="p-4 sm:p-6 border-b border-[#e6e0db] dark:border-[#3d362e] flex items-center justify-between sticky top-0 bg-white dark:bg-[#1a1612] z-10">
                <h3 class="text-lg sm:text-xl font-bold text-[#181411] dark:text-white" x-text="editMode ? 'Edit Kategori' : 'Tambah Kategori'"></h3>
                <button @click="closeModal()" class="text-[#897561] hover:text-[#181411] dark:hover:text-white p-2 -mr-2 touch-manipulation">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <form :action="editMode ? `/admin/categories/${currentCategory?.id}` : '{{ route('admin.categories.store') }}'" method="POST" class="p-4 sm:p-6 space-y-4">
                @csrf
                <template x-if="editMode">
                    @method('PUT')
                </template>
                
                <div>
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Nama Kategori</label>
                    <input type="text" name="name" :value="currentCategory?.name" required
                           class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-base">
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Deskripsi</label>
                    <textarea name="description" rows="3" :value="currentCategory?.description"
                              class="w-full px-4 py-3 rounded-lg border border-[#e6e0db] dark:border-[#3d362e] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent text-base"></textarea>
                </div>

                <div class="pt-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-[#a89c92]">Product Options</p>
                            <h3 class="text-lg font-semibold text-[#181411] dark:text-white">Toggle option groups</h3>
                        </div>
                        <span class="text-xs text-[#897561]">Enable per category</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <template x-for="option in optionDefinitions" :key="option.key">
                            <div>
                                <button type="button"
                                        @click="toggleFlag(option.key)"
                                        :title="option.description"
                                        :class="optionSettings[option.key] ? 'border-primary bg-primary/10 text-primary' : 'border-[#e6e0db] bg-white dark:bg-[#0f0d0b] text-[#181411] dark:text-white'"
                                        class="w-full px-3 py-2 rounded-2xl border text-sm font-semibold transition-colors text-left">
                                    <span x-text="option.name"></span>
                                </button>
                                <input type="hidden" :name="'option_flags[' + option.key + ']'" :value="optionSettings[option.key] ? 1 : 0">
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1" :checked="currentCategory?.is_active ?? true"
                           class="w-5 h-5 text-primary border-[#e6e0db] dark:border-[#3d362e] rounded focus:ring-primary">
                    <label for="is_active" class="text-sm font-medium text-[#181411] dark:text-white">Kategori Aktif</label>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <button type="button" @click="showModal = false"
                            class="w-full sm:flex-1 px-6 py-3 border border-[#e6e0db] dark:border-[#3d362e] text-[#897561] dark:text-[#a89c92] rounded-lg hover:bg-[#f4f2f0] dark:hover:bg-[#3e2d23] transition-colors font-medium touch-manipulation">
                        Batal
                    </button>
                    <button type="submit"
                            class="w-full sm:flex-1 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium touch-manipulation">
                        <span x-text="editMode ? 'Update' : 'Simpan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function categoryPage() {
        return {
            showModal: false,
            editMode: false,
            currentCategory: null,
            optionDefinitions: @json($optionDefinitions),
            optionFlagsDefaults: @json($optionFlagsDefaults),
            optionSettings: {},
            openModal(category = null) {
                this.editMode = !!category;
                this.currentCategory = category;
                this.optionSettings = {};
                this.optionDefinitions.forEach(option => {
                    const stored = category?.option_flags?.[option.key];
                    this.optionSettings[option.key] = stored === undefined
                        ? Boolean(this.optionFlagsDefaults[option.key] ?? false)
                        : Boolean(stored);
                });
                this.showModal = true;
            },
            toggleFlag(key) {
                this.optionSettings[key] = !this.optionSettings[key];
            },
            closeModal() {
                this.showModal = false;
                this.currentCategory = null;
            },
        };
    }
</script>
@endpush

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
     class="fixed bottom-4 right-4 bg-green-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg shadow-lg z-50 text-sm sm:text-base">
    {{ session('success') }}
</div>
@endif
@endsection
