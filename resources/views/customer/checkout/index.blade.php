<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Checkout Pesanan') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row gap-6 sm:gap-8">
            
            <!-- Sisi Kiri: Daftar Keranjang & Upload Bukti Pembayaran -->
            <div class="lg:w-2/3 space-y-6">
                
                <!-- Daftar Produk Checkout -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                    <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-4 mb-4">Item yang Dibeli</h3>
                    
                    <div class="space-y-4">
                        @foreach($carts as $cart)
                            <div class="flex items-center gap-4 p-4 border border-slate-100 rounded-xl bg-slate-50">
                                <!-- Mini Thumbnail -->
                                <div class="w-16 h-16 bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden flex-shrink-0 relative flex items-center justify-center">
                                    @if($cart->desain)
                                        @php
                                            $bajuType = $cart->produk->jenis_produk;
                                            $bajuColor = $cart->desain->warna_baju ?: '#ffffff';
                                            $isPanjang = \Illuminate\Support\Str::contains(strtolower($cart->produk->nama_produk), 'panjang');
                                            $mockupBase = match($bajuType) {
                                                'kaos' => $isPanjang ? 'kaos_panjang' : 'kaos',
                                                'hoodie' => 'hoodie',
                                                'topi' => 'topi',
                                                'polo' => 'polo',
                                                'seragam' => 'seragam',
                                                default => 'kaos'
                                            };
                                            $mockupUrl = asset('images/mockups/' . $mockupBase . '.png');
                                        @endphp
                                        
                                        <!-- Mini Composite -->
                                        <div class="absolute inset-0 z-0">
                                            <div class="w-full h-full flex items-center justify-center relative overflow-hidden">
                                                <img src="{{ $mockupUrl }}" class="absolute w-[85%] h-[85%] object-contain drop-shadow opacity-90 z-0">
                                                <div class="absolute w-[85%] h-[85%] mix-blend-multiply z-10"
                                                     style="-webkit-mask-image: url('{{ $mockupUrl }}'); -webkit-mask-size: contain; -webkit-mask-position: center; -webkit-mask-repeat: no-repeat; mask-image: url('{{ $mockupUrl }}'); mask-size: contain; mask-position: center; mask-repeat: no-repeat;">
                                                    <div class="w-full h-full" style="background-color: {{ $bajuColor }};"></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Desain Overlay -->
                                        <div class="absolute z-20" style="top: 20%; left: 27.08%; width: 45.83%; height: 53.33%;">
                                            @if($cart->desain->file_desain)
                                                <img src="{{ Str::startsWith($cart->desain->file_desain, 'data:image') ? $cart->desain->file_desain : Storage::url($cart->desain->file_desain) }}" class="w-full h-full object-contain">
                                            @endif
                                        </div>
                                    @else
                                        <!-- Produk Jadi -->
                                        <div class="absolute inset-0 z-0 flex items-center justify-center bg-slate-50">
                                            @if($cart->produk->gambar_produk)
                                                <img src="{{ Storage::url($cart->produk->gambar_produk) }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-2xl">👕</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-slate-800">{{ $cart->produk->nama_produk }}</h4>
                                    <p class="text-xs text-slate-500">Tipe: {{ ucfirst($cart->produk->jenis_produk) }} 
                                        @if($cart->desain)
                                            | Warna: <span class="inline-block w-3 h-3 rounded-full border border-slate-300 translate-y-0.5 ml-1" style="background-color: {{ $cart->desain->warna_baju ?: '#ffffff' }}"></span>
                                        @endif
                                    </p>
                                    @if($cart->desain && $cart->desain->detail_sablon)
                                        <p class="text-[11px] text-slate-500 mt-1 italic"><span class="font-bold text-slate-600">Rincian:</span> {{ $cart->desain->detail_sablon }}</p>
                                    @endif
                                    @php $hargaDesain = $cart->desain ? $cart->desain->harga_desain : 0; @endphp
                                    <p class="text-sm font-semibold text-indigo-600 mt-1">Rp {{ number_format($cart->produk->harga_dasar + $hargaDesain, 0, ',', '.') }} <span class="text-xs text-slate-400">x {{ $cart->quantity }}</span></p>
                                </div>
                                <div class="text-right font-bold text-slate-700">
                                    Rp {{ number_format(($cart->produk->harga_dasar + $hargaDesain) * $cart->quantity, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Konfirmasi Pesanan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                    <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-4 mb-4">Informasi Proses Pesanan</h3>
                    
                    <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-xl mb-6">
                        <ul class="list-decimal list-inside text-sm text-slate-700 space-y-2">
                            <li>Pesanan Anda akan dikirimkan ke tim kami untuk di-review kelayakan desainnya.</li>
                            <li>Jika desain perlu diperbaiki, kami akan meminta Anda melakukan revisi.</li>
                            <li>Pembayaran baru dilakukan <span class="font-bold text-indigo-600">SETELAH</span> desain disetujui.</li>
                        </ul>
                    </div>

                    <form action="{{ route('customer.checkout.store') }}" method="POST" id="checkoutForm">
                        @csrf
                        @foreach($carts as $cart)
                            <input type="hidden" name="cart_ids[]" value="{{ $cart->id_cart }}">
                        @endforeach
                    </form>
                </div>

            </div>

            <!-- Sisi Kanan: Summary Total -->
            <div class="lg:w-1/3">
                <div class="bg-slate-800 text-white p-6 rounded-2xl shadow-xl sticky top-8">
                    <h3 class="text-lg font-bold text-slate-100 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Konfirmasi Total
                    </h3>
                    
                    <div class="space-y-3 text-sm text-slate-300 mb-6">
                        <div class="flex justify-between">
                            <span>Total Item</span>
                            <span class="font-medium text-white">{{ count($carts) }} Produk</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Subtotal Harga</span>
                            <span class="font-medium text-white">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Biaya Layanan</span>
                            <span class="font-medium text-emerald-400 text-xs py-0.5 px-2 bg-emerald-400/10 rounded">Gratis</span>
                        </div>
                    </div>
                    
                    <div class="border-t border-slate-600 pt-4 mb-8">
                        <div class="flex justify-between items-center text-lg">
                            <span class="font-bold">Total Pembayaran</span>
                            <span class="font-black text-2xl text-indigo-400 tracking-tight">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button type="submit" form="checkoutForm" class="w-full flex items-center justify-center gap-2 py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg transition transform hover:-translate-y-0.5 relative overflow-hidden group">
                        <span class="relative z-10">Konfirmasi Pesanan</span>
                        <div class="absolute inset-0 h-full w-full scale-0 rounded-xl transition-all duration-300 ease-out group-hover:scale-100 group-hover:bg-white/10"></div>
                    </button>
                    <p class="text-xs text-center text-slate-400 mt-4">Pesanan Anda akan masuk ke tahap review desain oleh Admin kami.</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
