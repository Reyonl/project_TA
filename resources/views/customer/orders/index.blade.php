<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-slate-800 leading-tight font-outfit uppercase tracking-tight">
                🛍️ {{ __('Pesanan Saya') }}
            </h2>
            <a href="{{ route('customer.dashboard') }}" class="text-xs font-black text-sky-600 hover:text-sky-800 uppercase tracking-widest flex items-center gap-2 transition-transform hover:-translate-x-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 19l-7-7 7-7"></path></svg>
                Kembali ke Dashboard
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
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 p-20 text-center relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-sky-50 rounded-full blur-3xl"></div>
                    <div class="relative z-10">
                        <div class="text-6xl mb-6">📦</div>
                        <h3 class="text-2xl font-black text-slate-800 mb-2 font-outfit uppercase tracking-tight">Belum Ada Pesanan</h3>
                        <p class="text-slate-500 mb-8 font-medium italic">Ayo mulai desain pakaian sablon impianmu sekarang!</p>
                        <a href="{{ url('/') }}#katalog" class="inline-flex items-center gap-3 bg-sky-600 hover:bg-sky-500 text-white font-black px-8 py-4 rounded-2xl shadow-xl shadow-sky-100 transition transform hover:-translate-y-1">
                            MULAI DESAIN
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                    </div>
                </div>
            @else
                <div class="space-y-5">
                    @foreach($orders as $order)
                        @php
                            $statusConfig = [
                                'pending'    => ['label' => 'Menunggu Konfirmasi', 'color' => 'bg-amber-100 text-amber-700 border-amber-200',  'icon' => '⏳'],
                                'diproses'   => ['label' => 'Sedang Diproses',     'color' => 'bg-sky-100 text-sky-700 border-sky-200',     'icon' => '⚙️'],
                                'dikirim'    => ['label' => 'Dikirim',             'color' => 'bg-violet-100 text-violet-700 border-violet-200', 'icon' => '🚚'],
                                'selesai'    => ['label' => 'Selesai',             'color' => 'bg-emerald-100 text-emerald-700 border-emerald-200',   'icon' => '✅'],
                                'dibatalkan' => ['label' => 'Dibatalkan',          'color' => 'bg-rose-100 text-rose-700 border-rose-200',       'icon' => '❌'],
                            ];
                            $status = $statusConfig[$order->status_order] ?? ['label' => ucfirst($order->status_order), 'color' => 'bg-slate-100 text-slate-700 border-slate-200', 'icon' => '📋'];
                        @endphp
 
                        <a href="{{ route('customer.orders.show', $order->id_order) }}" class="group block bg-white rounded-[1.5rem] shadow-sm border border-slate-100 hover:border-sky-300 hover:shadow-2xl hover:shadow-sky-100 transition-all duration-300">
                            <div class="p-6 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-6">
                                    <div class="w-14 h-14 bg-sky-50 rounded-2xl flex items-center justify-center text-3xl shrink-0 group-hover:scale-110 transition-transform">
                                        {{ $status['icon'] }}
                                    </div>
                                    <div>
                                        <p class="font-black text-slate-800 text-lg font-outfit uppercase tracking-tight">Order #{{ str_pad($order->id_order, 5, '0', STR_PAD_LEFT) }}</p>
                                        <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-widest">{{ \Carbon\Carbon::parse($order->tanggal_order)->translatedFormat('d F Y, H:i') }} WIB</p>
                                    </div>
                                </div>
 
                                <div class="flex items-center gap-6 shrink-0">
                                    <span class="hidden sm:inline-block text-lg font-black text-slate-900 font-outfit">
                                        Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                    </span>
                                    <span class="inline-flex items-center gap-2 text-[10px] font-black px-4 py-2 rounded-xl border {{ $status['color'] }} uppercase tracking-widest shadow-sm">
                                        {{ $status['label'] }}
                                    </span>
                                    <svg class="w-5 h-5 text-slate-300 group-hover:text-sky-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
