@php
    if (Auth::guard('admin')->check()) {
        $user   = Auth::guard('admin')->user();
        $name   = $user->nama_admin;
        $role   = $user->role; // 'admin' atau 'owner'
        $dashboardRoute = route('admin.dashboard');
    } elseif (Auth::guard('customer')->check()) {
        $user   = Auth::guard('customer')->user();
        $name   = $user->nama_customer;
        $role   = 'customer';
        $dashboardRoute = route('customer.dashboard');
    } else {
        $user   = null;
        $name   = 'Guest';
        $role   = 'guest';
        $dashboardRoute = url('/');
    }
    // Semua guard menggunakan satu route logout
    $logoutRoute = route('logout');
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-slate-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ $dashboardRoute }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-indigo-600" />
                    </a>
                </div>

                <!-- Desktop Navigation Links (sesuai role) -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-8 sm:flex items-center">

                    <x-nav-link :href="$dashboardRoute" :active="request()->routeIs('*.dashboard')">
                        🏠 Dashboard
                    </x-nav-link>

                    @if($role === 'admin')
                        <x-nav-link :href="route('admin.templates.index')" :active="request()->routeIs('admin.templates.*')">
                            🎨 Template
                        </x-nav-link>
                        <x-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                            📦 Kelola Order
                        </x-nav-link>
                    @endif

                    @if($role === 'owner')
                        <x-nav-link :href="route('admin.report.index')" :active="request()->routeIs('admin.report.*')">
                            📊 Laporan
                        </x-nav-link>
                    @endif

                    @if($role === 'customer')
                        <x-nav-link :href="route('customer.orders.index')" :active="request()->routeIs('customer.orders.*')">
                            🛍️ Pesanan Saya
                        </x-nav-link>
                        <x-nav-link :href="route('customer.cart.index')" :active="request()->routeIs('customer.cart.*')">
                            🛒 Keranjang
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-transparent text-sm leading-4 font-bold rounded-lg text-slate-600 bg-white hover:bg-slate-50 hover:text-indigo-600 focus:outline-none transition ease-in-out duration-150">
                            <!-- Role badge -->
                            @if($role === 'admin')
                                <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 text-[10px] font-bold rounded uppercase">Admin</span>
                            @elseif($role === 'owner')
                                <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold rounded uppercase">Owner</span>
                            @elseif($role === 'customer')
                                <span class="px-1.5 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded uppercase">Customer</span>
                            @endif
                            <div>{{ $name }}</div>
                            <svg class="fill-current h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        {{-- Header info user --}}
                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-xs text-slate-500">Masuk sebagai</p>
                            <p class="text-sm font-bold text-slate-800 truncate">{{ $name }}</p>
                            @if($user)
                            <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
                            @endif
                        </div>

                        {{-- Menu Admin --}}
                        @if($role === 'admin')
                            <x-dropdown-link :href="route('admin.dashboard')">
                                🏠 Dashboard Admin
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.templates.index')">
                                🎨 Kelola Template
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.orders.index')">
                                📦 Kelola Pesanan
                            </x-dropdown-link>
                        @endif

                        {{-- Menu Owner --}}
                        @if($role === 'owner')
                            <x-dropdown-link :href="route('admin.dashboard')">
                                🏠 Dashboard Owner
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.report.index')">
                                📊 Laporan Penjualan
                            </x-dropdown-link>
                        @endif

                        {{-- Menu Customer --}}
                        @if($role === 'customer')
                            <x-dropdown-link :href="route('customer.dashboard')">
                                🏠 Dashboard
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('customer.orders.index')">
                                🛍️ Pesanan Saya
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('customer.cart.index')">
                                🛒 Keranjang
                            </x-dropdown-link>
                        @endif

                        {{-- Logout --}}
                        <div class="border-t border-slate-100 mt-1">
                            <form method="POST" action="{{ $logoutRoute }}">
                                @csrf
                                <x-dropdown-link :href="$logoutRoute"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="text-red-600 hover:text-red-700 hover:bg-red-50">
                                    🚪 Keluar
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none focus:bg-slate-100 focus:text-slate-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="$dashboardRoute" :active="request()->routeIs('*.dashboard')">
                🏠 Dashboard
            </x-responsive-nav-link>

            @if($role === 'admin')
                <x-responsive-nav-link :href="route('admin.templates.index')" :active="request()->routeIs('admin.templates.*')">
                    🎨 Kelola Template
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                    📦 Kelola Pesanan
                </x-responsive-nav-link>
            @endif

            @if($role === 'owner')
                <x-responsive-nav-link :href="route('admin.report.index')" :active="request()->routeIs('admin.report.*')">
                    📊 Laporan Penjualan
                </x-responsive-nav-link>
            @endif

            @if($role === 'customer')
                <x-responsive-nav-link :href="route('customer.orders.index')" :active="request()->routeIs('customer.orders.*')">
                    🛍️ Pesanan Saya
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('customer.cart.index')" :active="request()->routeIs('customer.cart.*')">
                    🛒 Keranjang
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive User Info & Logout -->
        <div class="pt-4 pb-1 border-t border-slate-200">
            <div class="px-4">
                <div class="flex items-center gap-2 mb-1">
                    @if($role === 'admin')
                        <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 text-[10px] font-bold rounded uppercase">Admin</span>
                    @elseif($role === 'owner')
                        <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold rounded uppercase">Owner</span>
                    @elseif($role === 'customer')
                        <span class="px-1.5 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded uppercase">Customer</span>
                    @endif
                    <div class="font-bold text-sm text-slate-800">{{ $name }}</div>
                </div>
                <div class="font-medium text-xs text-slate-500">{{ $user->email ?? '' }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <form method="POST" action="{{ $logoutRoute }}">
                    @csrf
                    <x-responsive-nav-link :href="$logoutRoute"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                        class="text-red-600">
                        🚪 Keluar
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
