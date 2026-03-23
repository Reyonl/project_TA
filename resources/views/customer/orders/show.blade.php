<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Detail Pesanan #{{ str_pad($order->id_order, 5, '0', STR_PAD_LEFT) }}
            </h2>
            <a href="{{ route('customer.orders.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                ← Semua Pesanan
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
            @endphp

            {{-- ===== Progress Bar Status ===== --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider mb-6">Status Pesanan</h3>

                @if($isCancelled)
                    <div class="flex items-center gap-3 bg-red-50 rounded-xl p-4 text-red-700">
                        <span class="text-2xl">❌</span>
                        <div>
                            <p class="font-bold">Pesanan Dibatalkan</p>
                            <p class="text-sm text-red-500">Pesanan ini telah dibatalkan.</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center justify-between">
                        @foreach($statusSteps as $i => $step)
                            @php
                                $isDone   = $currentIndex !== false && $i <= $currentIndex;
                                $isActive = $currentIndex !== false && $i === $currentIndex;
                                $cfg      = $statusLabels[$step];
                            @endphp
                            <div class="flex flex-col items-center flex-1 relative">
                                {{-- Connector line --}}
                                @if($i < count($statusSteps) - 1)
                                    <div class="absolute top-5 left-1/2 w-full h-0.5 {{ $isDone && $i < $currentIndex ? 'bg-indigo-500' : 'bg-slate-200' }}"></div>
                                @endif

                                {{-- Circle --}}
                                <div class="relative z-10 w-10 h-10 rounded-full flex items-center justify-center text-base font-bold
                                    {{ $isActive ? 'bg-indigo-600 text-white ring-4 ring-indigo-100' : ($isDone ? 'bg-indigo-500 text-white' : 'bg-slate-200 text-slate-400') }}">
                                    {{ $isDone ? $cfg['icon'] : ($i + 1) }}
                                </div>
                                <span class="mt-2 text-xs font-{{ $isActive ? 'bold' : 'medium' }} text-{{ $isActive ? 'indigo-600' : ($isDone ? 'slate-700' : 'slate-400') }} text-center">
                                    {{ $cfg['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ===== Info Order ===== --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider mb-4">Informasi Pesanan</h3>
                <dl class="grid grid-cols-2 gap-y-3 gap-x-6 text-sm">
                    <div>
                        <dt class="text-slate-500">Nomor Pesanan</dt>
                        <dd class="font-semibold text-slate-800">#{{ str_pad($order->id_order, 5, '0', STR_PAD_LEFT) }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Tanggal Order</dt>
                        <dd class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($order->tanggal_order)->translatedFormat('d F Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Total Harga</dt>
                        <dd class="font-bold text-indigo-600 text-base">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Status</dt>
                        <dd>
                            @php
                                $statusColors = [
                                    'pending'  => 'bg-yellow-100 text-yellow-800',
                                    'diproses' => 'bg-blue-100 text-blue-800',
                                    'dikirim'  => 'bg-purple-100 text-purple-800',
                                    'selesai'  => 'bg-green-100 text-green-800',
                                    'dibatalkan' => 'bg-red-100 text-red-800',
                                ];
                                $colorClass = $statusColors[$order->status_order] ?? 'bg-slate-100 text-slate-700';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $colorClass }}">
                                {{ ucfirst($order->status_order) }}
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
                            {{-- Desain preview --}}
                            <div class="w-[80px] h-[100px] rounded-lg overflow-hidden bg-slate-50 border border-slate-200 shrink-0 flex items-center justify-center relative shadow-inner">
                                @if($detail->desain && $detail->desain->file_desain)
                                    @if($detail->produk->jenis_produk == 'kaos')
                                        <img src="{{ asset('images/mockups/kaos.png?v='.time()) }}" class="absolute w-[85%] h-[85%] object-contain drop-shadow opacity-90 z-0">
                                        <div class="absolute w-[85%] h-[85%] mix-blend-multiply z-10"
                                             style="-webkit-mask-image: url('{{ asset('images/mockups/kaos.png?v='.time()) }}'); -webkit-mask-size: contain; -webkit-mask-position: center; -webkit-mask-repeat: no-repeat; mask-image: url('{{ asset('images/mockups/kaos.png?v='.time()) }}'); mask-size: contain; mask-position: center; mask-repeat: no-repeat;">
                                            <div class="w-full h-full" style="background-color: {{ $detail->desain->warna_baju ?? '#ffffff' }};"></div>
                                        </div>
                                    @elseif($detail->produk->jenis_produk == 'hoodie')
                                        <img src="{{ asset('images/mockups/hoodie.png?v='.time()) }}" class="absolute w-[85%] h-[85%] object-contain drop-shadow opacity-90 z-0">
                                        <div class="absolute w-[85%] h-[85%] mix-blend-multiply z-10"
                                             style="-webkit-mask-image: url('{{ asset('images/mockups/hoodie.png?v='.time()) }}'); -webkit-mask-size: contain; -webkit-mask-position: center; -webkit-mask-repeat: no-repeat; mask-image: url('{{ asset('images/mockups/hoodie.png?v='.time()) }}'); mask-size: contain; mask-position: center; mask-repeat: no-repeat;">
                                            <div class="w-full h-full" style="background-color: {{ $detail->desain->warna_baju ?? '#ffffff' }};"></div>
                                        </div>
                                    @endif
                                    
                                    <div class="absolute z-20" style="top: 20%; left: 27.08%; width: 45.83%; height: 53.33%;">
                                        <img src="{{ Str::startsWith($detail->desain->file_desain, 'data:image') ? $detail->desain->file_desain : asset('storage/' . $detail->desain->file_desain) }}" class="w-full h-full object-contain">
                                    </div>
                                @else
                                    <span class="text-3xl">🎨</span>
                                @endif
                            </div>

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
                                <p class="font-bold text-indigo-600">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
