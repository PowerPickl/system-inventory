<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Kelola Data User</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-slate-800 shadow-lg relative">
            <!-- Logo/Brand -->
            <div class="p-6 border-b border-slate-700">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-white font-semibold">Bengkel Inventory</h3>
                        <p class="text-slate-400 text-sm">Owner Panel</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 pb-20">
                <div class="px-3">
                    <!-- Dashboard -->
                    <a href="{{ route('owner.dashboard') }}" 
                       class="flex items-center px-3 py-2 text-slate-300 hover:text-white hover:bg-slate-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h2a2 2 0 012 2v10H8V5z"></path>
                        </svg>
                        Dashboard
                    </a>

                    <!-- Kelola Data User -->
                    <a href="{{ route('owner.kelola-user.index') }}" 
                       class="flex items-center px-3 py-2 text-white bg-slate-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m9 5.197v1M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Kelola Data User
                    </a>

                    <!-- Restock Approval -->
                    <a href="{{ route('owner.restock-approval.index') }}" 
                       class="flex items-center px-3 py-2 text-slate-300 hover:text-white hover:bg-slate-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Restock Approval
                        @php
                            $pendingCount = \App\Models\RestockRequest::where('status_request', 'Pending')->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingCount }}</span>
                        @endif
                    </a>

                    <!-- Laporan -->
                    <a href="{{ route('owner.simple-reports.index') }}" 
                       class="flex items-center px-3 py-2 text-slate-300 hover:text-white hover:bg-slate-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Laporan
                    </a>
                </div>
            </nav>

            <!-- User Info & Logout -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-700 bg-slate-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-white text-sm font-semibold">{{ Auth::user()->name }}</p>
                            <p class="text-slate-400 text-xs">Owner</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-white transition-colors duration-200" title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900">Kelola Data User</h1>
                            <p class="text-gray-600 text-sm">Manage users, roles, dan permissions</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Quick Stats -->
                            <div id="quick-stats" class="flex space-x-4 text-sm">
                                <div class="text-center">
                                    <div class="font-semibold text-blue-600" id="total-users">-</div>
                                    <div class="text-gray-500">Total Users</div>
                                </div>
                                <div class="text-center">
                                    <div class="font-semibold text-green-600" id="active-users">-</div>
                                    <div class="text-gray-500">Active</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6">
                <!-- Filter Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cari User</label>
                                <div class="relative">
                                    <input type="text" 
                                           id="search-input"
                                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                                           placeholder="Nama, email, atau phone...">
                                </div>
                            </div>

                            <!-- Role Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                                <select id="role-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Semua Role</option>
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select id="status-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Semua Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2 mt-4">
                            <button id="add-user-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah User
                            </button>
                            <button id="refresh-btn" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <!-- Results Header -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Daftar User</h3>
                            <div class="flex items-center space-x-4">
                                <!-- Results Info -->
                                <div id="results-info" class="text-sm text-gray-600">
                                    Menampilkan <span id="results-from">0</span>-<span id="results-to">0</span> dari <span id="results-total">0</span> user
                                </div>
                                
                              <!-- Per Page -->
                                <select id="per-page" class="px-3 py-1 border border-gray-300 rounded text-sm">
                                    <option value="15">15 per halaman</option>
                                    <option value="25">25 per halaman</option>
                                    <option value="50">50 per halaman</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div id="loading-state" class="hidden p-8 text-center">
                        <div class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-gray-600">Memuat data user...</span>
                        </div>
                    </div>

                    <!-- Results Table -->
                    <div id="results-container" class="overflow-x-auto">
                        <table class="w-full min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="results-tbody" class="bg-white divide-y divide-gray-200">
                                <!-- Dynamic content will be inserted here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div id="empty-state" class="hidden p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m9 5.197v1M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada user ditemukan</h3>
                        <p class="mt-1 text-sm text-gray-500">Coba ubah filter pencarian atau tambah user baru.</p>
                    </div>

                    <!-- Pagination -->
                    <div id="pagination-container" class="px-6 py-4 border-t border-gray-200">
                        <!-- Dynamic pagination will be inserted here -->
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add/Edit User Modal -->
    <div id="user-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="user-form">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                Tambah User Baru
                            </h3>
                            
                            <div class="space-y-4">
                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                                    <input type="text" name="name" id="user-name" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <span class="text-red-500 text-xs" id="error-name"></span>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                    <input type="email" name="email" id="user-email" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <span class="text-red-500 text-xs" id="error-email"></span>
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                                    <input type="text" name="phone" id="user-phone"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <span class="text-red-500 text-xs" id="error-phone"></span>
                                </div>

                                <!-- Role -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                                    <select name="role_id" id="user-role" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Pilih Role</option>
                                    </select>
                                    <span class="text-red-500 text-xs" id="error-role_id"></span>
                                </div>

                                <!-- Address -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                                    <textarea name="address" id="user-address" rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
                                    <span class="text-red-500 text-xs" id="error-address"></span>
                                </div>

                                <!-- Password -->
                                <div id="password-section">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                                    <input type="password" name="password" id="user-password"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <span class="text-red-500 text-xs" id="error-password"></span>
                                </div>

                                <!-- Password Confirmation -->
                                <div id="password-confirm-section">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password *</label>
                                    <input type="password" name="password_confirmation" id="user-password-confirmation"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_active" id="user-status" value="1" checked
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="user-status" class="ml-2 block text-sm text-gray-900">User Aktif</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" id="save-user-btn" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan
                        </button>
                        <button type="button" id="cancel-user-btn" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Detail User Modal -->
    <div id="detail-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="detail-modal-title">
                            Detail User
                        </h3>
                        <div id="detail-modal-content">
                            <!-- Dynamic content will be inserted here -->
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="close-detail-modal" 
                            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Kelola User JavaScript Implementation
        class KelolaUser {
            constructor() {
                this.currentPage = 1;
                this.perPage = 15;
                this.filters = {
                    search: '',
                    role_filter: '',
                    status_filter: '',
                    sort_by: 'created_at',
                    sort_order: 'desc'
                };
                this.editingUserId = null;

                this.init();
                this.loadInitialData();
            }

            init() {
                // Bind events
                document.getElementById('search-input').addEventListener('input', this.debounce(this.handleSearch.bind(this), 500));
                document.getElementById('role-filter').addEventListener('change', this.handleFilterChange.bind(this));
                document.getElementById('status-filter').addEventListener('change', this.handleFilterChange.bind(this));
                document.getElementById('per-page').addEventListener('change', this.handlePerPageChange.bind(this));
                
                document.getElementById('add-user-btn').addEventListener('click', this.showAddUserModal.bind(this));
                document.getElementById('refresh-btn').addEventListener('click', this.refreshData.bind(this));
                document.getElementById('cancel-user-btn').addEventListener('click', this.closeUserModal.bind(this));
                document.getElementById('close-detail-modal').addEventListener('click', this.closeDetailModal.bind(this));
                document.getElementById('user-form').addEventListener('submit', this.handleUserFormSubmit.bind(this));

                // Close modals on background click
                document.getElementById('user-modal').addEventListener('click', (e) => {
                    if (e.target.id === 'user-modal') {
                        this.closeUserModal();
                    }
                });

                document.getElementById('detail-modal').addEventListener('click', (e) => {
                    if (e.target.id === 'detail-modal') {
                        this.closeDetailModal();
                    }
                });
            }

            async loadInitialData() {
                await this.loadRoles();
                await this.loadStats();
                await this.loadUsers();
            }

            async loadRoles() {
                try {
                    const response = await fetch('/owner/kelola-user/roles');
                    const result = await response.json();
                    
                    if (result.success) {
                        // Populate role filter
                        const roleFilter = document.getElementById('role-filter');
                        roleFilter.innerHTML = '<option value="">Semua Role</option>';
                        
                        // Populate role in modal
                        const userRole = document.getElementById('user-role');
                        userRole.innerHTML = '<option value="">Pilih Role</option>';
                        
                        result.data.forEach(role => {
                            const option1 = document.createElement('option');
                            option1.value = role.id;
                            option1.textContent = role.nama_role;
                            roleFilter.appendChild(option1);

                            const option2 = document.createElement('option');
                            option2.value = role.id;
                            option2.textContent = role.nama_role;
                            userRole.appendChild(option2);
                        });
                    }
                } catch (error) {
                    console.error('Error loading roles:', error);
                }
            }

            async loadStats() {
                try {
                    const response = await fetch('/owner/kelola-user/stats');
                    const result = await response.json();
                    
                    if (result.success) {
                        document.getElementById('total-users').textContent = result.data.total_users;
                        document.getElementById('active-users').textContent = result.data.active_users;
                    }
                } catch (error) {
                    console.error('Error loading stats:', error);
                }
            }

            async loadUsers() {
                this.showLoading();
                
                try {
                    const params = new URLSearchParams({
                        ...this.filters,
                        page: this.currentPage,
                        per_page: this.perPage
                    });

                    const response = await fetch(`/owner/kelola-user/data?${params}`);
                    const result = await response.json();

                    if (result.success) {
                        this.renderResults(result.data);
                        this.renderPagination(result.pagination);
                        this.updateResultsInfo(result.pagination);
                    } else {
                        this.showError(result.message);
                    }
                } catch (error) {
                    console.error('Error loading users:', error);
                    this.showError('Terjadi kesalahan saat memuat data user');
                } finally {
                    this.hideLoading();
                }
            }

            renderResults(data) {
                const tbody = document.getElementById('results-tbody');
                const emptyState = document.getElementById('empty-state');
                const resultsContainer = document.getElementById('results-container');

                if (data.length === 0) {
                    resultsContainer.classList.add('hidden');
                    emptyState.classList.remove('hidden');
                    return;
                }

                resultsContainer.classList.remove('hidden');
                emptyState.classList.add('hidden');

                tbody.innerHTML = data.map(user => `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                    ${user.initials}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${user.name}</div>
                                    <div class="text-sm text-gray-500">${user.email}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">${user.phone}</div>
                            <div class="text-sm text-gray-500">${user.address.length > 30 ? user.address.substring(0, 30) + '...' : user.address}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="${user.role_badge_class}">
                                ${user.role_name}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="${user.status_badge_class}">
                                ${user.status_text}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">${user.created_at}</div>
                            <div class="text-sm text-gray-500">${user.created_at_diff}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium w-40">
                            <div class="flex flex-col space-y-1">
                                <button onclick="kelolaUser.showDetail(${user.id})" 
                                        class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs hover:bg-blue-200 transition-colors">
                                    Detail
                                </button>
                                <button onclick="kelolaUser.editUser(${user.id})" 
                                        class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs hover:bg-green-200 transition-colors">
                                    Edit
                                </button>
                                <button onclick="kelolaUser.toggleStatus(${user.id})" 
                                        class="px-2 py-1 ${user.is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200'} rounded text-xs transition-colors">
                                    ${user.is_active ? 'Nonaktifkan' : 'Aktifkan'}
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            }

            renderPagination(pagination) {
                const container = document.getElementById('pagination-container');
                
                if (pagination.last_page <= 1) {
                    container.innerHTML = '';
                    return;
                }

                let paginationHTML = '<div class="flex items-center justify-between">';
                
                // Previous button
                paginationHTML += `
                    <button onclick="kelolaUser.goToPage(${pagination.current_page - 1})" 
                            ${pagination.current_page <= 1 ? 'disabled' : ''}
                            class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        Previous
                    </button>
                `;

                // Page numbers
                paginationHTML += '<div class="flex space-x-1">';
                
                const startPage = Math.max(1, pagination.current_page - 2);
                const endPage = Math.min(pagination.last_page, pagination.current_page + 2);
                
                for (let i = startPage; i <= endPage; i++) {
                    paginationHTML += `
                        <button onclick="kelolaUser.goToPage(${i})" 
                                class="px-3 py-1 text-sm rounded ${i === pagination.current_page ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'}">
                            ${i}
                        </button>
                    `;
                }
                
                paginationHTML += '</div>';

                // Next button
                paginationHTML += `
                    <button onclick="kelolaUser.goToPage(${pagination.current_page + 1})" 
                            ${pagination.current_page >= pagination.last_page ? 'disabled' : ''}
                            class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                    </button>
                `;

                paginationHTML += '</div>';
                container.innerHTML = paginationHTML;
            }

            updateResultsInfo(pagination) {
                document.getElementById('results-from').textContent = pagination.from || 0;
                document.getElementById('results-to').textContent = pagination.to || 0;
                document.getElementById('results-total').textContent = pagination.total || 0;
            }

            // Modal Methods
            showAddUserModal() {
                this.editingUserId = null;
                document.getElementById('modal-title').textContent = 'Tambah User Baru';
                document.getElementById('user-form').reset();
                document.getElementById('user-status').checked = true;
                
                // Show password fields for new user
                document.getElementById('password-section').style.display = 'block';
                document.getElementById('password-confirm-section').style.display = 'block';
                document.getElementById('user-password').required = true;
                
                this.clearErrors();
                document.getElementById('user-modal').classList.remove('hidden');
            }

            async editUser(id) {
                try {
                    const response = await fetch(`/owner/kelola-user/${id}`);
                    const result = await response.json();

                    if (result.success) {
                        this.editingUserId = id;
                        const user = result.data;
                        
                        document.getElementById('modal-title').textContent = 'Edit User';
                        document.getElementById('user-name').value = user.name;
                        document.getElementById('user-email').value = user.email;
                        document.getElementById('user-phone').value = user.phone || '';
                        document.getElementById('user-role').value = user.role_id;
                        document.getElementById('user-address').value = user.address || '';
                        document.getElementById('user-status').checked = user.is_active;
                        
                        // Hide password fields for edit
                        document.getElementById('password-section').style.display = 'none';
                        document.getElementById('password-confirm-section').style.display = 'none';
                        document.getElementById('user-password').required = false;
                        
                        this.clearErrors();
                        document.getElementById('user-modal').classList.remove('hidden');
                    } else {
                        alert('User tidak ditemukan');
                    }
                } catch (error) {
                    console.error('Error loading user:', error);
                    alert('Terjadi kesalahan saat memuat data user');
                }
            }

            async showDetail(id) {
                try {
                    const response = await fetch(`/owner/kelola-user/${id}`);
                    const result = await response.json();

                    if (result.success) {
                        const user = result.data;
                        document.getElementById('detail-modal-title').textContent = `Detail ${user.name}`;
                        document.getElementById('detail-modal-content').innerHTML = `
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                        <p class="text-sm text-gray-900">${user.name}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Email</label>
                                        <p class="text-sm text-gray-900">${user.email}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">No. Telepon</label>
                                        <p class="text-sm text-gray-900">${user.phone || '-'}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Role</label>
                                        <p class="text-sm text-gray-900">${user.role_name}</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Alamat</label>
                                    <p class="text-sm text-gray-900">${user.address || '-'}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Status</label>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                            ${user.is_active ? 'Active' : 'Inactive'}
                                        </span>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Bergabung</label>
                                        <p class="text-sm text-gray-900">${user.created_at}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        document.getElementById('detail-modal').classList.remove('hidden');
                    } else {
                        alert('User tidak ditemukan');
                    }
                } catch (error) {
                    console.error('Error showing detail:', error);
                    alert('Terjadi kesalahan saat mengambil detail user');
                }
            }

            closeUserModal() {
                document.getElementById('user-modal').classList.add('hidden');
                this.editingUserId = null;
                this.clearErrors();
            }

            closeDetailModal() {
                document.getElementById('detail-modal').classList.add('hidden');
            }

            // Form submission
            async handleUserFormSubmit(e) {
            e.preventDefault();
            
            // ✅ PASTIKAN INI ADA - BUAT FORMDATA DARI FORM ELEMENT
            const form = e.target; // atau document.getElementById('user-form')
            const formData = new FormData(form);
            
            // ✅ ATAU CARA MANUAL YANG LEBIH AMAN:
            const userData = {
                name: document.getElementById('user-name').value,
                email: document.getElementById('user-email').value,
                phone: document.getElementById('user-phone').value || null,
                role_id: document.getElementById('user-role').value,
                address: document.getElementById('user-address').value || null,
                is_active: document.getElementById('user-status').checked ? 1 : 0
            };

            // ✅ JIKA ADA PASSWORD, TAMBAHKAN
            const passwordField = document.getElementById('user-password');
            const passwordConfirmField = document.getElementById('user-password-confirmation');
            
            if (passwordField && passwordField.value) {
                userData.password = passwordField.value;
                userData.password_confirmation = passwordConfirmField.value;
            }

            console.log('Sending data:', userData); // Debug log

            try {
                const url = this.editingUserId 
                    ? `/owner/kelola-user/${this.editingUserId}`
                    : '/owner/kelola-user';
                
                const method = this.editingUserId ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(userData)
                });

                const result = await response.json();
                console.log('Response:', result); // Debug log

                if (result.success) {
                    alert(result.message);
                    this.closeUserModal();
                    this.loadUsers();
                    this.loadStats();
                } else {
                    if (result.errors) {
                        this.displayErrors(result.errors);
                    } else {
                        alert(result.message);
                    }
                }
            } catch (error) {
                console.error('Error saving user:', error);
                alert('Terjadi kesalahan saat menyimpan user');
            }
        }

            async toggleStatus(id) {
                if (!confirm('Apakah Anda yakin ingin mengubah status user ini?')) {
                    return;
                }

                try {
                    const response = await fetch(`/owner/kelola-user/${id}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert(result.message);
                        this.loadUsers();
                        this.loadStats();
                    } else {
                        alert(result.message);
                    }
                } catch (error) {
                    console.error('Error toggling status:', error);
                    alert('Terjadi kesalahan saat mengubah status user');
                }
            }

            // Helper methods
            displayErrors(errors) {
                this.clearErrors();
                for (const [field, messages] of Object.entries(errors)) {
                    const errorElement = document.getElementById(`error-${field}`);
                    if (errorElement) {
                        errorElement.textContent = messages[0];
                    }
                }
            }

            clearErrors() {
                const errorElements = document.querySelectorAll('[id^="error-"]');
                errorElements.forEach(element => {
                    element.textContent = '';
                });
            }

            goToPage(page) {
                this.currentPage = page;
                this.loadUsers();
            }

            handleSearch() {
                this.filters.search = document.getElementById('search-input').value;
                this.currentPage = 1;
                this.loadUsers();
            }

            handleFilterChange() {
                this.filters.role_filter = document.getElementById('role-filter').value;
                this.filters.status_filter = document.getElementById('status-filter').value;
                this.currentPage = 1;
                this.loadUsers();
            }

            handlePerPageChange() {
                this.perPage = parseInt(document.getElementById('per-page').value);
                this.currentPage = 1;
                this.loadUsers();
            }

            refreshData() {
                this.loadUsers();
                this.loadStats();
            }

            showLoading() {
                document.getElementById('loading-state').classList.remove('hidden');
                document.getElementById('results-container').classList.add('hidden');
                document.getElementById('empty-state').classList.add('hidden');
            }

            hideLoading() {
                document.getElementById('loading-state').classList.add('hidden');
            }

            showError(message) {
                alert(message);
            }

            debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            window.kelolaUser = new KelolaUser();
        });
    </script>
</body>
</html>