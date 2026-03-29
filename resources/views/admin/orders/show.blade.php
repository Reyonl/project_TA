<x-app-layout>
    <!-- Muat HTML2Canvas CDN untuk fitur cetak elemen ke Image -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Detail Pesanan & Verifikasi Desain') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Tindakan Admin -->
            <div class="bg-white overflow-hidden shadow-xl shadow-indigo-100/50 sm:rounded-2xl border border-slate-100 p-8">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Update Status Pengerjaan</h3>
                        <p class="text-sm text-slate-500 mt-1">Status pesanan saat ini: 
                            <span class="font-bold text-indigo-600 uppercase">{{ $order->status_order }}</span>
                        </p>
                    </div>

                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="flex gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="status_order" class="rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm font-semibold text-slate-700">
                            <option value="pending" {{ $order->status_order == 'pending' ? 'selected' : '' }}>⏳ Menunggu Konfirmasi</option>
                            <option value="diproses" {{ $order->status_order == 'diproses' ? 'selected' : '' }}>🏭 Sedang Diproses/Dicetak</option>
                            <option value="dikirim" {{ $order->status_order == 'dikirim' ? 'selected' : '' }}>🚚 Dalam Pengiriman</option>
                            <option value="selesai" {{ $order->status_order == 'selesai' ? 'selected' : '' }}>✅ Selesai</option>
                            <option value="dibatalkan" {{ $order->status_order == 'dibatalkan' ? 'selected' : '' }}>❌ Dibatalkan</option>
                        </select>
                        <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg font-bold hover:bg-indigo-500 transition shadow">
                            Simpan Status
                        </button>
                    </form>
                </div>
                
                @if(session('success'))
                    <div class="mt-4 p-4 bg-green-50 text-green-700 font-semibold rounded-lg border border-green-100">
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            <!-- Detail Pesanan & Pelanggan -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Data Pemesan -->
                <div class="col-span-1 bg-white shadow-xl shadow-slate-100/50 sm:rounded-2xl border border-slate-100 p-6">
                    <h4 class="font-bold text-slate-800 border-b pb-2 mb-4">Informasi Pemesan</h4>
                    <ul class="space-y-3 text-sm text-slate-600">
                        <li><span class="font-semibold text-slate-800 block">Nama</span> {{ $order->customer->nama_customer }}</li>
                        <li><span class="font-semibold text-slate-800 block">Email</span> {{ $order->customer->email }}</li>
                        <li><span class="font-semibold text-slate-800 block">No. HP</span> {{ $order->customer->no_hp ?? '-' }}</li>
                        <li><span class="font-semibold text-slate-800 block">Alamat Pengiriman</span> 
                            {{ $order->customer->alamat ?? 'Alamat belum dilengkapi' }}
                        </li>
                    </ul>
                </div>

                <!-- Info Order -->
                <div class="col-span-2 bg-white shadow-xl shadow-slate-100/50 sm:rounded-2xl border border-slate-100 p-6">
                    <h4 class="font-bold text-slate-800 border-b pb-2 mb-4">Item Pesanan & Preview Desain</h4>
                    
                    <div class="space-y-6">
                        @foreach($order->orderDetails as $detail)
                            <div class="flex flex-col sm:flex-row gap-6 bg-slate-50 p-4 rounded-xl border border-slate-100">
                                
                                <!-- Canvas Preview (Jika ada desain) -->
                                <div class="w-full sm:w-5/12 flex-shrink-0 bg-white border border-slate-200 rounded-lg p-3 flex flex-col items-center justify-center min-h-[160px] gap-4">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1 w-full border-b pb-2">Preview Hasil</p>
                                    @if($detail->desain)
                                        <div class="flex flex-row overflow-x-auto w-full gap-5 pb-2 snap-x snap-mandatory pl-1">
                                            <!-- FRONT DESIGN -->
                                            @if($detail->desain->file_desain)
                                                <div class="flex flex-col items-center shrink-0 w-[280px] snap-center">
                                                    <span class="text-[10px] font-bold text-slate-400 mb-1">DEPAN</span>
                                                    <div class="relative w-full h-[350px] rounded-lg overflow-hidden flex items-center justify-center shadow-inner bg-slate-50 border border-slate-200 group transition-all">
                                                        @if($detail->produk->jenis_produk == 'kaos')
                                                            <img src="{{ asset('images/mockups/kaos.png?v='.time()) }}" class="absolute w-[85%] h-[85%] object-contain drop-shadow opacity-95 z-0 transition-transform group-hover:scale-105">
                                                            <div class="absolute w-[85%] h-[85%] mix-blend-multiply z-10 transition-transform group-hover:scale-105"
                                                                 style="-webkit-mask-image: url('{{ asset('images/mockups/kaos.png?v='.time()) }}'); -webkit-mask-size: contain; -webkit-mask-position: center; -webkit-mask-repeat: no-repeat; mask-image: url('{{ asset('images/mockups/kaos.png?v='.time()) }}'); mask-size: contain; mask-position: center; mask-repeat: no-repeat;">
                                                                <div class="w-full h-full" style="background-color: {{ $detail->desain->warna_baju ?: '#ffffff' }};"></div>
                                                            </div>
                                                        @elseif($detail->produk->jenis_produk == 'hoodie')
                                                            <img src="{{ asset('images/mockups/hoodie.png?v='.time()) }}" class="absolute w-[85%] h-[85%] object-contain drop-shadow opacity-95 z-0 transition-transform group-hover:scale-105">
                                                            <div class="absolute w-[85%] h-[85%] mix-blend-multiply z-10 transition-transform group-hover:scale-105"
                                                                 style="-webkit-mask-image: url('{{ asset('images/mockups/hoodie.png?v='.time()) }}'); -webkit-mask-size: contain; -webkit-mask-position: center; -webkit-mask-repeat: no-repeat; mask-image: url('{{ asset('images/mockups/hoodie.png?v='.time()) }}'); mask-size: contain; mask-position: center; mask-repeat: no-repeat;">
                                                                <div class="w-full h-full" style="background-color: {{ $detail->desain->warna_baju ?: '#ffffff' }};"></div>
                                                            </div>
                                                        @endif

                                                        <div class="absolute z-20 transition-transform group-hover:scale-105" style="top: 20%; left: 27.08%; width: 45.83%; height: 53.33%;">
                                                            <img src="{{ Str::startsWith($detail->desain->file_desain, 'data:image') ? $detail->desain->file_desain : Storage::url($detail->desain->file_desain) }}" class="w-full h-full object-contain">
                                                        </div>
                                                    </div>
                                                    @php
                                                        $desainUrl = Str::startsWith($detail->desain->file_desain, 'data:image') ? $detail->desain->file_desain : Storage::url($detail->desain->file_desain);
                                                        $bajuType = $detail->produk->jenis_produk;
                                                        $bajuColor = $detail->desain->warna_baju ?: '#ffffff';
                                                        $mockupUrl = asset('images/mockups/' . $bajuType . '.png?v='.time());
                                                    @endphp
                                                    <button type="button" onclick="openDesignModal('{{ $desainUrl }}', '{{ $mockupUrl }}', '{{ $bajuColor }}', '{{ $bajuType }}', false)" class="mt-2 w-full bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[11px] font-bold py-2 px-3 rounded-lg border border-indigo-200 transition duration-200 flex items-center justify-center gap-1.5 shadow-sm">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                                        Full Mockup Depan
                                                    </button>
                                                </div>
                                            @endif

                                            <!-- BACK DESIGN -->
                                            @if($detail->desain->file_desain_belakang)
                                                <div class="flex flex-col items-center shrink-0 w-[280px] snap-center">
                                                    <span class="text-[10px] font-bold text-slate-400 mb-1">BELAKANG</span>
                                                    <div class="relative w-full h-[350px] rounded-lg overflow-hidden flex items-center justify-center shadow-inner bg-slate-50 border border-slate-200 group transition-all">
                                                        @if($detail->produk->jenis_produk == 'kaos')
                                                            <img src="{{ asset('images/mockups/kaos_belakang.png?v='.time()) }}" class="absolute w-[85%] h-[85%] object-contain drop-shadow opacity-95 z-0 transition-transform group-hover:scale-105">
                                                            <div class="absolute w-[85%] h-[85%] mix-blend-multiply z-10 transition-transform group-hover:scale-105"
                                                                 style="-webkit-mask-image: url('{{ asset('images/mockups/kaos_belakang.png?v='.time()) }}'); -webkit-mask-size: contain; -webkit-mask-position: center; -webkit-mask-repeat: no-repeat; mask-image: url('{{ asset('images/mockups/kaos_belakang.png?v='.time()) }}'); mask-size: contain; mask-position: center; mask-repeat: no-repeat;">
                                                                <div class="w-full h-full" style="background-color: {{ $detail->desain->warna_baju ?: '#ffffff' }};"></div>
                                                            </div>
                                                        @elseif($detail->produk->jenis_produk == 'hoodie')
                                                            <img src="{{ asset('images/mockups/hoodie_belakang.png?v='.time()) }}" class="absolute w-[85%] h-[85%] object-contain drop-shadow opacity-95 z-0 transition-transform group-hover:scale-105">
                                                            <div class="absolute w-[85%] h-[85%] mix-blend-multiply z-10 transition-transform group-hover:scale-105"
                                                                 style="-webkit-mask-image: url('{{ asset('images/mockups/hoodie_belakang.png?v='.time()) }}'); -webkit-mask-size: contain; -webkit-mask-position: center; -webkit-mask-repeat: no-repeat; mask-image: url('{{ asset('images/mockups/hoodie_belakang.png?v='.time()) }}'); mask-size: contain; mask-position: center; mask-repeat: no-repeat;">
                                                                <div class="w-full h-full" style="background-color: {{ $detail->desain->warna_baju ?: '#ffffff' }};"></div>
                                                            </div>
                                                        @endif

                                                        <div class="absolute z-20 transition-transform group-hover:scale-105" style="top: 20%; left: 27.08%; width: 45.83%; height: 53.33%;">
                                                            <img src="{{ Str::startsWith($detail->desain->file_desain_belakang, 'data:image') ? $detail->desain->file_desain_belakang : Storage::url($detail->desain->file_desain_belakang) }}" class="w-full h-full object-contain">
                                                        </div>
                                                    </div>
                                                    @php
                                                        $desainUrlB = Str::startsWith($detail->desain->file_desain_belakang, 'data:image') ? $detail->desain->file_desain_belakang : Storage::url($detail->desain->file_desain_belakang);
                                                        $bajuType = $detail->produk->jenis_produk;
                                                        $bajuColor = $detail->desain->warna_baju ?: '#ffffff';
                                                        $mockupUrlB = asset('images/mockups/' . $bajuType . '_belakang.png?v='.time());
                                                    @endphp
                                                    <button type="button" onclick="openDesignModal('{{ $desainUrlB }}', '{{ $mockupUrlB }}', '{{ $bajuColor }}', '{{ $bajuType }}', true)" class="mt-2 w-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold py-2 px-3 rounded-lg border border-slate-300 transition duration-200 flex items-center justify-center gap-1.5 shadow-sm">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                                        Full Mockup Belakang
                                                    </button>
                                                </div>
                                            @endif
                                            
                                            @if(!$detail->desain->file_desain && !$detail->desain->file_desain_belakang)
                                                 <span class="text-slate-400 text-sm py-8 w-full text-center">Tidak ada desain kustom</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-sm py-8">Tidak ada desain kustom</span>
                                    @endif
                                </div>

                                <!-- Detail Item -->
                                <div class="w-full sm:w-7/12 flex flex-col justify-center">
                                    <h5 class="font-black text-lg text-slate-800">{{ $detail->produk->nama_produk ?? 'Produk Custom' }}</h5>
                                    
                                    <div class="flex items-center gap-2 mb-2">
                                        <p class="text-sm text-slate-500">Desain: {{ $detail->desain->nama_desain ?? 'Desain Kustom' }}</p>
                                        @if($detail->desain && $detail->desain->warna_baju)
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            Warna Baju:
                                            <span class="w-3 h-3 rounded-full border border-slate-300 shadow-sm" style="background-color: {{ $detail->desain->warna_baju }}"></span>
                                            {{ strtoupper($detail->desain->warna_baju) }}
                                        </span>
                                        @endif
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4 mt-2 mb-3">
                                        <div class="bg-white p-2 rounded border border-slate-100">
                                            <p class="text-xs text-slate-400 font-bold uppercase">Harga Satuan</p>
                                            <p class="font-semibold text-slate-800">Rp {{ number_format(($detail->harga_produk ?? 0) + ($detail->harga_desain ?? 0), 0, ',', '.') }}</p>
                                        </div>
                                        <div class="bg-white p-2 rounded border border-slate-100">
                                            <p class="text-xs text-slate-400 font-bold uppercase">Jumlah Qty</p>
                                            <p class="font-semibold text-slate-800">{{ $detail->quantity ?? 1 }} pcs</p>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center border-t border-slate-200 pt-3 mt-auto">
                                        <span class="font-bold text-slate-600">Subtotal Item:</span>
                                        <span class="font-black text-indigo-600 text-lg">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                                    </div>

                                    @if($detail->status_desain == 'revisi')
                                        <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                            <p class="text-xs font-bold text-red-600 uppercase mb-1">Status: Menunggu Revisi Customer</p>
                                            <p class="text-sm text-red-700 italic">"{{ $detail->catatan_admin }}"</p>
                                        </div>
                                    @elseif($detail->status_desain == 'disetujui')
                                        <div class="mt-3 p-2 bg-emerald-50 border border-emerald-200 rounded-lg text-center">
                                            <p class="text-xs font-bold text-emerald-600 uppercase">Status: Desain Disetujui</p>
                                        </div>
                                    @else
                                        <div class="mt-4 flex gap-2">
                                            <button onclick="document.getElementById('modalRevisi_{{ $detail->id_order_detail }}').showModal()" class="flex-1 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-bold py-2 rounded-lg text-sm transition">
                                                Tolak & Minta Revisi
                                            </button>
                                            <form action="{{ route('admin.orders.updateStatusDesain', [$order->id_order, $detail->id_order_detail]) }}" method="POST" class="flex-1">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status_desain" value="disetujui">
                                                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2 rounded-lg text-sm transition">
                                                    Setujui Desain
                                                </button>
                                            </form>
                                        </div>
                                    @endif

                                    <!-- Modal Form Revisi -->
                                    <dialog id="modalRevisi_{{ $detail->id_order_detail }}" class="p-0 rounded-2xl shadow-2xl backdrop:bg-slate-900/50 w-full max-w-lg">
                                        <div class="p-6">
                                            <h3 class="text-lg font-bold text-slate-800 mb-2">Tolak & Minta Revisi Desain</h3>
                                            <p class="text-sm text-slate-500 mb-4">Berikan catatan perbaikan agar pelanggan dapat memperbaiki desainnya.</p>
                                            
                                            <form action="{{ route('admin.orders.updateStatusDesain', [$order->id_order, $detail->id_order_detail]) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status_desain" value="revisi">
                                                <textarea name="catatan_admin" rows="4" class="w-full rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500 text-sm mb-4" placeholder="Misal: Resolusi gambar depan terlalu kecil / pecah..." required></textarea>
                                                
                                                <div class="flex gap-2 justify-end">
                                                    <button type="button" onclick="document.getElementById('modalRevisi_{{ $detail->id_order_detail }}').close()" class="px-4 py-2 bg-slate-100 font-bold text-slate-600 rounded-lg">Batal</button>
                                                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 font-bold text-white rounded-lg transition">Kirim Permintaan Revisi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </dialog>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Total Keseluruhan -->
                    <div class="mt-8 flex justify-between items-center p-4 bg-indigo-50 border border-indigo-100 rounded-xl">
                        <span class="text-lg font-bold text-indigo-900">Total Tagihan Pesanan</span>
                        <span class="text-2xl font-black text-indigo-700">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                    </div>

                </div>
            </div>

            <div class="text-center pt-4">
                <a href="{{ route('admin.orders.index') }}" class="text-slate-500 hover:text-slate-800 font-semibold underline decoration-2 underline-offset-4">
                    &larr; Kembali ke Daftar Pesanan
                </a>
            </div>
        </div>
    </div>

    <!-- Modal Fullscreen Design untuk Tim Sablon -->
    <div id="designModal" class="fixed inset-0 z-[999] bg-slate-900/90 hidden items-center justify-center p-4 backdrop-blur-sm transition-opacity opacity-0 duration-300" onclick="closeDesignModal(event)">
        <div class="relative max-w-5xl w-full max-h-[90vh] flex flex-col items-center justify-center pointer-events-none">
            
            <button type="button" class="absolute -top-12 right-0 text-white hover:text-indigo-300 font-bold text-sm pointer-events-auto bg-black/40 hover:bg-black/60 px-5 py-2 rounded-full transition flex items-center gap-2" onclick="closeDesignModal(event, true)">
                Tutup <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            
            <div class="bg-white/5 p-4 rounded-2xl border border-white/10 shadow-2xl backdrop-blur-md pointer-events-auto flex flex-col items-center">
                <!-- Container Komposit Mockup HD (Rasio 4:5 proporsi presisi) -->
                <div id="mockupContainerFull" class="relative rounded-xl overflow-hidden shadow-inner bg-slate-50 flex items-center justify-center mx-auto" style="height: 70vh; max-height: 625px; aspect-ratio: 4/5;">
                    <!-- Base Image -->
                    <img id="modalMockupBase" src="" crossorigin="anonymous" class="absolute w-[85%] h-[85%] object-contain drop-shadow opacity-95 z-0">
                    <!-- Color Masking -->
                    <div id="modalMockupMask" class="absolute w-[85%] h-[85%] mix-blend-multiply z-10" style="">
                        <div id="modalMockupColor" class="w-full h-full" style="background-color: #ffffff;"></div>
                    </div>
                    <!-- Design Layer -->
                    <div class="absolute z-20" style="top: 20%; left: 27.08%; width: 45.83%; height: 53.33%;">
                        <img id="modalDesignImage" src="" alt="Full Design Sablon" crossorigin="anonymous" class="w-full h-full object-contain">
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-white/10 w-full flex justify-between items-center px-2">
                    <p class="text-white/80 text-sm font-medium tracking-wide">
                        <span class="text-indigo-400 font-bold">Preview:</span> Mockup Baju Utuh <span class="mx-2 text-white/30">|</span> 
                        <span class="text-indigo-400 font-bold">Produk:</span> <span id="modalJenisProduk" class="uppercase">Kaos</span>
                    </p>
                    <button type="button" onclick="downloadMockupImage()" id="downloadDesainBtn" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold py-2 px-5 rounded-lg shadow transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 12m4-4v12"></path></svg>
                        <span>Download Mockup (Utuh)</span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <script>
        const modal = document.getElementById('designModal');
        const modalImg = document.getElementById('modalDesignImage'); // Layer transparan
        const modalBase = document.getElementById('modalMockupBase'); // Image Base Kaos
        const modalMask = document.getElementById('modalMockupMask'); // Div Masking Container
        const modalColor = document.getElementById('modalMockupColor'); // Div Background Color
        const modalJenis = document.getElementById('modalJenisProduk'); // Teks Jenis
        const downloadBtn = document.getElementById('downloadDesainBtn');

        let isFlipped = false;

        function openDesignModal(desainUrl, mockupUrl, bajuColor, bajuType, flip = false) {
            isFlipped = flip;
            // Pasang semua aset komposit
            modalImg.src = desainUrl;
            modalBase.src = mockupUrl;
            
            // Atur masking sesuai URL Baju terupdate 
            modalMask.style.webkitMaskImage = `url('${mockupUrl}')`;
            modalMask.style.webkitMaskSize  = 'contain';
            modalMask.style.webkitMaskPosition = 'center';
            modalMask.style.webkitMaskRepeat = 'no-repeat';
            
            modalMask.style.maskImage = `url('${mockupUrl}')`;
            modalMask.style.maskSize  = 'contain';
            modalMask.style.maskPosition = 'center';
            modalMask.style.maskRepeat = 'no-repeat';

            // Set Warna
            modalColor.style.backgroundColor = bajuColor;
            
            // Set Label
            modalJenis.textContent = bajuType + (flip ? ' (Belakang)' : ' (Depan)');
            
            modalBase.style.transform = 'none';
            modalMask.style.transform = 'none';
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => modal.classList.remove('opacity-0'), 10);
            document.body.style.overflow = 'hidden';
        }

        function closeDesignModal(e, force = false) {
            // Tutup hanya jika di-klik di luar gambar (area background gelap) atau tombol Tutup
            if (force || e.target === modal) {
                modal.classList.add('opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    modalImg.src = '';
                    document.body.style.overflow = 'auto'; // Buka kembsali scroll
                }, 300);
            }
        }

        // Fungsi baru berbasis Native Canvas untuk Render Mockup Baju Utuh dengan Benar
        // Menggantikan HTML2Canvas yang terblokir "Tainted Canvas" & "Mix-Blend"
        function downloadMockupImage() {
            const btn = document.getElementById('downloadDesainBtn');
            const originalText = btn.innerHTML;
            
            // Animasi loading
            btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...`;
            btn.disabled = true;

            const baseImgStr = modalBase.src; 
            const designImgStr = modalImg.src;
            const bajuColor = modalColor.style.backgroundColor;

            // Buat Canvas bayangan beresolusi 500x625
            const W = 500;
            const H = 625;
            const canvas = document.createElement('canvas');
            canvas.width = W;
            canvas.height = H;
            const ctx = canvas.getContext('2d');

            // Set Background Putih Salju sebagai landasan kanvas
            ctx.fillStyle = "#f8fafc"; // Warna slate-50 yang sama seperti background container
            ctx.fillRect(0, 0, W, H);

            // Fungsi utilitas helper untuk meload gambar
            const loadImage = (url) => {
                return new Promise((resolve, reject) => {
                    const img = new Image();
                    img.crossOrigin = 'Anonymous'; // Mencegah Tainted Canvas CORS
                    
                    // Jika URL adalah data Base64, tidak butuh trik tambahan.
                    // Jika URL adalah asset URL biasa, tambahkan dummy parameter cachebuster agar tidak disajikan dari cache lokal bermasalah
                    let safeUrl = url;
                    if (!url.startsWith('data:')) {
                       safeUrl = url + (url.indexOf('?') > -1 ? '&' : '?') + 'timestamp=' + new Date().getTime();
                    }

                    img.onload = () => resolve(img);
                    img.onerror = () => reject(new Error("Gagal meload elemen: " + url));
                    img.src = safeUrl;
                });
            };

            // Urutan Rendering: 
            // 1. Gambar Base Baju
            // 2. Gambar Warna Baju (dengan mask base & globalCompositeOperation multiply)
            // 3. Gambar Desain Sablon di tengah
            
            Promise.all([loadImage(baseImgStr), loadImage(designImgStr)]).then(images => {
                const baseImgDOM = images[0];
                const designImgDOM = images[1];

                // --- LAYER 1: Base Image Kaos/Hoodie ---
                // Karena img di UI punya style w-[85%] h-[85%], kita skalakan sama.  (85% dari 500 = 425)
                const imgW = W * 0.85; 
                const imgH = H * 0.85;
                const offsetX = (W - imgW) / 2; // Rata Tengah (37.5px)
                const offsetY = (H - imgH) / 2; // Rata Tengah (46.8px)

                ctx.drawImage(baseImgDOM, offsetX, offsetY, imgW, imgH);

                // --- LAYER 2: Overlay Warna Baju (Multiply) ---
                // Buat canvas masking warna bayangan sementara terlebih dulu agar aman
                const colorCanvas = document.createElement('canvas');
                colorCanvas.width = W; colorCanvas.height = H;
                const cCtx = colorCanvas.getContext('2d');
                
                // Gambar Base baju sebagai masker siluetnya
                cCtx.drawImage(baseImgDOM, offsetX, offsetY, imgW, imgH);
                
                // Tempelkan blok warna pilihan di atas siluet baju tersebut (hanya yg berpotongan)
                cCtx.globalCompositeOperation = "source-in";
                cCtx.fillStyle = bajuColor;
                cCtx.fillRect(0, 0, W, H);
                
                // Tindihkan canvas warna tadi ke canvas utama menggunakan mode Multiply
                ctx.globalCompositeOperation = "multiply";
                ctx.drawImage(colorCanvas, 0, 0);

                // Kembalikan status blending agar sablon tidak ikut ter-multiply
                ctx.globalCompositeOperation = "source-over";

                // --- LAYER 3: Desain Sablon Pilihan ---
                // Sesuai layout visual di CSS: top: 20%; left: 27.08%; width: 45.83%; height: 53.33%;
                const dX = W * 0.2708;
                const dY = H * 0.20;
                const dW = W * 0.4583;
                const dH = H * 0.5333;
                
                ctx.drawImage(designImgDOM, dX, dY, dW, dH);

                // SUKSES. EKSPOR CANVAS MENJADI GAMBAR PNG!
                const sideText = isFlipped ? 'Belakang' : 'Depan';
                const finalImgData = canvas.toDataURL("image/png");
                const link = document.createElement('a');
                link.download = `Mockup_Sablon_Order_${sideText}_${new Date().getTime()}.png`;
                link.href = finalImgData;
                link.click();

                // Kembalikan Tombol ke Semula
                btn.innerHTML = originalText;
                btn.disabled = false;

            }).catch(err => {
                console.error("Gagal sintesis Mockup Native Canvas: ", err);
                alert("Koneksi bermasalah saat mengekstraksi gambar desain. Silakan coba lagi.");
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    </script>
</x-app-layout>
