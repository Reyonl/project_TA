@php
    // Deteksi guard yang aktif
    if (Auth::guard('admin')->check()) {
        $authUser = Auth::guard('admin')->user();
        $authName  = $authUser->nama_admin;
        $authEmail = $authUser->email;
        $authRole  = $authUser->role; // 'admin' atau 'owner'
    } elseif (Auth::guard('customer')->check()) {
        $authUser  = Auth::guard('customer')->user();
        $authName  = $authUser->nama_customer;
        $authEmail = $authUser->email;
        $authRole  = 'customer';
    } else {
        $authUser  = null;
        $authName  = 'Tamu';
        $authEmail = '';
        $authRole  = 'guest';
    }
@endphp

<flux:dropdown position="bottom" align="start">
    <flux:sidebar.profile
        :name="$authName"
        :initials="substr($authName, 0, 2)"
        icon:trailing="chevrons-up-down"
        data-test="sidebar-menu-button"
    />

    <flux:menu>
        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
            <flux:avatar
                :name="$authName"
                :initials="substr($authName, 0, 2)"
            />
            <div class="grid flex-1 text-start text-sm leading-tight">
                <flux:heading class="truncate flex items-center gap-2">
                    {{ $authName }}
                    @if($authRole === 'admin')
                        <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 text-[10px] font-bold rounded uppercase">Admin</span>
                    @elseif($authRole === 'owner')
                        <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold rounded uppercase">Owner</span>
                    @elseif($authRole === 'customer')
                        <span class="px-1.5 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded uppercase">Customer</span>
                    @endif
                </flux:heading>
                <flux:text class="truncate">{{ $authEmail }}</flux:text>
            </div>
        </div>
        <flux:menu.separator />
        
        <flux:menu.radio.group>
            {{-- Menu Admin --}}
            @if($authRole === 'admin')
                <flux:menu.item href="{{ route('admin.dashboard') }}" icon="home" wire:navigate>
                    Dashboard Admin
                </flux:menu.item>
                <flux:menu.item href="{{ route('admin.templates.index') }}" icon="photo" wire:navigate>
                    Kelola Template
                </flux:menu.item>
                <flux:menu.item href="{{ route('admin.orders.index') }}" icon="inbox" wire:navigate>
                    Kelola Pesanan
                </flux:menu.item>
            @endif

            {{-- Menu Owner --}}
            @if($authRole === 'owner')
                <flux:menu.item href="{{ route('admin.dashboard') }}" icon="home" wire:navigate>
                    Dashboard Owner
                </flux:menu.item>
                <flux:menu.item href="{{ route('admin.report.index') }}" icon="chart-bar" wire:navigate>
                    Laporan Penjualan
                </flux:menu.item>
            @endif

            {{-- Menu Customer --}}
            @if($authRole === 'customer')
                <flux:menu.item href="{{ route('customer.dashboard') }}" icon="home" wire:navigate>
                    Dashboard
                </flux:menu.item>
                <flux:menu.item href="{{ route('customer.orders.index') }}" icon="shopping-bag" wire:navigate>
                    Pesanan Saya
                </flux:menu.item>
            @endif

            <flux:menu.separator />

            <form method="POST" action="{{ route('logout') }}" class="w-full" id="logout-form">
                @csrf
                <flux:menu.item
                    as="button"
                    type="submit"
                    icon="arrow-right-start-on-rectangle"
                    class="w-full cursor-pointer text-red-600 hover:bg-red-50 hover:text-red-700"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    data-test="logout-button"
                >
                    {{ __('Keluar') }}
                </flux:menu.item>
            </form>
        </flux:menu.radio.group>
    </flux:menu>
</flux:dropdown>
