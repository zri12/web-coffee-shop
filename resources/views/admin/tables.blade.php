@extends('layouts.dashboard')

@section('title', 'Table / QR Monitoring')

@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp

@section('content')
<div class="p-6 space-y-6" x-data="tableManager()">
    <!-- Header with Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#181411] dark:text-white">Table / QR Monitoring</h1>
            <p class="text-sm text-[#897561] dark:text-[#a89c92] mt-1">Manage cafe tables and generate QR codes</p>
        </div>
        <div class="flex gap-3">
            <button @click="openCreateModal()" class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                <span class="material-symbols-outlined text-[20px]">add</span>
                <span class="text-sm font-medium">Add Table</span>
            </button>
            <button onclick="printAllQR()" class="flex items-center gap-2 px-4 py-2 bg-[#f4f2f0] dark:bg-[#2c241b] text-[#181411] dark:text-white rounded-lg hover:bg-[#e8e4df] dark:hover:bg-[#3e2d23] transition-colors">
                <span class="material-symbols-outlined text-[20px]">print</span>
                <span class="text-sm font-medium">Print All QR</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white dark:bg-[#1a1612] rounded-xl p-4 border border-[#e6e0db] dark:border-[#3d362e]">
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#897561] dark:text-[#a89c92]">search</span>
                    <input 
                        type="text" 
                        x-model="searchQuery"
                        @input="filterTables()"
                        placeholder="Search table number..." 
                        class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-[#2c241b] border border-transparent focus:border-primary rounded-lg text-sm text-[#181411] dark:text-white placeholder-[#897561] dark:placeholder-[#a89c92]">
                </div>
            </div>
            
            <!-- Status Filter -->
            <div class="flex gap-2">
                <button @click="statusFilter = 'all'; filterTables()" :class="statusFilter === 'all' ? 'bg-primary text-white' : 'bg-gray-50 dark:bg-[#2c241b] text-[#181411] dark:text-white'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    All
                </button>
                <button @click="statusFilter = 'available'; filterTables()" :class="statusFilter === 'available' ? 'bg-green-600 text-white' : 'bg-gray-50 dark:bg-[#2c241b] text-[#181411] dark:text-white'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Available
                </button>
                <button @click="statusFilter = 'occupied'; filterTables()" :class="statusFilter === 'occupied' ? 'bg-red-600 text-white' : 'bg-gray-50 dark:bg-[#2c241b] text-[#181411] dark:text-white'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Occupied
                </button>
                <button @click="statusFilter = 'reserved'; filterTables()" :class="statusFilter === 'reserved' ? 'bg-yellow-600 text-white' : 'bg-gray-50 dark:bg-[#2c241b] text-[#181411] dark:text-white'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Reserved
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-[#1a1612] rounded-xl p-4 border border-[#e6e0db] dark:border-[#3d362e]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[#897561] dark:text-[#a89c92]">Total Tables</p>
                    <p class="text-2xl font-bold text-[#181411] dark:text-white mt-1">{{ $tables->count() }}</p>
                </div>
                <div class="size-12 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">table_restaurant</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1a1612] rounded-xl p-4 border border-[#e6e0db] dark:border-[#3d362e]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[#897561] dark:text-[#a89c92]">Available</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $tables->where('status', 'available')->count() }}</p>
                </div>
                <div class="size-12 rounded-lg bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600 dark:text-green-400">check_circle</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1a1612] rounded-xl p-4 border border-[#e6e0db] dark:border-[#3d362e]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[#897561] dark:text-[#a89c92]">Occupied</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $tables->where('status', 'occupied')->count() }}</p>
                </div>
                <div class="size-12 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-red-600 dark:text-red-400">event_seat</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1a1612] rounded-xl p-4 border border-[#e6e0db] dark:border-[#3d362e]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[#897561] dark:text-[#a89c92]">Reserved</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $tables->where('status', 'reserved')->count() }}</p>
                </div>
                <div class="size-12 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-yellow-600 dark:text-yellow-400">bookmark</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($tables as $table)
        <div class="table-card bg-white dark:bg-[#1a1612] rounded-xl border-2 border-[#e6e0db] dark:border-[#3d362e] overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1" 
             data-status="{{ $table->status }}" 
             data-number="{{ $table->table_number }}">
            
            <!-- Status Banner -->
            <div class="h-2 
                @if($table->status === 'available') bg-gradient-to-r from-green-500 to-green-600
                @elseif($table->status === 'occupied') bg-gradient-to-r from-red-500 to-red-600 animate-pulse
                @else bg-gradient-to-r from-yellow-500 to-yellow-600
                @endif">
            </div>

            <!-- Table Header -->
            <div class="p-4 border-b border-[#e6e0db] dark:border-[#3d362e]">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-[#181411] dark:text-white">Table {{ $table->table_number }}</h3>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="material-symbols-outlined text-[16px] text-[#897561] dark:text-[#a89c92]">group</span>
                            <p class="text-sm text-[#897561] dark:text-[#a89c92]">{{ $table->capacity }} seats</p>
                        </div>
                        @if($table->active_orders_count > 0)
                        <div class="flex items-center gap-2 mt-1">
                            <span class="material-symbols-outlined text-[16px] text-red-600">receipt</span>
                            <p class="text-sm text-red-600 font-medium">{{ $table->active_orders_count }} active order(s)</p>
                        </div>
                        @endif
                    </div>
                    <div class="flex flex-col gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold text-center
                            @if($table->status === 'available') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                            @elseif($table->status === 'occupied') bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400
                            @else bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400
                            @endif">
                            {{ ucfirst($table->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- QR Code -->
            <div class="p-6 flex flex-col items-center bg-gradient-to-br from-gray-50 to-[#f4f2f0] dark:from-[#2c241b] dark:to-[#3d362e]">
                <div class="bg-white p-4 rounded-xl shadow-lg" id="qr-{{ $table->id }}">
                    {!! QrCode::size(160)->generate($table->qr_url) !!}
                </div>
                <p class="text-xs text-[#897561] dark:text-[#a89c92] mt-3 text-center font-medium">Scan to order from Table {{ $table->table_number }}</p>
            </div>

            <!-- Quick Actions -->
            <div class="p-3 bg-gray-50 dark:bg-[#2c241b] border-t border-[#e6e0db] dark:border-[#3d362e]">
                <div class="grid grid-cols-2 gap-2">
                    <button @click="openEditModal({{ $table->id }}, '{{ $table->table_number }}', {{ $table->capacity }})" class="flex items-center justify-center gap-1 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors text-sm font-medium">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                        <span>Edit</span>
                    </button>
                    <button @click="deleteTable({{ $table->id }}, '{{ $table->table_number }}')" class="flex items-center justify-center gap-1 px-3 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors text-sm font-medium">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                        <span>Delete</span>
                    </button>
                    <button onclick="downloadQR('{{ $table->id }}', '{{ $table->table_number }}')" class="flex items-center justify-center gap-1 px-3 py-2 bg-primary/10 text-primary rounded-lg hover:bg-primary/20 transition-colors text-sm font-medium">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        <span>Download</span>
                    </button>
                    <button onclick="printQR('{{ $table->id }}')" class="flex items-center justify-center gap-1 px-3 py-2 bg-[#f4f2f0] dark:bg-[#3d362e] text-[#181411] dark:text-white rounded-lg hover:bg-[#e8e4df] dark:hover:bg-[#4a3a31] transition-colors text-sm font-medium">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                        <span>Print</span>
                    </button>
                </div>
                
                <!-- Status Change Buttons -->
                <div class="mt-2 pt-2 border-t border-[#e6e0db] dark:border-[#3d362e]">
                    <p class="text-xs text-[#897561] dark:text-[#a89c92] mb-2">Quick Status:</p>
                    <div class="grid grid-cols-3 gap-1">
                        <button @click="updateStatus({{ $table->id }}, 'available')" class="px-2 py-1 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded text-xs font-medium hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                            Available
                        </button>
                        <button @click="updateStatus({{ $table->id }}, 'occupied')" class="px-2 py-1 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded text-xs font-medium hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                            Occupied
                        </button>
                        <button @click="updateStatus({{ $table->id }}, 'reserved')" class="px-2 py-1 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400 rounded text-xs font-medium hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors">
                            Reserved
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="showModal" 
         x-cloak
         @click.self="closeModal()"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div @click.stop 
             class="bg-white dark:bg-[#1a1612] rounded-xl shadow-2xl max-w-md w-full"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <!-- Modal Header -->
            <div class="p-6 border-b border-[#e6e0db] dark:border-[#3d362e]">
                <h3 class="text-xl font-bold text-[#181411] dark:text-white" x-text="editMode ? 'Edit Table' : 'Create New Table'"></h3>
            </div>

            <!-- Modal Body -->
            <form @submit.prevent="submitForm()" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Table Number</label>
                    <input 
                        type="text" 
                        x-model="formData.table_number"
                        required
                        maxlength="20"
                        class="w-full px-4 py-2 bg-gray-50 dark:bg-[#2c241b] border border-transparent focus:border-primary rounded-lg text-[#181411] dark:text-white"
                        placeholder="e.g., 1, VIP-1, Outdoor-1">
                    <p class="text-xs text-[#897561] dark:text-[#a89c92] mt-1">Unique identifier for this table</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#181411] dark:text-white mb-2">Capacity (Seats)</label>
                    <input 
                        type="number" 
                        x-model="formData.capacity"
                        required
                        min="1"
                        max="20"
                        class="w-full px-4 py-2 bg-gray-50 dark:bg-[#2c241b] border border-transparent focus:border-primary rounded-lg text-[#181411] dark:text-white"
                        placeholder="e.g., 4">
                </div>

                <!-- Error Message -->
                <div x-show="errorMessage" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <p class="text-sm text-red-600 dark:text-red-400" x-text="errorMessage"></p>
                </div>

                <!-- Modal Actions -->
                <div class="flex gap-3 pt-4">
                    <button type="button" @click="closeModal()" class="flex-1 px-4 py-2 bg-gray-100 dark:bg-[#2c241b] text-[#181411] dark:text-white rounded-lg hover:bg-gray-200 dark:hover:bg-[#3d362e] transition-colors font-medium">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium">
                        <span x-text="editMode ? 'Update Table' : 'Create Table'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
function tableManager() {
    return {
        showModal: false,
        editMode: false,
        editId: null,
        searchQuery: '',
        statusFilter: 'all',
        errorMessage: '',
        formData: {
            table_number: '',
            capacity: 4
        },

        openCreateModal() {
            this.editMode = false;
            this.editId = null;
            this.formData = { table_number: '', capacity: 4 };
            this.errorMessage = '';
            this.showModal = true;
        },

        openEditModal(id, number, capacity) {
            this.editMode = true;
            this.editId = id;
            this.formData = { table_number: number, capacity: capacity };
            this.errorMessage = '';
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.errorMessage = '';
        },

        async submitForm() {
            this.errorMessage = '';
            
            try {
                const url = this.editMode 
                    ? `/admin/tables/${this.editId}`
                    : '/admin/tables';
                
                const method = this.editMode ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                } else {
                    this.errorMessage = data.message || 'An error occurred';
                }
            } catch (error) {
                this.errorMessage = 'Failed to save table. Please try again.';
            }
        },

        async deleteTable(id, number) {
            if (!confirm(`Are you sure you want to delete Table ${number}?`)) {
                return;
            }

            try {
                const response = await fetch(`/admin/tables/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert('Failed to delete table. Please try again.');
            }
        },

        async updateStatus(id, status) {
            try {
                const response = await fetch(`/admin/tables/${id}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: status })
                });

                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert('Failed to update status. Please try again.');
            }
        },

        filterTables() {
            const cards = document.querySelectorAll('.table-card');
            
            cards.forEach(card => {
                const status = card.dataset.status;
                const number = card.dataset.number.toLowerCase();
                const searchMatch = number.includes(this.searchQuery.toLowerCase());
                const statusMatch = this.statusFilter === 'all' || status === this.statusFilter;
                
                if (searchMatch && statusMatch) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    }
}

// Download QR Code
function downloadQR(tableId, tableNumber) {
    const qrElement = document.getElementById('qr-' + tableId);
    
    html2canvas(qrElement, {
        backgroundColor: '#ffffff',
        scale: 2
    }).then(canvas => {
        const link = document.createElement('a');
        link.download = `table-${tableNumber}-qr.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
    });
}

// Print QR Code
function printQR(tableId) {
    const qrElement = document.getElementById('qr-' + tableId);
    const printWindow = window.open('', '', 'width=600,height=600');
    
    printWindow.document.write('<html><head><title>Print QR Code</title>');
    printWindow.document.write('<style>body { display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; } img { max-width: 400px; }</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(qrElement.innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 250);
}

// Print All QR Codes
function printAllQR() {
    const printWindow = window.open('', '', 'width=800,height=600');
    
    printWindow.document.write('<html><head><title>All Table QR Codes</title>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; padding: 20px; }');
    printWindow.document.write('.qr-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }');
    printWindow.document.write('.qr-item { text-align: center; page-break-inside: avoid; }');
    printWindow.document.write('.qr-item h3 { margin: 0 0 10px 0; font-size: 18px; }');
    printWindow.document.write('.qr-item svg { max-width: 200px; }');
    printWindow.document.write('@media print { .qr-grid { grid-template-columns: repeat(2, 1fr); } }');
    printWindow.document.write('</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h1 style="text-align: center; margin-bottom: 30px;">Cafe Aroma - Table QR Codes</h1>');
    printWindow.document.write('<div class="qr-grid">');
    
    // Get all QR containers from the DOM
    const qrContainers = document.querySelectorAll('[id^="qr-"]');
    qrContainers.forEach(container => {
        // Extract table ID from ID string "qr-{id}"
        const id = container.id.replace('qr-', '');
        // Find the table card to get the table number
        const card = container.closest('.table-card');
        const number = card ? card.dataset.number : 'Unknown';
        
        printWindow.document.write('<div class="qr-item">');
        printWindow.document.write('<h3>Table ' + number + '</h3>');
        printWindow.document.write(container.innerHTML);
        printWindow.document.write('</div>');
    });
    
    printWindow.document.write('</div>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    
    setTimeout(() => {
        printWindow.print();
    }, 500);
}
</script>
@endpush
@endsection
