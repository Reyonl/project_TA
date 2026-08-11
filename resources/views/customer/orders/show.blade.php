<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 leading-tight font-outfit uppercase tracking-tight">
            Tracking Order <span class="text-red-600">#{{ str_pad($order->id_order, 5, '0', STR_PAD_LEFT) }}</span>
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @php
                $statusOrder = ['reviewing', 'pending_payment', 'processing', 'completed'];
                $steps = [
                    ['status' => 'reviewing', 'label' => 'Review Desain'],
                    ['status' => 'pending_payment', 'label' => 'Pembayaran'],
                    ['status' => 'processing', 'label' => 'Produksi'],
                    ['status' => 'completed', 'label' => 'Selesai'],
                ];
                $isCancelled = $order->status_order === 'cancelled';
            @endphp

            <div class="bg-white overflow-hidden shadow-[0_20px_50px_rgba(220,_38,_38,_0.05)] sm:rounded-[2.5rem] border border-slate-100 p-10 mb-8 relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-red-50 rounded-full blur-3xl opacity-50 group-hover:scale-150 transition-transform duration-1000"></div>
 
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-6">
                        <div class="w-16 h-16 bg-red-600 rounded-2xl flex items-center justify-center text-3xl shadow-xl shadow-red-100 animate-pulse shrink-0">📦</div>
                        <div>
                            <h3 class="text-2xl font-black text-slate-900 font-outfit uppercase tracking-tight">Status: <span class="text-red-600 uppercase">{{ str_replace('_', ' ', $order->status_order) }}</span></h3>
                            <p class="text-slate-500 font-medium italic mt-1">Terakhir diperbarui: {{ $order->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex flex-col w-full md:w-auto gap-4">
                        <a href="{{ route('customer.orders.index') }}" class="w-full md:w-auto text-center px-6 py-3 bg-slate-100 text-[10px] font-black uppercase tracking-widest text-slate-600 rounded-xl hover:bg-slate-200 transition shadow-sm">Kembali Ke List</a>
                    </div>
                </div>
            </div>

            @if(!$isCancelled)
            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 p-8 relative overflow-hidden">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between relative z-10 gap-8 md:gap-0">
                    @foreach($steps as $step)
                        @php 
                            $isCompleted = array_search($order->status_order, $statusOrder) >= array_search($step['status'], $statusOrder);
                            $isActive = $order->status_order === $step['status'];
                        @endphp
                        <div class="flex flex-row md:flex-col items-center justify-start relative z-10 w-full group/step gap-4 md:gap-0">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center md:mb-4 transition-all duration-500 shadow-xl border-2 {{ $isActive ? 'bg-red-600 text-white border-red-200 scale-125' : ($isCompleted ? 'bg-red-100 text-red-600 border-red-200' : 'bg-slate-50 text-slate-300 border-slate-100 group-hover/step:border-red-100 hover:scale-110') }} shrink-0">
                                @if($isCompleted && !$isActive)
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                @else
                                    <span class="text-lg font-black font-outfit">{{ $loop->iteration }}</span>
                                @endif
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] {{ $isActive ? 'text-red-600' : ($isCompleted ? 'text-slate-800' : 'text-slate-400') }}">{{ $step['label'] }}</span>
                            @if(!$loop->last)
                                <div class="absolute top-6 left-[60%] w-[80%] h-0.5 {{ $isCompleted ? 'bg-red-200' : 'bg-slate-100' }} -z-10 hidden md:block"></div>
                                <div class="absolute top-12 left-6 w-0.5 h-12 {{ $isCompleted ? 'bg-red-200' : 'bg-slate-100' }} -z-10 block md:hidden"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ===== Info Order ===== --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Informasi Transaksi</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8 text-sm">
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
                        <dd class="font-black text-red-600 text-2xl font-outfit italic tracking-tighter">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Status Saat Ini</dt>
                        <dd class="mt-1">
                            @php
                                $statusColors = [
                                    'reviewing'  => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'pending_payment' => 'bg-orange-100 text-orange-700 border-orange-200',
                                    'processing' => 'bg-sky-100 text-sky-700 border-sky-200',
                                    'completed'  => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'cancelled' => 'bg-rose-100 text-rose-700 border-rose-200',
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

            {{-- ===== Form Upload Pembayaran ===== --}}
            @if($order->payment_status === 'awaiting_payment' || $order->payment_status === 'failed')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-4 mb-4">Pembayaran (Transfer Bank)</h3>
                
                @if($order->payment_status === 'failed')
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-xl mb-6">
                        <strong>Pembayaran Ditolak Admin!</strong> Silakan periksa kembali dan unggah ulang bukti transfer yang benar.
                    </div>
                @endif

                <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-xl mb-6 flex items-start gap-4">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center font-bold text-indigo-600 shadow-sm shrink-0">BCA</div>
                    <div>
                        <p class="text-sm text-slate-600 mb-1">Silakan transfer sesuai total tagihan ke rekening berikut:</p>
                        <div class="flex items-center gap-3">
                            <p class="text-lg font-bold text-slate-800 font-mono tracking-wider">8273 1234 5678</p>
                        </div>
                        <p class="text-sm font-semibold text-slate-700 mt-1">A.N: CustomSablon Nusantara</p>
                    </div>
                </div>

                <form action="{{ route('customer.orders.payment', $order->id_order) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Upload Bukti Transfer <span class="text-red-500">*</span></label>
                        <input type="file" name="bukti_pembayaran" accept="image/png, image/jpeg, image/jpg" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        @error('bukti_pembayaran')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg transition">
                        Kirim Bukti Pembayaran
                    </button>
                </form>
            </div>
            @elseif($order->payment_status === 'awaiting_verification')
            <div class="bg-blue-50 border border-blue-200 text-blue-700 p-4 rounded-xl shadow-sm">
                <p class="font-bold">Menunggu Verifikasi Pembayaran</p>
                <p class="text-sm mt-1">Bukti pembayaran Anda sedang diperiksa oleh Admin.</p>
            </div>
            @endif

            {{-- ===== Item Pesanan ===== --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider mb-4">Item Pesanan</h3>
                <div class="space-y-4">
                    @foreach($order->orderDetails as $detail)
                        <div class="flex flex-col sm:flex-row items-start gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100 w-full">
                            <div class="flex flex-row gap-2 overflow-x-auto w-full sm:w-auto pb-2 sm:pb-0 hide-scrollbar">
                                <!-- DESAIN DEPAN -->
                                <div class="w-[80px] h-[100px] rounded-lg overflow-hidden bg-slate-50 border border-slate-200 shrink-0 flex items-center justify-center relative shadow-inner">
                                    @if($detail->desain && $detail->desain->file_desain)
                                    <span class="absolute top-0.5 left-0.5 z-30 bg-white/80 text-[8px] font-bold px-1 rounded shadow-sm">Depan</span>
                                    @endif
                                    @php
                                        $mockupBase = match($detail->produk->jenis_produk) {
                                            'kaos' => 'kaos',
                                            'hoodie' => 'hoodie',
                                            'topi' => 'topi',
                                            'polo' => 'polo',
                                            'seragam' => 'seragam',
                                            default => 'kaos'
                                        };
                                        $mockupPath = asset('images/mockups/' . $mockupBase . '.png');
                                        $hasBack = in_array($detail->produk->jenis_produk, ['kaos', 'hoodie', 'polo', 'seragam']);
                                    @endphp

                                    @if($detail->desain && $detail->desain->file_desain)
                                        <img src="{{ $mockupPath }}" class="absolute w-[85%] h-[85%] object-contain drop-shadow opacity-90 z-0">
                                        <div class="absolute w-[85%] h-[85%] mix-blend-multiply z-10"
                                             style="-webkit-mask-image: url('{{ $mockupPath }}'); -webkit-mask-size: contain; -webkit-mask-position: center; -webkit-mask-repeat: no-repeat; mask-image: url('{{ $mockupPath }}'); mask-size: contain; mask-position: center; mask-repeat: no-repeat;">
                                            <div class="w-full h-full" style="background-color: {{ $detail->desain->warna_baju ?: '#ffffff' }};"></div>
                                        </div>
                                        
                                        @php
                                            // Dynamic overlay positioning
                                            $overlayStyle = match($detail->produk->jenis_produk) {
                                                'topi' => 'top: 40%; left: 35%; width: 30%; height: 25%;',
                                                'polo' => 'top: 26.66%; left: 55.2%; width: 18.75%; height: 15%;',
                                                'seragam' => 'top: 30%; left: 28%; width: 22%; height: 20%;',
                                                default => 'top: 20%; left: 27.08%; width: 45.83%; height: 53.33%;'
                                            };
                                        @endphp
                                        <div class="absolute z-20" style="{{ $overlayStyle }}">
                                            <img src="{{ Str::startsWith($detail->desain->file_desain, 'data:image') ? $detail->desain->file_desain : asset('storage/' . $detail->desain->file_desain) }}" class="w-full h-full object-contain">
                                        </div>
                                    @elseif($detail->produk && $detail->produk->gambar_produk)
                                        <img src="{{ asset('storage/' . $detail->produk->gambar_produk) }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-3xl">🎨</span>
                                    @endif
                                </div>

                                <!-- DESAIN BELAKANG -->
                                @if($detail->desain && $detail->desain->file_desain_belakang && $hasBack)
                                <div class="w-[80px] h-[100px] rounded-lg overflow-hidden bg-slate-50 border border-slate-200 shrink-0 flex items-center justify-center relative shadow-inner">
                                    <span class="absolute top-0.5 left-0.5 z-30 bg-white/80 text-[8px] font-bold px-1 rounded shadow-sm">Belakang</span>
                                    @php $mockupPathB = asset('images/mockups/' . $mockupBase . '_belakang.png'); @endphp
                                    <img src="{{ $mockupPathB }}" class="absolute w-[85%] h-[85%] object-contain drop-shadow opacity-90 z-0" onerror="this.src='{{ $mockupPath }}'">
                                    <div class="absolute w-[85%] h-[85%] mix-blend-multiply z-10"
                                         style="-webkit-mask-image: url('{{ $mockupPathB }}'); -webkit-mask-size: contain; -webkit-mask-position: center; -webkit-mask-repeat: no-repeat; mask-image: url('{{ $mockupPathB }}'); mask-size: contain; mask-position: center; mask-repeat: no-repeat;">
                                        <div class="w-full h-full" style="background-color: {{ $detail->desain->warna_baju ?: '#ffffff' }};"></div>
                                    </div>
                                    
                                    <div class="absolute z-20" style="top: 20%; left: 27.08%; width: 45.83%; height: 53.33%;">
                                        <img src="{{ Str::startsWith($detail->desain->file_desain_belakang, 'data:image') ? $detail->desain->file_desain_belakang : asset('storage/' . $detail->desain->file_desain_belakang) }}" class="w-full h-full object-contain">
                                    </div>
                                </div>
                                @endif

                                <!-- DESAIN KIRI -->
                                @if($detail->desain && $detail->desain->file_desain_kiri)
                                <div class="w-[80px] h-[100px] rounded-lg overflow-hidden bg-slate-50 border border-slate-200 shrink-0 flex items-center justify-center relative shadow-inner">
                                    <span class="absolute top-0.5 left-0.5 z-30 bg-white/80 text-[8px] font-bold px-1 rounded shadow-sm">Kiri</span>
                                    @php $mockupPathL = asset('images/mockups/' . $mockupBase . '_kiri.png'); @endphp
                                    <img src="{{ $mockupPathL }}" class="absolute w-[85%] h-[85%] object-contain drop-shadow opacity-90 z-0" onerror="this.src='{{ $mockupPath }}'">
                                    <div class="absolute w-[85%] h-[85%] mix-blend-multiply z-10"
                                         style="-webkit-mask-image: url('{{ $mockupPathL }}'); -webkit-mask-size: contain; -webkit-mask-position: center; -webkit-mask-repeat: no-repeat; mask-image: url('{{ $mockupPathL }}'); mask-size: contain; mask-position: center; mask-repeat: no-repeat;">
                                        <div class="w-full h-full" style="background-color: {{ $detail->desain->warna_baju ?: '#ffffff' }};"></div>
                                    </div>
                                    <div class="absolute z-20" style="{{ $overlayStyle ?? 'top: 20%; left: 27.08%; width: 45.83%; height: 53.33%;' }}">
                                        <img src="{{ Str::startsWith($detail->desain->file_desain_kiri, 'data:image') ? $detail->desain->file_desain_kiri : asset('storage/' . $detail->desain->file_desain_kiri) }}" class="w-full h-full object-contain">
                                    </div>
                                </div>
                                @endif

                                <!-- DESAIN KANAN -->
                                @if($detail->desain && $detail->desain->file_desain_kanan)
                                <div class="w-[80px] h-[100px] rounded-lg overflow-hidden bg-slate-50 border border-slate-200 shrink-0 flex items-center justify-center relative shadow-inner">
                                    <span class="absolute top-0.5 left-0.5 z-30 bg-white/80 text-[8px] font-bold px-1 rounded shadow-sm">Kanan</span>
                                    @php $mockupPathR = asset('images/mockups/' . $mockupBase . '_kanan.png'); @endphp
                                    <img src="{{ $mockupPathR }}" class="absolute w-[85%] h-[85%] object-contain drop-shadow opacity-90 z-0" onerror="this.src='{{ $mockupPath }}'">
                                    <div class="absolute w-[85%] h-[85%] mix-blend-multiply z-10"
                                         style="-webkit-mask-image: url('{{ $mockupPathR }}'); -webkit-mask-size: contain; -webkit-mask-position: center; -webkit-mask-repeat: no-repeat; mask-image: url('{{ $mockupPathR }}'); mask-size: contain; mask-position: center; mask-repeat: no-repeat;">
                                        <div class="w-full h-full" style="background-color: {{ $detail->desain->warna_baju ?: '#ffffff' }};"></div>
                                    </div>
                                    <div class="absolute z-20" style="{{ $overlayStyle ?? 'top: 20%; left: 27.08%; width: 45.83%; height: 53.33%;' }}">
                                        <img src="{{ Str::startsWith($detail->desain->file_desain_kanan, 'data:image') ? $detail->desain->file_desain_kanan : asset('storage/' . $detail->desain->file_desain_kanan) }}" class="w-full h-full object-contain">
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="flex-1 w-full mt-2 sm:mt-0">
                                <p class="font-bold text-slate-800">{{ $detail->produk->nama_produk ?? 'Produk' }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-2 py-0.5 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-md">{{ $detail->produk->jenis_produk }}</span>
                                    <span class="px-2 py-0.5 bg-sky-500 text-white text-[9px] font-black uppercase tracking-widest rounded-md">Sablon</span>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-600">
                                    <span>Jumlah: <strong>{{ $detail->quantity }} pcs</strong></span>
                                    <span>Harga Produk: <strong>Rp {{ number_format($detail->harga_produk, 0, ',', '.') }}</strong></span>
                                    <span>Harga Desain: <strong>Rp {{ number_format($detail->harga_desain, 0, ',', '.') }}</strong></span>
                                    @if($detail->desain && $detail->desain->detail_sablon)
                                        <span class="block w-full text-xs text-slate-500 mt-2 bg-slate-100 p-2 rounded-lg border border-slate-200/50"><strong class="text-slate-600">Rincian Sablon:</strong> {{ $detail->desain->detail_sablon }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <p class="text-xs text-slate-500">Subtotal</p>
                                <p class="font-bold text-red-600 text-lg">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <!-- Status Revisi -->
                        @if($detail->status_desain == 'revision_required')
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
                                <a href="{{ route('customer.designs.editor', ['produk' => $detail->produk->id_produk, 'revisi' => $detail->id_desain]) }}" class="w-full sm:w-auto shrink-0 relative z-10 bg-rose-600 hover:bg-rose-500 text-white font-black py-4 px-8 rounded-2xl shadow-xl shadow-rose-200 transition-all transform hover:-translate-y-1 active:scale-95 uppercase tracking-widest text-xs text-center mt-4 sm:mt-0">
                                    PERBAIKI SEKARANG
                                </a>
                            </div>
                        @elseif($detail->status_desain == 'approved')
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
