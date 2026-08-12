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

    $notifications = [];
    $unreadCount = 0;
    if ($role === 'customer' && $user) {
        $customerOrders = \App\Models\Order::where('id_customer', $user->id_customer)
            ->with(['orderDetails.produk', 'orderDetails.desain'])
            ->latest('updated_at')
            ->take(8)
            ->get();

        foreach ($customerOrders as $ord) {
            // Cek jika ada item yang memerlukan revisi desain
            $revisions = $ord->orderDetails->filter(fn($od) => $od->status_desain === 'revision_required');
            foreach ($revisions as $rev) {
                $notifications[] = [
                    'type' => 'revision',
                    'title' => 'Revisi Desain Diperlukan',
                    'message' => 'Pesanan #' . str_pad($ord->id_order, 5, '0', STR_PAD_LEFT) . ' (' . ($rev->produk->nama_produk ?? 'Produk') . '): ' . \Illuminate\Support\Str::limit($rev->catatan_admin ?: 'Perlu perbaikan', 45),
                    'url' => route('customer.orders.show', $ord->id_order),
                    'time' => $ord->updated_at->diffForHumans(),
                    'badge' => 'bg-rose-100 text-rose-700 border-rose-200',
                    'icon' => 'warning',
                    'actionable' => true
                ];
                $unreadCount++;
            }

            // Cek jika pesanan menunggu pembayaran
            if ($ord->payment_status === 'awaiting_payment') {
                $notifications[] = [
                    'type' => 'payment',
                    'title' => 'Desain Disetujui — Menunggu Pembayaran',
                    'message' => 'Pesanan #' . str_pad($ord->id_order, 5, '0', STR_PAD_LEFT) . ' siap dibayar. Silakan transfer & unggah bukti.',
                    'url' => route('customer.orders.show', $ord->id_order),
                    'time' => $ord->updated_at->diffForHumans(),
                    'badge' => 'bg-amber-100 text-amber-700 border-amber-200',
                    'icon' => 'credit-card',
                    'actionable' => true
                ];
                $unreadCount++;
            } elseif ($ord->payment_status === 'failed') {
                $notifications[] = [
                    'type' => 'payment_failed',
                    'title' => 'Bukti Pembayaran Ditolak',
                    'message' => 'Bukti bayar pesanan #' . str_pad($ord->id_order, 5, '0', STR_PAD_LEFT) . ' ditolak. Mohon unggah ulang.',
                    'url' => route('customer.orders.show', $ord->id_order),
                    'time' => $ord->updated_at->diffForHumans(),
                    'badge' => 'bg-rose-100 text-rose-700 border-rose-200',
                    'icon' => 'x-circle',
                    'actionable' => true
                ];
                $unreadCount++;
            } elseif ($ord->status_order === 'processing') {
                $notifications[] = [
                    'type' => 'processing',
                    'title' => 'Pesanan Sedang Diproduksi',
                    'message' => 'Pesanan #' . str_pad($ord->id_order, 5, '0', STR_PAD_LEFT) . ' sedang dalam proses sablon.',
                    'url' => route('customer.orders.show', $ord->id_order),
                    'time' => $ord->updated_at->diffForHumans(),
                    'badge' => 'bg-sky-100 text-sky-700 border-sky-200',
                    'icon' => 'cog',
                    'actionable' => false
                ];
            } elseif ($ord->status_order === 'completed') {
                $notifications[] = [
                    'type' => 'completed',
                    'title' => 'Pesanan Telah Selesai',
                    'message' => 'Pesanan #' . str_pad($ord->id_order, 5, '0', STR_PAD_LEFT) . ' selesai diproses.',
                    'url' => route('customer.orders.show', $ord->id_order),
                    'time' => $ord->updated_at->diffForHumans(),
                    'badge' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                    'icon' => 'check',
                    'actionable' => false
                ];
            }
        }
    }
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-slate-200 shadow-sm relative z-[70]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ $dashboardRoute }}" class="hover:opacity-80 transition duration-300">
                        <img src="{{ asset('images/logo-dailyco.png') }}" class="h-12 w-auto drop-shadow-sm" alt="DAILY.CO Logo">
                    </a>
                </div>
 
                <!-- Desktop Navigation Links (sesuai role) -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-8 sm:flex items-center">
 
                    <x-nav-link :href="$dashboardRoute" :active="request()->routeIs('*.dashboard')" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard</span>
                    </x-nav-link>
 
                    @if($role === 'admin')
                        <x-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span>Kelola Produk</span>
                        </x-nav-link>
                        <x-nav-link :href="route('admin.templates.index')" :active="request()->routeIs('admin.templates.*')" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                            <span>Template</span>
                        </x-nav-link>
                        <x-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            <span>Kelola Order</span>
                        </x-nav-link>
                    @endif
 
                    @if($role === 'owner' || $role === 'admin')
                        <x-nav-link :href="route('admin.report.index')" :active="request()->routeIs('admin.report.*')" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span>Laporan</span>
                        </x-nav-link>
                    @endif
 
                    @if($role === 'customer')
                        <x-nav-link :href="route('customer.orders.index')" :active="request()->routeIs('customer.orders.*')" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            <span>Pesanan Saya</span>
                        </x-nav-link>
                        <x-nav-link :href="route('customer.cart.index')" :active="request()->routeIs('customer.cart.*')" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>Keranjang</span>
                        </x-nav-link>
                    @endif
                </div>
            </div>
 
            <!-- User Dropdown & Notification Bell -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                @if($role === 'customer')
                    <!-- Customer Notification Bell (Desktop) -->
                    <div class="relative" x-data="{ notifOpen: false }">
                        <button @click="notifOpen = !notifOpen" 
                                class="relative p-2.5 bg-slate-50 hover:bg-red-50 text-slate-600 hover:text-red-600 rounded-xl border border-slate-200 transition shadow-sm focus:outline-none"
                                title="Notifikasi Pesanan">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 flex h-5 w-5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                    <span class="relative inline-flex items-center justify-center rounded-full h-5 w-5 bg-rose-600 text-[10px] font-black text-white shadow-sm">
                                        {{ $unreadCount }}
                                    </span>
                                </span>
                            @endif
                        </button>

                        <!-- Dropdown Panel -->
                        <div x-show="notifOpen" 
                             @click.outside="notifOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 z-[100] overflow-hidden"
                             style="display: none;">
                            
                            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-black text-slate-800 uppercase tracking-wider">Notifikasi</span>
                                    @if($unreadCount > 0)
                                        <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-black rounded-full">{{ $unreadCount }} Perlu Perhatian</span>
                                    @endif
                                </div>
                            </div>

                            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 custom-scrollbar">
                                @forelse($notifications as $notif)
                                    <a href="{{ $notif['url'] }}" @click="notifOpen = false" class="block p-3.5 hover:bg-slate-50 transition {{ $notif['actionable'] ? 'bg-rose-50/30' : '' }}">
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm shrink-0 border {{ $notif['badge'] }}">
                                                @if($notif['icon'] === 'warning')
                                                    ⚠️
                                                @elseif($notif['icon'] === 'credit-card')
                                                    💳
                                                @elseif($notif['icon'] === 'x-circle')
                                                    ❌
                                                @elseif($notif['icon'] === 'cog')
                                                    ⚙️
                                                @else
                                                    ✅
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-bold text-slate-900 truncate">{{ $notif['title'] }}</p>
                                                <p class="text-[11px] text-slate-600 mt-0.5 line-clamp-2 leading-relaxed">{{ $notif['message'] }}</p>
                                                <span class="text-[9px] font-medium text-slate-400 mt-1 block">{{ $notif['time'] }}</span>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="py-8 text-center text-slate-400">
                                        <div class="text-3xl mb-2">🔔</div>
                                        <p class="text-xs font-bold">Belum ada notifikasi baru</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Status pesanan Anda akan muncul di sini</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="p-2 border-t border-slate-100 bg-slate-50/50 text-center">
                                <a href="{{ route('customer.orders.index') }}" @click="notifOpen = false" class="text-xs font-bold text-red-600 hover:text-red-700 block py-1.5 rounded-lg hover:bg-red-50 transition">
                                    Lihat Semua Pesanan →
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-4 py-2 bg-slate-50 border border-slate-100 text-sm leading-4 font-bold rounded-xl text-slate-600 hover:bg-red-50 hover:text-red-600 hover:border-red-100 focus:outline-none transition ease-in-out duration-150 shadow-sm hover:shadow-md">
                            <!-- Role badge -->
                            @if($role === 'admin')
                                <span class="px-1.5 py-0.5 bg-red-100 text-red-700 text-[10px] font-bold rounded uppercase">Admin</span>
                            @elseif($role === 'owner')
                                <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold rounded uppercase">Owner</span>
                            @elseif($role === 'customer')
                                <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded uppercase">Customer</span>
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
                            <x-dropdown-link :href="route('admin.dashboard')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span>Dashboard Admin</span>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.products.index')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <span>Kelola Produk</span>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.templates.index')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                                <span>Kelola Template</span>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.orders.index')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                <span>Kelola Pesanan</span>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.report.index')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <span>Laporan Penjualan</span>
                            </x-dropdown-link>
                        @endif

                        {{-- Menu Owner --}}
                        @if($role === 'owner')
                            <x-dropdown-link :href="route('admin.dashboard')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span>Dashboard Owner</span>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.report.index')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <span>Laporan Penjualan</span>
                            </x-dropdown-link>
                        @endif

                        {{-- Menu Customer --}}
                        @if($role === 'customer')
                            <x-dropdown-link :href="route('customer.dashboard')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span>Dashboard</span>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('customer.orders.index')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                <span>Pesanan Saya</span>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('customer.cart.index')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Keranjang</span>
                            </x-dropdown-link>
                        @endif

                        {{-- Logout --}}
                        <div class="border-t border-slate-100 mt-1">
                            <form method="POST" action="{{ $logoutRoute }}">
                                @csrf
                                <x-dropdown-link :href="$logoutRoute"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="text-red-600 hover:text-red-700 hover:bg-red-50 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    <span>Keluar</span>
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger & Mobile Notif -->
            <div class="-me-2 flex items-center gap-1 sm:hidden">
                @if($role === 'customer')
                    <!-- Mobile Notif Bell -->
                    <div class="relative" x-data="{ notifMobileOpen: false }">
                        <button @click="notifMobileOpen = !notifMobileOpen" class="relative p-2 text-slate-600 hover:text-red-600 hover:bg-slate-100 rounded-lg focus:outline-none transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            @if($unreadCount > 0)
                                <span class="absolute top-1 right-1 flex h-4 w-4">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                    <span class="relative inline-flex items-center justify-center rounded-full h-4 w-4 bg-rose-600 text-[9px] font-black text-white">
                                        {{ $unreadCount }}
                                    </span>
                                </span>
                            @endif
                        </button>

                        <!-- Mobile Notif Dropdown -->
                        <div x-show="notifMobileOpen" 
                             @click.outside="notifMobileOpen = false"
                             x-transition
                             class="fixed inset-x-4 top-16 bg-white rounded-2xl shadow-2xl border border-slate-200 py-2 z-[100] overflow-hidden"
                             style="display: none;">
                            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                                <span class="text-xs font-black text-slate-800 uppercase tracking-wider">Notifikasi Pesanan</span>
                                @if($unreadCount > 0)
                                    <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-black rounded-full">{{ $unreadCount }} Perlu Perhatian</span>
                                @endif
                            </div>
                            <div class="max-h-64 overflow-y-auto divide-y divide-slate-100 custom-scrollbar">
                                @forelse($notifications as $notif)
                                    <a href="{{ $notif['url'] }}" @click="notifMobileOpen = false" class="block p-3 hover:bg-slate-50 transition {{ $notif['actionable'] ? 'bg-rose-50/40' : '' }}">
                                        <div class="flex items-start gap-2.5">
                                            <span class="text-base shrink-0">
                                                @if($notif['icon'] === 'warning') ⚠️ @elseif($notif['icon'] === 'credit-card') 💳 @elseif($notif['icon'] === 'x-circle') ❌ @elseif($notif['icon'] === 'cog') ⚙️ @else ✅ @endif
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-bold text-slate-900">{{ $notif['title'] }}</p>
                                                <p class="text-[11px] text-slate-600 mt-0.5 line-clamp-2 leading-relaxed">{{ $notif['message'] }}</p>
                                                <span class="text-[9px] font-medium text-slate-400 mt-1 block">{{ $notif['time'] }}</span>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="py-6 text-center text-slate-400 text-xs">Belum ada notifikasi baru</div>
                                @endforelse
                            </div>
                            <div class="p-2 border-t border-slate-100 bg-slate-50 text-center">
                                <a href="{{ route('customer.orders.index') }}" @click="notifMobileOpen = false" class="text-xs font-bold text-red-600 block py-1">Lihat Semua Pesanan →</a>
                            </div>
                        </div>
                    </div>
                @endif

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
            <x-responsive-nav-link :href="$dashboardRoute" :active="request()->routeIs('*.dashboard')" class="flex items-center gap-2">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dashboard</span>
            </x-responsive-nav-link>

            @if($role === 'admin')
                <x-responsive-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')" class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span>Kelola Produk</span>
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.templates.index')" :active="request()->routeIs('admin.templates.*')" class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                    <span>Kelola Template</span>
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')" class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Kelola Pesanan</span>
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.report.index')" :active="request()->routeIs('admin.report.*')" class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Laporan Penjualan</span>
                </x-responsive-nav-link>
            @endif

            @if($role === 'owner')
                <x-responsive-nav-link :href="route('admin.report.index')" :active="request()->routeIs('admin.report.*')" class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Laporan Penjualan</span>
                </x-responsive-nav-link>
            @endif

            @if($role === 'customer')
                <x-responsive-nav-link :href="route('customer.orders.index')" :active="request()->routeIs('customer.orders.*')" class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Pesanan Saya</span>
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('customer.cart.index')" :active="request()->routeIs('customer.cart.*')" class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Keranjang</span>
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
                        class="text-red-600 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Keluar</span>
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
