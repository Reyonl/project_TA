<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Keranjang Belanja') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-600 px-4 py-3 rounded-lg flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="lg:flex gap-8">
                <!-- Data Keranjang -->
                <div class="lg:w-2/3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-4 mb-4">Item Desain Anda</h3>

                            @forelse($carts as $cart)
                                <div class="flex flex-col sm:flex-row gap-6 p-4 bg-slate-50 border border-slate-100 rounded-xl mb-4 relative group hover:shadow-md transition">
                                    
                                    <!-- Thumbnail Desain -->
                                    <div class="w-full sm:w-32 aspect-[3/4] bg-white rounded-lg shadow-inner overflow-hidden flex items-center justify-center relative flex-shrink-0">
                                        @php
                                            $bajuType = $cart->produk->jenis_produk;
                                            $bajuColor = $cart->desain->warna_baju ?: '#ffffff';
                                            $mockupUrl = asset('images/mockups/' . $bajuType . '.png?v='.time());
                                            $mockupUrlB = asset('images/mockups/' . $bajuType . '_belakang.png?v='.time());
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
                                            <img src="{{ Str::startsWith($cart->desain->file_desain, 'data:image') ? $cart->desain->file_desain : Storage::url($cart->desain->file_desain) }}" class="w-full h-full object-contain">
                                        </div>

                                        @if($cart->desain->file_desain_belakang)
                                        <span class="absolute bottom-1 right-1 bg-indigo-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow shadow-indigo-200">2 Sisi</span>
                                        @endif
                                    </div>

                                    <!-- Detail Info -->
                                    <div class="flex-1 flex flex-col justify-between">
                                        <div>
                                            <h4 class="font-bold text-slate-800 text-lg">{{ $cart->produk->nama_produk }}</h4>
                                            <div class="flex items-center gap-4 mt-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                                    {{ ucfirst($cart->produk->jenis_produk) }}
                                                </span>
                                                <div class="flex items-center gap-1.5 text-xs text-slate-600 font-medium">
                                                    Warna: 
                                                    <span class="w-4 h-4 rounded-full border border-slate-300 shadow-sm" style="background-color: {{ $bajuColor }}"></span>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-4 grid grid-cols-2 gap-x-4 gap-y-1 text-sm">
                                                <p class="text-slate-500">Harga Dasar: <span class="font-semibold text-slate-700">Rp {{ number_format($cart->produk->harga_dasar, 0, ',', '.') }}</span></p>
                                                <p class="text-slate-500">Biaya Sablon: <span class="font-semibold text-slate-700">Rp {{ number_format($cart->desain->harga_desain, 0, ',', '.') }}</span></p>
                                                <p class="col-span-2 text-indigo-600 font-bold mt-1">Subtotal per item: Rp {{ number_format($cart->produk->harga_dasar + $cart->desain->harga_desain, 0, ',', '.') }}</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between mt-4 sm:mt-0">
                                            <!-- Kuantitas -->
                                            <div class="flex items-center gap-3">
                                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Qty</label>
                                                <input type="number" min="1" value="{{ $cart->quantity }}" onchange="updateCartQuantity({{ $cart->id_cart }}, this.value)" class="w-20 text-center rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold">
                                            </div>

                                            <!-- Hapus -->
                                            <form action="{{ route('customer.cart.destroy', $cart->id_cart) }}" method="POST" onsubmit="return confirm('Hapus produk dari keranjang?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus Item">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12 px-6">
                                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-slate-800 mb-2">Keranjang Anda Kosong</h3>
                                    <p class="text-slate-500 mb-6">Mulai desain kaos atau hoodie impian Anda sekarang.</p>
                                    <a href="{{ route('customer.dashboard') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl shadow shadow-indigo-200 transition">
                                        Eksplorasi Produk
                                    </a>
                                </div>
                            @endforelse

                        </div>
                    </div>
                </div>

                <!-- Ringkasan Checkout -->
                <div class="lg:w-1/3 mt-8 lg:mt-0 @if(count($carts) === 0) hidden @endif">
                    <div class="bg-indigo-50 border border-indigo-100 p-6 rounded-2xl sticky top-8 shadow-sm">
                        <h3 class="text-xl font-bold text-indigo-900 mb-6 border-b border-indigo-200 pb-4">Ringkasan Pesanan</h3>
                        
                        <div class="space-y-4 mb-6">
                            @php $netTotal = 0; @endphp
                            @foreach($carts as $c)
                                @php 
                                    $itemTotal = ($c->produk->harga_dasar + $c->desain->harga_desain) * $c->quantity;
                                    $netTotal += $itemTotal;
                                @endphp
                                <div class="flex justify-between items-start text-sm">
                                    <span class="text-indigo-800">{{ $c->produk->nama_produk }} <span class="font-bold text-xs bg-indigo-200 text-indigo-800 px-1.5 rounded-md ml-1">x{{ $c->quantity }}</span></span>
                                    <span class="text-indigo-900 font-medium whitespace-nowrap">Rp {{ number_format($itemTotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="border-t border-indigo-200 border-dashed pt-4 mb-8">
                            <div class="flex justify-between items-center text-lg">
                                <span class="font-bold text-indigo-900">Total Pembayaran</span>
                                <span class="font-black text-indigo-600 text-xl tracking-tight">Rp {{ number_format($netTotal, 0, ',', '.') }}</span>
                            </div>
                            <p class="text-xs text-indigo-400 mt-2">*Belum termasuk ongkir.</p>
                        </div>

                        <a href="{{ route('customer.checkout.index') }}" class="w-full block text-center py-4 bg-indigo-600 hover:bg-indigo-700 text-white text-lg font-bold rounded-xl shadow-lg shadow-indigo-200 transition transform hover:-translate-y-0.5">
                            Checkout Sekarang ({{ count($carts) }})
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Script AJAX untuk real-time update Qty -->
    <script>
        function updateCartQuantity(cartId, quantity) {
            fetch(`/customer/cart/${cartId}/quantity`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.location.reload(); // Reload halaman untuk menghitung ulang subtotal PHP
                } else {
                    alert('Gagal memperbarui kuantitas');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan jaringan.');
            });
        }
    </script>
</x-app-layout>
