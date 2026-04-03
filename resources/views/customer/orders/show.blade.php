<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-slate-800 leading-tight font-outfit uppercase tracking-tight">
                Status Pesanan <span class="text-sky-600">#{{ str_pad($order->id_order, 5, '0', STR_PAD_LEFT) }}</span>
            </h2>
            <a href="{{ route('customer.orders.index') }}" class="text-xs font-black text-sky-600 hover:text-sky-800 uppercase tracking-widest flex items-center gap-2 transition-transform hover:-translate-x-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 19l-7-7 7-7"></path></svg>
                Semua Pesanan
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @php
                $statusSteps = ['pending', 'diproses', 'dikirim', 'selesai'];
                $statusLabels = [
                    'pending'    => ['label' => 'Dikonfirmasi',  'icon' => '📋'],
                    'diproses'   => ['label' => 'Diproses',      'icon' => '⚙️'],
                    'dikirim'    => ['label' => 'Dikirim',       'icon' => '🚚'],
                    'selesai'    => ['label' => 'Selesai',       'icon' => '✅'],
                ];
                $currentStatus = $order->status_order;
                $currentIndex  = array_search($currentStatus, $statusSteps);
                $isCancelled   = $currentStatus === 'dibatalkan';
                   {{-- ===== Progress Bar Status ===== --}}
            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 p-8 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-sky-50 rounded-full blur-3xl opacity-50"></div>
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-8 relative z-10">Lacak Status Realtime</h3>
 
                @if($isCancelled)
                    <div class="flex items-center gap-4 bg-rose-50 border border-rose-100 rounded-2xl p-6 text-rose-700 relative z-10 animate-pulse">
                        <span class="text-3xl">❌</span>
                        <div>
                            <p class="font-black font-outfit uppercase tracking-tight">Pesanan Dibatalkan</p>
                            <p class="text-sm font-medium italic">Maaf, pesanan ini telah dibatalkan oleh sistem atau admin.</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center justify-between relative z-10">
                        @foreach($statusSteps as $i => $step)
                            @php
                                $isDone   = $currentIndex !== false && $i <= $currentIndex;
                                $isActive = $currentIndex !== false && $i === $currentIndex;
                                $cfg      = $statusLabels[$step];
                            @endphp
                            <div class="flex flex-col items-center flex-1 relative">
                                {{-- Connector line --}}
                                @if($i < count($statusSteps) - 1)
                                    <div class="absolute top-6 left-1/2 w-full h-[3px] {{ $isDone && $i < $currentIndex ? 'bg-sky-500' : 'bg-slate-100' }}"></div>
                                @endif
 
                                {{-- Circle --}}
                                <div class="relative z-10 w-12 h-12 rounded-2xl flex items-center justify-center text-xl font-bold transition-all duration-500
                                    {{ $isActive ? 'bg-sky-600 text-white shadow-xl shadow-sky-200 scale-110 ring-4 ring-sky-50' : ($isDone ? 'bg-sky-500 text-white' : 'bg-slate-100 text-slate-300') }}">
                                    {{ $isDone ? $cfg['icon'] : ($i + 1) }}
                                </div>
                                <span class="mt-4 text-[10px] font-black uppercase tracking-widest text-center transition-colors duration-500 {{ $isActive ? 'text-sky-600' : ($isDone ? 'text-slate-600' : 'text-slate-300') }}">
                                    {{ $cfg['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
         </div>

            {{-- ===== Info Order ===== --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Informasi Transaksi</h3>
                <dl class="grid grid-cols-2 gap-y-6 gap-x-8 text-sm">
                    <div>
                        <dt class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">ID Pesanan</dt>
                        <dd class="font-black text-slate-800 text-lg font-outfit uppercase tracking-tight">#{{ str_pad($order->id_order, 5, '0', STR_PAD_LEFT) }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tanggal Transaksi</dt>
                        <dd class="font-bold text-slate-700 font-outfit text-lg">{{ \Carbon\Carbon::parse($order->tanggal_order)->translatedFormat('d F Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Pembayaran</dt>
                        <dd class="font-black text-sky-600 text-2xl font-outfit italic tracking-tighter">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Status Saat Ini</dt>
                        <dd class="mt-1">
                            @php
                                $statusColors = [
                                    'pending'  => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'diproses' => 'bg-sky-100 text-sky-700 border-sky-200',
                                    'dikirim'  => 'bg-violet-100 text-violet-700 border-violet-200',
                                    'selesai'  => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'dibatalkan' => 'bg-rose-100 text-rose-700 border-rose-200',
                                ];
                                $colorClass = $statusColors[$order->status_order] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                            @endphp
                            <span class="px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-[0.1em] border {{ $colorClass }} shadow-sm">
                                {{ $order->status_order }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- ===== Item Pesanan ===== --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider mb-4">Item Pesanan</h3>
                <div class="space-y-4">
                    @foreach($order->orderDetails as $detail)
                        <div class="flex items-start gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                                <!-- DESAIN DEPAN -->
                                <div class="w-[80px] h-[100px] rounded-lg overflow-hidden bg-slate-50 border border-slate-200 shrink-0 flex items-center justify-center relative shadow-inner">
                                    <span class="absolute top-0.5 left-0.5 z-30 bg-white/80 text-[8px] font-bold px-1 rounded shadow-sm">Depan</span>
                                    @if($detail->desain && $detail->desain->file_desain)
                                        @if($detail->produk->jenis_produk == 'kaos')
                                            <img src="{{ asset('images/mockups/kaos.png?v='.time()) }}" class="absolute w-[85%] h-[85%] object-contain drop-shadow opacity-90 z-0">
                                            <div class="absolute w-[85%] h-[85%] mix-blend-multiply z-10"
                                                 style="-webkit-mask-image: url('{{ asset('images/mockups/kaos.png?v='.time()) }}'); -webkit-mask-size: contain; -webkit-mask-position: center; -webkit-mask-repeat: no-repeat; mask-image: url('{{ asset('images/mockups/kaos.png?v='.time()) }}'); mask-size: contain; mask-position: center; mask-repeat: no-repeat;">
                                                <div class="w-full h-full" style="background-color: {{ $detail->desain->warna_baju ?: '#ffffff' }};"></div>
                                            </div>
                                        @elseif($detail->produk->jenis_produk == 'hoodie')
                                            <img src="{{ asset('images/mockups/hoodie.png?v='.time()) }}" class="absolute w-[85%] h-[85%] object-contain drop-shadow opacity-90 z-0">
                                            <div class="absolute w-[85%] h-[85%] mix-blend-multiply z-10"
                                                 style="-webkit-mask-image: url('{{ asset('images/mockups/hoodie.png?v='.time()) }}'); -webkit-mask-size: contain; -webkit-mask-position: center; -webkit-mask-repeat: no-repeat; mask-image: url('{{ asset('images/mockups/hoodie.png?v='.time()) }}'); mask-size: contain; mask-position: center; mask-repeat: no-repeat;">
                                                <div class="w-full h-full" style="background-color: {{ $detail->desain->warna_baju ?: '#ffffff' }};"></div>
                                            </div>
                                        @endif
                                        
                                        <div class="absolute z-20" style="top: 20%; left: 27.08%; width: 45.83%; height: 53.33%;">
                                            <img src="{{ Str::startsWith($detail->desain->file_desain, 'data:image') ? $detail->desain->file_desain : asset('storage/' . $detail->desain->file_desain) }}" class="w-full h-full object-contain">
                                        </div>
                                    @else
                                        <span class="text-3xl">🎨</span>
                                    @endif
                                </div>

                                <!-- DESAIN BELAKANG -->
                                @if($detail->desain && $detail->desain->file_desain_belakang)
                                <div class="w-[80px] h-[100px] rounded-lg overflow-hidden bg-slate-50 border border-slate-200 shrink-0 flex items-center justify-center relative shadow-inner">
                                    <span class="absolute top-0.5 left-0.5 z-30 bg-white/80 text-[8px] font-bold px-1 rounded shadow-sm">Belakang</span>
                                    @if($detail->produk->jenis_produk == 'kaos')
                                        <img src="{{ asset('images/mockups/kaos_belakang.png?v='.time()) }}" class="absolute w-[85%] h-[85%] object-contain drop-shadow opacity-90 z-0">
                                        <div class="absolute w-[85%] h-[85%] mix-blend-multiply z-10"
                                             style="-webkit-mask-image: url('{{ asset('images/mockups/kaos_belakang.png?v='.time()) }}'); -webkit-mask-size: contain; -webkit-mask-position: center; -webkit-mask-repeat: no-repeat; mask-image: url('{{ asset('images/mockups/kaos_belakang.png?v='.time()) }}'); mask-size: contain; mask-position: center; mask-repeat: no-repeat;">
                                            <div class="w-full h-full" style="background-color: {{ $detail->desain->warna_baju ?: '#ffffff' }};"></div>
                                        </div>
                                    @elseif($detail->produk->jenis_produk == 'hoodie')
                                        <img src="{{ asset('images/mockups/hoodie_belakang.png?v='.time()) }}" class="absolute w-[85%] h-[85%] object-contain drop-shadow opacity-90 z-0">
                                        <div class="absolute w-[85%] h-[85%] mix-blend-multiply z-10"
                                             style="-webkit-mask-image: url('{{ asset('images/mockups/hoodie_belakang.png?v='.time()) }}'); -webkit-mask-size: contain; -webkit-mask-position: center; -webkit-mask-repeat: no-repeat; mask-image: url('{{ asset('images/mockups/hoodie_belakang.png?v='.time()) }}'); mask-size: contain; mask-position: center; mask-repeat: no-repeat;">
                                            <div class="w-full h-full" style="background-color: {{ $detail->desain->warna_baju ?: '#ffffff' }};"></div>
                                        </div>
                                    @endif
                                    
                                    <div class="absolute z-20" style="top: 20%; left: 27.08%; width: 45.83%; height: 53.33%;">
                                        <img src="{{ Str::startsWith($detail->desain->file_desain_belakang, 'data:image') ? $detail->desain->file_desain_belakang : asset('storage/' . $detail->desain->file_desain_belakang) }}" class="w-full h-full object-contain">
                                    </div>
                                </div>
                                @endif

                            <div class="flex-1">
                                <p class="font-bold text-slate-800">{{ $detail->produk->nama_produk ?? 'Produk' }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ ucfirst($detail->produk->jenis_produk ?? '') }}</p>
                                <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-600">
                                    <span>Jumlah: <strong>{{ $detail->quantity }} pcs</strong></span>
                                    <span>Harga Produk: <strong>Rp {{ number_format($detail->harga_produk, 0, ',', '.') }}</strong></span>
                                    <span>Harga Desain: <strong>Rp {{ number_format($detail->harga_desain, 0, ',', '.') }}</strong></span>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <p class="text-xs text-slate-500">Subtotal</p>
                                <p class="font-bold text-indigo-600 text-lg">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <!-- Status Revisi -->
                        @if($detail->status_desain == 'revisi')
                            <div class="mt-4 p-6 border border-rose-100 bg-rose-50 rounded-[2rem] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 relative overflow-hidden group">
                                <div class="absolute -right-5 -bottom-5 text-rose-200/30 group-hover:scale-110 transition-transform">
                                    <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                                </div>
                                <div class="flex-1 relative z-10">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-8 h-8 bg-rose-600 rounded-lg flex items-center justify-center text-white shadow-lg shadow-rose-200 animate-bounce">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        </div>
                                        <h5 class="font-black text-rose-900 font-outfit uppercase tracking-tight">Perlu Perbaikan Desain</h5>
                                    </div>
                                    <p class="text-sm text-rose-700/80 font-medium italic animate-pulse">Admin: "{{ $detail->catatan_admin }}"</p>
                                </div>
                                <a href="{{ route('customer.designs.editor', ['produk' => $detail->produk->id_produk, 'revisi' => $detail->id_desain]) }}" class="shrink-0 relative z-10 bg-rose-600 hover:bg-rose-500 text-white font-black py-4 px-8 rounded-2xl shadow-xl shadow-rose-200 transition-all transform hover:-translate-y-1 active:scale-95 uppercase tracking-widest text-xs">
                                    PERBAIKI SEKARANG
                                </a>
                            </div>
                        @elseif($detail->status_desain == 'disetujui')
                            <div class="mt-4 p-4 border border-emerald-100 bg-emerald-50 rounded-2xl flex items-center gap-3">
                                <div class="w-6 h-6 bg-emerald-500 rounded-lg flex items-center justify-center text-white shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="text-xs font-black text-emerald-700 uppercase tracking-widest">Desain Disetujui & Siap Produksi</span>
                            </div>
                        @endif

                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
