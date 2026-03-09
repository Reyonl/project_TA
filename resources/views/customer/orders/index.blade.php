<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                🛍️ {{ __('Pesanan Saya') }}
            </h2>
            <a href="{{ route('customer.dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
                ← Katalog Produk
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-300 text-green-800 rounded-xl px-5 py-4 flex items-start gap-3">
                    <span class="text-2xl">🎉</span>
                    <div>
                        <p class="font-bold text-green-900">Pesanan Berhasil Dibuat!</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if($orders->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-16 text-center">
                    <div class="text-6xl mb-4">📦</div>
                    <h3 class="text-xl font-semibold text-slate-800 mb-2">Belum Ada Pesanan</h3>
                    <p class="text-slate-500 mb-6">Ayo mulai desain pakaian sablon kamu yang pertama!</p>
                    <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-xl transition">
                        Lihat Katalog
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($orders as $order)
                        @php
                            $statusConfig = [
                                'pending'    => ['label' => 'Menunggu Konfirmasi', 'color' => 'bg-yellow-100 text-yellow-800',  'icon' => '⏳'],
                                'diproses'   => ['label' => 'Sedang Diproses',     'color' => 'bg-blue-100 text-blue-800',     'icon' => '⚙️'],
                                'dikirim'    => ['label' => 'Dikirim',             'color' => 'bg-purple-100 text-purple-800', 'icon' => '🚚'],
                                'selesai'    => ['label' => 'Selesai',             'color' => 'bg-green-100 text-green-800',   'icon' => '✅'],
                                'dibatalkan' => ['label' => 'Dibatalkan',          'color' => 'bg-red-100 text-red-800',       'icon' => '❌'],
                            ];
                            $status = $statusConfig[$order->status_order] ?? ['label' => ucfirst($order->status_order), 'color' => 'bg-slate-100 text-slate-700', 'icon' => '📋'];
                        @endphp

                        <a href="{{ route('customer.orders.show', $order->id_order) }}" class="block bg-white rounded-2xl shadow-sm border border-slate-100 hover:border-indigo-300 hover:shadow-md transition-all duration-200">
                            <div class="p-5 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-2xl shrink-0">
                                        {{ $status['icon'] }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">Order #{{ str_pad($order->id_order, 5, '0', STR_PAD_LEFT) }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ \Carbon\Carbon::parse($order->tanggal_order)->translatedFormat('d F Y, H:i') }} WIB</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 shrink-0">
                                    <span class="hidden sm:inline-block text-sm font-bold text-slate-800">
                                        Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full {{ $status['color'] }}">
                                        {{ $status['icon'] }} {{ $status['label'] }}
                                    </span>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
