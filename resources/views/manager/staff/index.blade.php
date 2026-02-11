@extends('layouts.dashboard')

@section('title', 'Staff Monitoring')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#181411] dark:text-white">Staff Monitoring</h1>
            <p class="text-sm text-[#897561] dark:text-[#a89c92] mt-1">Monitor cashier and manager activities</p>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-[#2d2115] rounded-xl p-4 border border-[#f4f2f0] dark:border-[#3e2d23]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[#897561] dark:text-[#a89c92]">Total Staff</p>
                    <p class="text-2xl font-bold text-[#181411] dark:text-white mt-1">{{ $staff->count() }}</p>
                </div>
                <div class="size-12 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">group</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#2d2115] rounded-xl p-4 border border-[#f4f2f0] dark:border-[#3e2d23]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[#897561] dark:text-[#a89c92]">Managers</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">{{ $staff->where('role', 'manager')->count() }}</p>
                </div>
                <div class="size-12 rounded-lg bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">admin_panel_settings</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#2d2115] rounded-xl p-4 border border-[#f4f2f0] dark:border-[#3e2d23]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[#897561] dark:text-[#a89c92]">Cashiers</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $staff->where('role', 'cashier')->count() }}</p>
                </div>
                <div class="size-12 rounded-lg bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600 dark:text-green-400">point_of_sale</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#2d2115] rounded-xl p-4 border border-[#f4f2f0] dark:border-[#3e2d23]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[#897561] dark:text-[#a89c92]">Active Today</p>
                    <p class="text-2xl font-bold text-primary mt-1">{{ $staff->count() }}</p>
                </div>
                <div class="size-12 rounded-lg bg-primary/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary">schedule</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff List -->
    <div class="bg-white dark:bg-[#2d2115] rounded-xl border border-[#f4f2f0] dark:border-[#3e2d23] overflow-hidden">
        <!-- Table Header -->
        <div class="p-4 border-b border-[#f4f2f0] dark:border-[#3e2d23]">
            <h2 class="text-lg font-bold text-[#181411] dark:text-white">Staff Members</h2>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-[#fdfbf7] dark:bg-[#221910] border-b border-[#f4f2f0] dark:border-[#3e2d23]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#897561] dark:text-[#a89c92] uppercase tracking-wider">Staff</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#897561] dark:text-[#a89c92] uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#897561] dark:text-[#a89c92] uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#897561] dark:text-[#a89c92] uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#897561] dark:text-[#a89c92] uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-[#897561] dark:text-[#a89c92] uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f4f2f0] dark:divide-[#3e2d23]">
                    @foreach($staff as $member)
                    <tr class="hover:bg-[#fdfbf7] dark:hover:bg-[#221910] transition-colors">
                        <!-- Staff Info -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="size-10 rounded-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-[#181411] dark:text-white">{{ $member->name }}</div>
                                    <div class="text-xs text-[#897561] dark:text-[#a89c92]">ID: {{ $member->id }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Role -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($member->role === 'manager') bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-400
                                @elseif($member->role === 'cashier') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400
                                @else bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-400
                                @endif">
                                {{ ucfirst($member->role) }}
                            </span>
                        </td>

                        <!-- Email -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-[#181411] dark:text-white">{{ $member->email }}</div>
                        </td>

                        <!-- Joined Date -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-[#897561] dark:text-[#a89c92]">{{ $member->created_at->format('M d, Y') }}</div>
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                                Active
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="viewStaffDetails({{ $member->id }}, '{{ $member->name }}', '{{ $member->email }}', '{{ $member->role }}')" 
                                        class="p-2 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="View Details">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                                <button onclick="editStaff({{ $member->id }}, '{{ $member->name }}', '{{ $member->email }}', '{{ $member->role }}')" 
                                        class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Edit">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </button>
                                <button onclick="deleteStaff({{ $member->id }}, '{{ $member->name }}')" 
                                        class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Delete">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        @if($staff->isEmpty())
        <div class="p-12 text-center">
            <div class="size-16 mx-auto rounded-full bg-[#f4f2f0] dark:bg-[#221910] flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[#897561] dark:text-[#a89c92] text-[32px]">group_off</span>
            </div>
            <h3 class="text-lg font-medium text-[#181411] dark:text-white mb-2">No Staff Members</h3>
            <p class="text-sm text-[#897561] dark:text-[#a89c92]">No staff members to display at this time</p>
        </div>
        @endif
    </div>

    <!-- Recent Activity -->
    <div class="bg-white dark:bg-[#2d2115] rounded-xl border border-[#f4f2f0] dark:border-[#3e2d23] overflow-hidden">
        <div class="p-4 border-b border-[#f4f2f0] dark:border-[#3e2d23]">
            <h2 class="text-lg font-bold text-[#181411] dark:text-white">Recent Activity</h2>
        </div>
        
        <div class="p-6">
            <div class="space-y-4">
                @foreach($staff->take(5) as $member)
                <div class="flex items-center gap-4 p-4 bg-[#fdfbf7] dark:bg-[#221910] rounded-lg hover:bg-[#f4f2f0] dark:hover:bg-[#2c241b] transition-colors">
                    <div class="size-10 rounded-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center text-white font-bold flex-shrink-0">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-[#181411] dark:text-white truncate">{{ $member->name }}</p>
                        <p class="text-xs text-[#897561] dark:text-[#a89c92]">Last active: {{ $member->updated_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-[20px]">check_circle</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Staff Detail Modal -->
<div id="staffDetailModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#2d2115] rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b border-[#f4f2f0] dark:border-[#3e2d23] flex items-center justify-between">
            <h2 class="text-lg font-bold text-[#181411] dark:text-white">Staff Details</h2>
            <button onclick="closeModal('staffDetailModal')" class="text-[#897561] hover:text-[#181411] dark:text-[#a89c92] dark:hover:text-white">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="text-sm font-medium text-[#897561] dark:text-[#a89c92]">Name</label>
                <p class="text-lg font-semibold text-[#181411] dark:text-white mt-1" id="detailName"></p>
            </div>
            <div>
                <label class="text-sm font-medium text-[#897561] dark:text-[#a89c92]">Email</label>
                <p class="text-base text-[#181411] dark:text-white mt-1" id="detailEmail"></p>
            </div>
            <div>
                <label class="text-sm font-medium text-[#897561] dark:text-[#a89c92]">Password</label>
                <div class="flex items-center gap-2 mt-1">
                    <input type="password" id="detailPassword" class="flex-1 px-3 py-2 bg-[#f4f2f0] dark:bg-[#3e2d23] text-[#181411] dark:text-white rounded-lg text-sm" readonly>
                    <button onclick="togglePasswordVisibility()" class="p-2 text-[#897561] hover:text-[#181411] dark:text-[#a89c92] dark:hover:text-white">
                        <span class="material-symbols-outlined text-[20px]" id="passwordToggleIcon">visibility_off</span>
                    </button>
                </div>
            </div>
            <div>
                <label class="text-sm font-medium text-[#897561] dark:text-[#a89c92]">Role</label>
                <p class="text-lg font-semibold text-[#181411] dark:text-white mt-1" id="detailRole"></p>
            </div>
        </div>
        <div class="p-6 border-t border-[#f4f2f0] dark:border-[#3e2d23] flex gap-3">
            <button onclick="closeModal('staffDetailModal')" class="flex-1 px-4 py-2 bg-[#f4f2f0] dark:bg-[#3e2d23] text-[#181411] dark:text-white rounded-lg hover:bg-[#e8e6e3] dark:hover:bg-[#4a3a2e] transition-colors font-medium">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#2d2115] rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b border-[#f4f2f0] dark:border-[#3e2d23]">
            <h2 class="text-lg font-bold text-[#181411] dark:text-white">Delete Staff Member</h2>
        </div>
        <div class="p-6">
            <p class="text-[#897561] dark:text-[#a89c92]">Are you sure you want to delete <strong id="deleteStaffName" class="text-[#181411] dark:text-white"></strong>? This action cannot be undone.</p>
        </div>
        <div class="p-6 border-t border-[#f4f2f0] dark:border-[#3e2d23] flex gap-3">
            <button onclick="closeModal('deleteModal')" class="flex-1 px-4 py-2 bg-[#f4f2f0] dark:bg-[#3e2d23] text-[#181411] dark:text-white rounded-lg hover:bg-[#e8e6e3] dark:hover:bg-[#4a3a2e] transition-colors font-medium">
                Cancel
            </button>
            <button onclick="confirmDelete()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                Delete
            </button>
        </div>
    </div>
</div>

<!-- Edit Staff Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#2d2115] rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b border-[#f4f2f0] dark:border-[#3e2d23] flex items-center justify-between">
            <h2 class="text-lg font-bold text-[#181411] dark:text-white">Edit Staff Member</h2>
            <button onclick="closeModal('editModal')" class="text-[#897561] hover:text-[#181411] dark:text-[#a89c92] dark:hover:text-white">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="text-sm font-medium text-[#897561] dark:text-[#a89c92]">Name</label>
                <input type="text" id="editName" class="w-full mt-1 px-3 py-2 bg-[#f4f2f0] dark:bg-[#3e2d23] text-[#181411] dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="text-sm font-medium text-[#897561] dark:text-[#a89c92]">Email</label>
                <input type="email" id="editEmail" class="w-full mt-1 px-3 py-2 bg-[#f4f2f0] dark:bg-[#3e2d23] text-[#181411] dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="text-sm font-medium text-[#897561] dark:text-[#a89c92]">Password (leave blank to keep current)</label>
                <input type="password" id="editPassword" class="w-full mt-1 px-3 py-2 bg-[#f4f2f0] dark:bg-[#3e2d23] text-[#181411] dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Enter new password">
            </div>
            <div>
                <label class="text-sm font-medium text-[#897561] dark:text-[#a89c92]">Role</label>
                <select id="editRole" class="w-full mt-1 px-3 py-2 bg-[#f4f2f0] dark:bg-[#3e2d23] text-[#181411] dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="manager">Manager</option>
                    <option value="cashier">Cashier</option>
                </select>
            </div>
        </div>
        <div class="p-6 border-t border-[#f4f2f0] dark:border-[#3e2d23] flex gap-3">
            <button onclick="closeModal('editModal')" class="flex-1 px-4 py-2 bg-[#f4f2f0] dark:bg-[#3e2d23] text-[#181411] dark:text-white rounded-lg hover:bg-[#e8e6e3] dark:hover:bg-[#4a3a2e] transition-colors font-medium">
                Cancel
            </button>
            <button onclick="saveStaffChanges()" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition-colors font-medium">
                Save
            </button>
        </div>
    </div>
</div>

<script>
let deleteStaffId = null;
let editStaffId = null;
let passwordVisible = false;

function viewStaffDetails(id, name, email, role) {
    document.getElementById('detailName').textContent = name;
    document.getElementById('detailEmail').textContent = email;
    document.getElementById('detailPassword').value = '••••••••';
    document.getElementById('detailRole').textContent = role.charAt(0).toUpperCase() + role.slice(1);
    document.getElementById('staffDetailModal').classList.remove('hidden');
}

function togglePasswordVisibility() {
    const passwordField = document.getElementById('detailPassword');
    const toggleIcon = document.getElementById('passwordToggleIcon');
    
    if (passwordVisible) {
        passwordField.type = 'password';
        passwordField.value = '••••••••';
        toggleIcon.textContent = 'visibility_off';
        passwordVisible = false;
    } else {
        passwordField.type = 'text';
        passwordField.value = '(Password stored securely)';
        toggleIcon.textContent = 'visibility';
        passwordVisible = true;
    }
}

function editStaff(id, name, email, role) {
    editStaffId = id;
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editPassword').value = '';
    document.getElementById('editRole').value = role;
    document.getElementById('editModal').classList.remove('hidden');
}

function saveStaffChanges() {
    if (!editStaffId) return;
    
    const name = document.getElementById('editName').value.trim();
    const email = document.getElementById('editEmail').value.trim();
    const password = document.getElementById('editPassword').value.trim();
    const role = document.getElementById('editRole').value;
    
    if (!name || !email) {
        alert('Please fill in all required fields');
        return;
    }
    
    const data = {
        name: name,
        email: email,
        role: role
    };
    
    if (password) {
        data.password = password;
    }
    
    fetch(`/manager/staff/${editStaffId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    }).then(response => {
        if (response.ok) {
            alert('Staff member updated successfully');
            window.location.reload();
        } else {
            alert('Error updating staff member');
        }
    }).catch(error => {
        console.error('Error:', error);
        alert('Error updating staff member');
    });
    
    closeModal('editModal');
}

function deleteStaff(id, name) {
    deleteStaffId = id;
    document.getElementById('deleteStaffName').textContent = name;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function confirmDelete() {
    if (deleteStaffId) {
        fetch(`/manager/staff/${deleteStaffId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                alert('Staff member deleted successfully');
                window.location.reload();
            } else {
                alert('Error deleting staff member');
            }
        }).catch(error => {
            console.error('Error:', error);
            alert('Error deleting staff member');
        });
        closeModal('deleteModal');
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const modals = ['staffDetailModal', 'deleteModal', 'editModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });
});
</script>
@endsection
