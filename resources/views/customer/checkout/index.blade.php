<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Checkout Pesanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col lg:flex-row gap-8">
            
            <!-- Sisi Kiri: Daftar Keranjang & Upload Bukti Pembayaran -->
            <div class="lg:w-2/3 space-y-6">
                
                <!-- Daftar Produk Checkout -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                    <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-4 mb-4">Item yang Dibeli</h3>
                    
                    <div class="space-y-4">
                        @foreach($carts as $cart)
                            <div class="flex items-center gap-4 p-4 border border-slate-100 rounded-xl bg-slate-50">
                                <!-- Mini Thumbnail -->
                                <div class="w-16 h-16 bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden flex-shrink-0 relative flex items-center justify-center p-1">
                                    <img src="{{ Str::startsWith($cart->desain->file_desain, 'data:image') ? $cart->desain->file_desain : Storage::url($cart->desain->file_desain) }}" class="w-full h-full object-contain">
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-slate-800">{{ $cart->produk->nama_produk }}</h4>
                                    <p class="text-xs text-slate-500">Tipe: {{ ucfirst($cart->produk->jenis_produk) }} | Warna: <span class="inline-block w-3 h-3 rounded-full border border-slate-300 translate-y-0.5 ml-1" style="background-color: {{ $cart->desain->warna_baju ?: '#ffffff' }}"></span></p>
                                    <p class="text-sm font-semibold text-indigo-600 mt-1">Rp {{ number_format($cart->produk->harga_dasar + $cart->desain->harga_desain, 0, ',', '.') }} <span class="text-xs text-slate-400">x {{ $cart->quantity }}</span></p>
                                </div>
                                <div class="text-right font-bold text-slate-700">
                                    Rp {{ number_format(($cart->produk->harga_dasar + $cart->desain->harga_desain) * $cart->quantity, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Form Upload Pembayaran -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6">
                    <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-4 mb-4">Metode Pembayaran (Transfer Bank)</h3>
                    
                    <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-xl mb-6 flex items-start gap-4">
                        <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center font-bold text-indigo-600 shadow-sm shrink-0">BCA</div>
                        <div>
                            <p class="text-sm text-slate-600 mb-1">Silakan transfer sesuai total tagihan ke rekening berikut:</p>
                            <div class="flex items-center gap-3">
                                <p class="text-lg font-bold text-slate-800 font-mono tracking-wider">8273 1234 5678</p>
                                <button onclick="navigator.clipboard.writeText('827312345678'); alert('Nomor Rekening disalin!');" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium bg-white px-2 py-1 rounded border border-indigo-200 shadow-sm transition">Salin</button>
                            </div>
                            <p class="text-sm font-semibold text-slate-700 mt-1">A.N: CustomSablon Nusantara</p>
                        </div>
                    </div>

                    <form action="{{ route('customer.checkout.store') }}" method="POST" enctype="multipart/form-data" id="checkoutForm">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Upload Bukti Transfer <span class="text-red-500">*</span></label>
                            
                            <!-- Custom File Input -->
                            <div class="relative group cursor-pointer">
                                <input type="file" name="bukti_pembayaran" id="buktiInput" accept="image/png, image/jpeg, image/jpg" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewFile()">
                                <div id="dropZone" class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center bg-slate-50 group-hover:bg-slate-100 group-hover:border-indigo-400 transition duration-200">
                                    <div id="previewContainer" class="hidden flex-col items-center">
                                        <img id="imagePreview" class="max-h-40 rounded shadow-md mb-3 object-contain">
                                        <span class="text-xs text-indigo-600 font-bold bg-indigo-100 px-3 py-1 rounded-full cursor-pointer hover:bg-indigo-200 transition">Ubah File</span>
                                    </div>
                                    <div id="uploadPlaceholder">
                                        <svg class="w-10 h-10 mx-auto text-slate-400 group-hover:text-indigo-500 mb-3 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        <p class="text-sm text-slate-600"><span class="font-bold text-indigo-600">Klik untuk upload</span> atau drag and drop</p>
                                        <p class="text-xs text-slate-400 mt-1">PNG, JPG up to 2MB</p>
                                    </div>
                                </div>
                            </div>
                            @error('bukti_pembayaran')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

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
                        <span class="relative z-10">Kirim & Konfirmasi Pembayaran</span>
                        <div class="absolute inset-0 h-full w-full scale-0 rounded-xl transition-all duration-300 ease-out group-hover:scale-100 group-hover:bg-white/10"></div>
                    </button>
                    <p class="text-xs text-center text-slate-400 mt-4">Pesanan akan diproses maksimal 1x24 jam setelah bukti pembayaran valid.</p>
                </div>
            </div>

        </div>
    </div>

    <!-- Script Preview Gambar -->
    <script>
        function previewFile() {
            const input = document.getElementById('buktiInput');
            const previewContainer = document.getElementById('previewContainer');
            const imagePreview = document.getElementById('imagePreview');
            const placeholder = document.getElementById('uploadPlaceholder');
            const dropZone = document.getElementById('dropZone');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    previewContainer.classList.add('flex');
                    placeholder.classList.add('hidden');
                    dropZone.classList.add('border-indigo-400', 'bg-indigo-50/30');
                    dropZone.classList.remove('border-slate-300', 'bg-slate-50');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-app-layout>
