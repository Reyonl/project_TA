<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Konfirmasi Pesanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl shadow-indigo-100/50 sm:rounded-2xl border border-slate-100">
                <div class="p-8 md:p-12 lg:flex gap-12">
                    
                    <!-- Ringkasan Produk & Desain -->
                    <div class="lg:w-1/2 flex flex-col gap-6 mb-8 lg:mb-0">
                        <h3 class="text-2xl font-bold text-slate-800 border-b pb-4">Ringkasan Desain</h3>
                        
                        <!-- Preview Desain Hasil Canvas (Composite) -->
                        <div class="bg-slate-100 p-6 rounded-2xl flex items-center justify-center border border-slate-200">
                            <div class="relative w-[300px] h-[375px] rounded-xl overflow-hidden flex items-center justify-center shadow-inner bg-slate-50">
                                @if($produk->jenis_produk == 'kaos')
                                    <!-- Base Foto Kaos Nyata -->
                                    <img src="{{ asset('images/mockups/kaos.png?v='.time()) }}" class="absolute w-[85%] h-[85%] object-contain drop-shadow-lg opacity-90 z-0">
                                    
                                    <!-- Layer Masking Warna Kaos -->
                                    <div class="absolute w-[85%] h-[85%] mix-blend-multiply z-10"
                                         style="-webkit-mask-image: url('{{ asset('images/mockups/kaos.png?v='.time()) }}'); -webkit-mask-size: contain; -webkit-mask-position: center; -webkit-mask-repeat: no-repeat; mask-image: url('{{ asset('images/mockups/kaos.png?v='.time()) }}'); mask-size: contain; mask-position: center; mask-repeat: no-repeat;">
                                        <div class="w-full h-full" style="background-color: {{ $desain->warna_baju ?? '#ffffff' }};"></div>
                                    </div>
                                @elseif($produk->jenis_produk == 'hoodie')
                                    <!-- Base Foto Hoodie Nyata -->
                                    <img src="{{ asset('images/mockups/hoodie.png?v='.time()) }}" class="absolute w-[85%] h-[85%] object-contain drop-shadow-lg opacity-90 z-0">
                                    
                                    <!-- Layer Masking Warna Hoodie -->
                                    <div class="absolute w-[85%] h-[85%] mix-blend-multiply z-10"
                                         style="-webkit-mask-image: url('{{ asset('images/mockups/hoodie.png?v='.time()) }}'); -webkit-mask-size: contain; -webkit-mask-position: center; -webkit-mask-repeat: no-repeat; mask-image: url('{{ asset('images/mockups/hoodie.png?v='.time()) }}'); mask-size: contain; mask-position: center; mask-repeat: no-repeat;">
                                        <div class="w-full h-full" style="background-color: {{ $desain->warna_baju ?? '#ffffff' }};"></div>
                                    </div>
                                @endif

                                <!-- Canvas Design Print Area -->
                                <div class="absolute z-20" style="top: 20%; left: 27.08%; width: 45.83%; height: 53.33%;">
                                    <img src="{{ Str::startsWith($desain->file_desain, 'data:image') ? $desain->file_desain : Storage::url($desain->file_desain) }}" class="w-full h-full object-contain">
                                </div>
                            </div>
                        </div>

                        <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                            <h4 class="font-bold text-indigo-900 mb-1">{{ $produk->nama_produk }}</h4>
                            <p class="text-sm text-indigo-700 capitalize">Tipe: {{ $produk->jenis_produk }}</p>
                        </div>
                    </div>

                    <!-- Rincian Harga & Form -->
                    <div class="lg:w-1/2 flex flex-col justify-between">
                        <form method="POST" action="{{ route('customer.checkout.store') }}" id="checkoutForm">
                            @csrf
                            <input type="hidden" name="id_produk" value="{{ $produk->id_produk }}">
                            <input type="hidden" name="id_desain" value="{{ $desain->id_desain }}">

                            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 mb-8">
                                <h3 class="text-xl font-bold text-slate-800 mb-6">Rincian Harga</h3>
                                
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-slate-600 font-medium">Harga Dasar Produk</span>
                                    <span class="text-slate-800 font-bold" id="hargaProduk" data-harga="{{ $produk->harga_dasar }}">Rp {{ number_format($produk->harga_dasar, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-slate-600 font-medium">Biaya Sablon Custom</span>
                                    <span class="text-slate-800 font-bold" id="hargaDesain" data-harga="{{ $desain->harga_desain }}">Rp {{ number_format($desain->harga_desain, 0, ',', '.') }}</span>
                                </div>
                                
                                <hr class="my-4 border-slate-300 border-dashed">

                                <div class="flex items-center justify-between mb-6">
                                    <span class="text-slate-700 font-bold">Kuantitas</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" id="btnMinus" class="w-8 h-8 rounded-full bg-slate-200 hover:bg-slate-300 text-slate-700 flex items-center justify-center font-bold transition">-</button>
                                        <input type="number" name="quantity" id="quantityInput" value="1" min="1" class="w-16 text-center border-slate-300 rounded-md font-bold focus:border-indigo-500 focus:ring-indigo-500">
                                        <button type="button" id="btnPlus" class="w-8 h-8 rounded-full bg-indigo-100 hover:bg-indigo-200 text-indigo-700 flex items-center justify-center font-bold transition">+</button>
                                    </div>
                                    @error('quantity')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex justify-between items-center bg-indigo-600 text-white p-4 rounded-xl shadow-inner my-2">
                                    <span class="text-indigo-100 font-semibold text-lg">Total Harga</span>
                                    <span class="text-2xl font-black" id="totalHarga">Rp 0</span>
                                </div>
                                <p class="text-xs text-slate-400 mt-2 text-center">*Total belum termasuk ongkos kirim. Sistem pembayaran akan dikonfirmasi kemudian oleh Admin.</p>
                            </div>

                            <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white text-lg font-bold rounded-xl shadow-lg shadow-indigo-200 transition transform hover:-translate-y-1">
                                Konfirmasi Order Sekarang
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Script Kalkulasi Harga -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hProduk = parseInt(document.getElementById('hargaProduk').getAttribute('data-harga'));
            const hDesain = parseInt(document.getElementById('hargaDesain').getAttribute('data-harga'));
            const qtyInput = document.getElementById('quantityInput');
            const totalEl = document.getElementById('totalHarga');
            
            const btnPlus = document.getElementById('btnPlus');
            const btnMinus = document.getElementById('btnMinus');

            function formatRupiah(angka) {
                return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function hitungTotal() {
                let qty = parseInt(qtyInput.value);
                if(isNaN(qty) || qty < 1) {
                    qty = 1;
                    qtyInput.value = 1;
                }
                const total = (hProduk + hDesain) * qty;
                totalEl.textContent = formatRupiah(total);
            }

            btnPlus.addEventListener('click', () => {
                qtyInput.value = parseInt(qtyInput.value) + 1;
                hitungTotal();
            });

            btnMinus.addEventListener('click', () => {
                if(parseInt(qtyInput.value) > 1) {
                    qtyInput.value = parseInt(qtyInput.value) - 1;
                    hitungTotal();
                }
            });

            qtyInput.addEventListener('input', hitungTotal);

            // Hitung awal
            hitungTotal();
        });
    </script>
</x-app-layout>
