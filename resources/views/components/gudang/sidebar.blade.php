
@props(['active' => ''])

<div class="w-64 bg-emerald-800 shadow-lg relative">
    <!-- Logo/Brand -->
    <div class="p-6 border-b border-emerald-700">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-emerald-600 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-white font-semibold">Bengkel Inventory</h3>
                <p class="text-emerald-400 text-sm">Gudang Panel</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="mt-6 pb-20">
        <div class="px-3">
            <!-- Dashboard -->
            <a href="/dashboard-gudang" 
               class="flex items-center px-3 py-2 rounded-lg transition-colors duration-200 mb-1 {{ $active === 'dashboard' ? 'text-white bg-emerald-700' : 'text-emerald-300 hover:text-white hover:bg-emerald-700' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                </svg>
                Dashboard
            </a>

            <!-- Monitoring Stock -->
            <a href="{{ route('gudang.monitoring-stock') }}" 
               class="flex items-center px-3 py-2 rounded-lg transition-colors duration-200 mb-1 {{ $active === 'monitoring' ? 'text-white bg-emerald-700' : 'text-emerald-300 hover:text-white hover:bg-emerald-700' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Monitoring Stock
                <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">EOQ</span>
            </a>

            <!-- Request Restock -->
            <a href="{{ route('gudang.restock-requests') }}" 
               class="flex items-center px-3 py-2 rounded-lg transition-colors duration-200 mb-1 {{ $active === 'requests' ? 'text-white bg-emerald-700' : 'text-emerald-300 hover:text-white hover:bg-emerald-700' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Request Restock
                @if(isset($pendingCount) && $pendingCount > 0)
                <span class="ml-auto bg-orange-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingCount }}</span>
                @endif
            </a>

            <!-- Verifikasi Permintaan -->
            <a href="{{ route('gudang.verifikasi-permintaan') }}" 
               class="flex items-center px-3 py-2 rounded-lg transition-colors duration-200 mb-1 {{ $active === 'verification' ? 'text-white bg-emerald-700' : 'text-emerald-300 hover:text-white hover:bg-emerald-700' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Verifikasi Permintaan
            </a>

            <!-- Kelola Data Barang -->
            <a href="{{ route('gudang.kelola-data-barang') }}" 
               class="flex items-center px-3 py-2 rounded-lg transition-colors duration-200 mb-1 {{ $active === 'data' ? 'text-white bg-emerald-700' : 'text-emerald-300 hover:text-white hover:bg-emerald-700' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Kelola Data Barang
            </a>

            <!-- Barang Masuk -->
            <a href="{{ route('gudang.barang-masuk') }}" 
               class="flex items-center px-3 py-2 rounded-lg transition-colors duration-200 mb-1 {{ $active === 'incoming' ? 'text-white bg-emerald-700' : 'text-emerald-300 hover:text-white hover:bg-emerald-700' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Barang Masuk
            </a>
        </div>
    </nav>

    <!-- User Info & Logout -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-emerald-700 bg-emerald-800">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-emerald-600 rounded-full flex items-center justify-center">
                    <span class="text-white text-sm font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
                <div class="ml-3">
                    <p class="text-white text-sm font-semibold">{{ Auth::user()->name }}</p>
                    <p class="text-emerald-400 text-xs">Gudang</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-emerald-400 hover:text-white transition-colors duration-200" title="Logout">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>