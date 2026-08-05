<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Keranjang Belanja') }}
        </h2>
    </x-slot>

    <div class="py-12 pb-32 lg:pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-600 px-4 py-3 rounded-lg flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('customer.checkout.index') }}" method="GET" id="cartForm"></form>

            <div class="lg:flex gap-8">
                <!-- Data Keranjang -->
                <div class="lg:w-2/3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                        <div class="p-6">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                                <h3 class="text-lg font-bold text-slate-800">Item Desain Anda</h3>
                                @if(count($carts) > 0)
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="selectAllCheckbox" checked class="w-5 h-5 accent-indigo-600 cursor-pointer">
                                    <label for="selectAllCheckbox" class="text-xs font-bold text-slate-500 uppercase tracking-wider cursor-pointer">Pilih Semua</label>
                                </div>
                                @endif
                            </div>

                            @forelse($carts as $cart)
                                <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 p-4 bg-slate-50 border border-slate-100 rounded-xl mb-4 relative group hover:shadow-md transition">
                                    
                                    <div class="flex items-start sm:items-center gap-4">
                                        <!-- Checkbox Select Item -->
                                        <div class="flex items-center justify-center pt-2 sm:pt-0 sm:pr-2">
                                            <input type="checkbox" name="cart_ids[]" value="{{ $cart->id_cart }}" form="cartForm" checked class="cart-item-checkbox w-6 h-6 accent-indigo-600 cursor-pointer" data-price="{{ ($cart->produk->harga_dasar + ($cart->desain ? $cart->desain->harga_desain : 0)) * $cart->quantity }}">
                                        </div>
                                        
                                        <!-- Thumbnail Desain -->
                                        <div class="w-24 sm:w-32 aspect-[3/4] bg-white rounded-lg shadow-inner overflow-hidden flex items-center justify-center relative flex-shrink-0 @if($cart->desain) cursor-pointer group/thumb ring-1 ring-slate-200 hover:ring-indigo-500 transition @endif" @if($cart->desain) onclick="openPreviewModal({{ $cart->id_cart }})" @endif>

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
                                                <img src="{{ Str::startsWith($cart->desain->file_desain, 'data:image') ? $cart->desain->file_desain : Storage::url($cart->desain->file_desain) }}" class="w-full h-full object-contain">
                                            </div>

                                            @php 
                                                $sisiCount = 1;
                                                if($cart->desain->file_desain_belakang) $sisiCount++;
                                                if($cart->desain->file_desain_kiri) $sisiCount++;
                                                if($cart->desain->file_desain_kanan) $sisiCount++;
                                            @endphp
                                            @if($sisiCount > 1)
                                            <span class="absolute bottom-1 right-1 bg-indigo-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow shadow-indigo-200 z-30">{{ $sisiCount }} Sisi</span>
                                            @endif
                                            
                                            <!-- Hover Preview Icon -->
                                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover/thumb:opacity-100 transition-opacity z-40">
                                                <svg class="w-8 h-8 text-white drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                            </div>

                                        @else
                                            <!-- Produk Jadi -->
                                            <div class="absolute inset-0 z-0 flex items-center justify-center bg-slate-50">
                                                @if($cart->produk->gambar_produk)
                                                    <img src="{{ Storage::url($cart->produk->gambar_produk) }}" class="w-full h-full object-cover">
                                                @else
                                                    <span class="text-4xl">👕</span>
                                                @endif
                                            </div>
                                        @endif
                                        </div>
                                    </div>

                                    <!-- Detail Info -->
                                    <div class="flex-1 flex flex-col justify-between">
                                        <div>
                                            <h4 class="font-bold text-slate-800 text-lg">{{ $cart->produk->nama_produk }}</h4>
                                            <div class="flex items-center gap-4 mt-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-slate-900 text-white">
                                                    {{ $cart->produk->jenis_produk }}
                                                </span>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-sky-500 text-white">
                                                    Sablon
                                                </span>
                                                @if($cart->desain)
                                                    <div class="flex items-center gap-1.5 text-xs text-slate-600 font-medium">
                                                        Warna: 
                                                        <span class="w-4 h-4 rounded-full border border-slate-300 shadow-sm" style="background-color: {{ $cart->desain->warna_baju ?: '#ffffff' }}"></span>
                                                        <span class="uppercase text-[10px] tracking-wider text-slate-400">{{ $cart->desain->warna_baju ?: '#FFFFFF' }}</span>
                                                    </div>
                                                @endif
                                                @if($cart->desain && $cart->desain->detail_sablon)
                                                    <div class="w-full text-xs text-slate-500 mt-2 bg-white/70 border border-slate-200/50 p-2 rounded-lg col-span-2">
                                                        <span class="font-bold text-slate-600">Rincian Sablon:</span> {{ $cart->desain->detail_sablon }}
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="mt-4 grid grid-cols-2 gap-x-4 gap-y-1 text-sm">
                                                <p class="text-slate-500">Harga Dasar: <span class="font-semibold text-slate-700">Rp {{ number_format($cart->produk->harga_dasar, 0, ',', '.') }}</span></p>
                                                @if($cart->desain)
                                                    <p class="text-slate-500">Biaya Sablon: <span class="font-semibold text-slate-700">Rp {{ number_format($cart->desain->harga_desain, 0, ',', '.') }}</span></p>
                                                    <p class="col-span-2 text-indigo-600 font-bold mt-1">Subtotal per item: Rp {{ number_format($cart->produk->harga_dasar + $cart->desain->harga_desain, 0, ',', '.') }}</p>
                                                @else
                                                    <p class="col-span-2 text-indigo-600 font-bold mt-1">Subtotal per item: Rp {{ number_format($cart->produk->harga_dasar, 0, ',', '.') }}</p>
                                                @endif
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
                <div class="lg:w-1/3 mt-8 lg:mt-0 @if(count($carts) === 0) hidden @endif fixed bottom-0 left-0 w-full lg:static z-40">
                    <div class="bg-white/95 lg:bg-indigo-50 backdrop-blur-md border-t lg:border border-indigo-100 p-4 sm:p-6 rounded-t-2xl lg:rounded-2xl lg:sticky lg:top-8 shadow-[0_-10px_15px_-3px_rgba(0,0,0,0.1)] lg:shadow-sm">
                        <h3 class="hidden lg:block text-xl font-bold text-indigo-900 mb-6 border-b border-indigo-200 pb-4">Ringkasan Pesanan</h3>
                        
                        <div class="hidden lg:block space-y-4 mb-6">
                            @php $netTotal = 0; @endphp
                            @foreach($carts as $c)
                                @php 
                                    $hargaDesain = $c->desain ? $c->desain->harga_desain : 0;
                                    $itemTotal = ($c->produk->harga_dasar + $hargaDesain) * $c->quantity;
                                    $netTotal += $itemTotal;
                                @endphp
                                <div class="flex justify-between items-start text-sm summary-item" id="summary-cart-{{ $c->id_cart }}">
                                    <span class="text-indigo-800">{{ $c->produk->nama_produk }} <span class="font-bold text-xs bg-indigo-200 text-indigo-800 px-1.5 rounded-md ml-1">x{{ $c->quantity }}</span></span>
                                    <span class="text-indigo-900 font-medium whitespace-nowrap">Rp {{ number_format($itemTotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="flex flex-row lg:flex-col lg:border-t lg:border-indigo-200 lg:border-dashed lg:pt-4 mb-4 lg:mb-8 items-center lg:items-stretch justify-between gap-4">
                            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center text-lg">
                                <span class="font-bold text-indigo-900 text-sm lg:text-lg">Total Pembayaran</span>
                                <span class="font-black text-indigo-600 text-lg lg:text-xl tracking-tight" id="totalHargaVal">Rp {{ number_format($netTotal ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <p class="hidden lg:block text-xs text-indigo-400 mt-2">*Belum termasuk ongkir.</p>
                            
                            <div class="w-1/2 lg:w-full lg:mt-6">
                                <button type="submit" form="cartForm" id="checkoutBtn" class="w-full block text-center py-3 lg:py-4 bg-indigo-600 hover:bg-indigo-700 text-white text-base lg:text-lg font-bold rounded-xl shadow-lg shadow-indigo-200 transition transform hover:-translate-y-0.5 whitespace-nowrap">
                                    Checkout (<span id="selectedCount">{{ count($carts) }}</span>)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modals Preview Desain -->
    @foreach($carts as $cart)
        @if($cart->desain)
            <div id="previewModal-{{ $cart->id_cart }}" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 transition-opacity">
                <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] flex flex-col overflow-hidden transform scale-95 transition-transform duration-300" id="previewModalInner-{{ $cart->id_cart }}">
                    <!-- Header Modal -->
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white z-10 shadow-sm">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">Preview Desain Full View</h3>
                            <p class="text-sm text-slate-500">{{ $cart->produk->nama_produk }} - <span class="uppercase">{{ $cart->desain->warna_baju ?: 'Putih' }}</span></p>
                        </div>
                        <button onclick="closePreviewModal({{ $cart->id_cart }})" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <!-- Body Modal (Slider) -->
                    <div class="flex-1 bg-slate-100 relative overflow-hidden flex flex-col">
                        @php
                            $bajuTypeModal = $cart->produk->jenis_produk;
                            $bajuColorModal = $cart->desain->warna_baju ?: '#ffffff';
                            $isPanjangModal = \Illuminate\Support\Str::contains(strtolower($cart->produk->nama_produk), 'panjang');
                            $mockupBaseModal = match($bajuTypeModal) {
                                'kaos' => $isPanjangModal ? 'kaos_panjang' : 'kaos',
                                'hoodie' => 'hoodie',
                                'topi' => 'topi',
                                'polo' => 'polo',
                                'seragam' => 'seragam',
                                default => 'kaos'
                            };
                            
                            $sides = [
                                ['name' => 'Depan', 'file' => $cart->desain->file_desain, 'base' => $mockupBaseModal],
                                ['name' => 'Belakang', 'file' => $cart->desain->file_desain_belakang, 'base' => $mockupBaseModal . '_belakang'],
                                ['name' => 'Kiri', 'file' => $cart->desain->file_desain_kiri, 'base' => $mockupBaseModal . '_samping_kiri'],
                                ['name' => 'Kanan', 'file' => $cart->desain->file_desain_kanan, 'base' => $mockupBaseModal . '_samping_kanan'],
                            ];
                            
                            $activeSides = array_filter($sides, function($s) { return !empty($s['file']); });
                            $activeSides = array_values($activeSides);
                            $totalSides = count($activeSides);
                        @endphp
                        
                        <div class="relative w-full h-[60vh] md:h-[70vh] flex items-center justify-center">
                            @foreach($activeSides as $index => $side)
                                @php 
                                    $mockupPath = 'images/mockups/' . $side['base'] . '.png';
                                    $fallbackPath = 'images/mockups/' . $mockupBaseModal . '.png';
                                    $mockupUrlModal = file_exists(public_path($mockupPath)) ? asset($mockupPath) : asset($fallbackPath);
                                    
                                    $designUrlModal = Str::startsWith($side['file'], 'data:image') ? $side['file'] : Storage::url($side['file']);
                                @endphp
                                
                                <div id="slide-{{ $cart->id_cart }}-{{ $index }}" class="absolute inset-0 w-full h-full transition-opacity duration-300 ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }} flex flex-col items-center justify-center p-4 sm:p-8">
                                    
                                    <h4 class="absolute top-4 sm:top-8 font-bold text-slate-700 uppercase tracking-widest text-sm px-6 py-2 bg-white/90 backdrop-blur rounded-full shadow-sm z-30">{{ $side['name'] }}</h4>
                                    
                                    <div class="w-full max-w-md aspect-[3/4] relative flex items-center justify-center mt-6">
                                        <!-- Mockup Base -->
                                        <div class="absolute inset-0 z-0">
                                            <div class="w-full h-full flex items-center justify-center relative overflow-hidden">
                                                <img src="{{ $mockupUrlModal }}" class="absolute w-[85%] h-[85%] object-contain drop-shadow-xl opacity-90 z-0">
                                                <div class="absolute w-[85%] h-[85%] mix-blend-multiply z-10"
                                                     style="-webkit-mask-image: url('{{ $mockupUrlModal }}'); -webkit-mask-size: contain; -webkit-mask-position: center; -webkit-mask-repeat: no-repeat; mask-image: url('{{ $mockupUrlModal }}'); mask-size: contain; mask-position: center; mask-repeat: no-repeat;">
                                                    <div class="w-full h-full" style="background-color: {{ $bajuColorModal }};"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Design Overlay -->
                                        <div class="absolute z-20 drop-shadow-md" style="top: 20%; left: 27.08%; width: 45.83%; height: 53.33%;">
                                            <img src="{{ $designUrlModal }}" class="w-full h-full object-contain">
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if($totalSides > 1)
                                <!-- Arrows -->
                                <button onclick="changeSlide({{ $cart->id_cart }}, -1, {{ $totalSides }})" class="absolute left-2 sm:left-8 top-1/2 -translate-y-1/2 z-30 p-3 sm:p-4 bg-white/80 hover:bg-white rounded-full shadow-lg text-slate-800 transition transform hover:scale-110">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <button onclick="changeSlide({{ $cart->id_cart }}, 1, {{ $totalSides }})" class="absolute right-2 sm:right-8 top-1/2 -translate-y-1/2 z-30 p-3 sm:p-4 bg-white/80 hover:bg-white rounded-full shadow-lg text-slate-800 transition transform hover:scale-110">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                                
                                <!-- Indicators -->
                                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-30 flex gap-2">
                                    @for($i = 0; $i < $totalSides; $i++)
                                        <div id="indicator-{{ $cart->id_cart }}-{{ $i }}" class="h-2.5 rounded-full transition-all {{ $i === 0 ? 'bg-indigo-600 w-6' : 'bg-slate-300 w-2.5' }}"></div>
                                    @endfor
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Script AJAX untuk real-time update Qty & Skenario Pilih Item -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.cart-item-checkbox');
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const selectedCountSpan = document.getElementById('selectedCount');
            const checkoutBtn = document.getElementById('checkoutBtn');
            const totalHargaVal = document.getElementById('totalHargaVal');

            function updateSummary() {
                let total = 0;
                let count = 0;

                checkboxes.forEach(cb => {
                    const cartId = cb.value;
                    const price = parseFloat(cb.getAttribute('data-price')) || 0;
                    const summaryRow = document.getElementById('summary-cart-' + cartId);

                    if (cb.checked) {
                        total += price;
                        count++;
                        if (summaryRow) summaryRow.style.display = 'flex';
                    } else {
                        if (summaryRow) summaryRow.style.display = 'none';
                    }
                });

                if (selectedCountSpan) selectedCountSpan.textContent = count;
                if (totalHargaVal) totalHargaVal.textContent = 'Rp ' + total.toLocaleString('id-ID');

                if (checkoutBtn) {
                    if (count === 0) {
                        checkoutBtn.disabled = true;
                        checkoutBtn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700', 'cursor-pointer');
                        checkoutBtn.classList.add('bg-slate-400', 'cursor-not-allowed');
                    } else {
                        checkoutBtn.disabled = false;
                        checkoutBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-700', 'cursor-pointer');
                        checkoutBtn.classList.remove('bg-slate-400', 'cursor-not-allowed');
                    }
                }

                if (selectAllCheckbox) {
                    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                    const someChecked = Array.from(checkboxes).some(cb => cb.checked);
                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;
                }
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateSummary);
            });

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    checkboxes.forEach(cb => {
                        cb.checked = selectAllCheckbox.checked;
                    });
                    updateSummary();
                });
            }

            updateSummary();
        });

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

        // Preview Modal Functions
        function openPreviewModal(cartId) {
            const modal = document.getElementById('previewModal-' + cartId);
            const inner = document.getElementById('previewModalInner-' + cartId);
            if (modal && inner) {
                modal.classList.remove('hidden');
                // Trigger reflow for animation
                void modal.offsetWidth;
                inner.classList.remove('scale-95');
                inner.classList.add('scale-100');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            }
        }

        function closePreviewModal(cartId) {
            const modal = document.getElementById('previewModal-' + cartId);
            const inner = document.getElementById('previewModalInner-' + cartId);
            if (modal && inner) {
                inner.classList.remove('scale-100');
                inner.classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                    
                    // Reset slide to 0 when closed
                    if (window.currentSlides && window.currentSlides[cartId]) {
                        const allSlides = modal.querySelectorAll('[id^="slide-'+cartId+'-"]');
                        const allInd = modal.querySelectorAll('[id^="indicator-'+cartId+'-"]');
                        allSlides.forEach((el, idx) => {
                            if(idx === 0) { el.classList.remove('opacity-0','z-0'); el.classList.add('opacity-100','z-10'); }
                            else { el.classList.remove('opacity-100','z-10'); el.classList.add('opacity-0','z-0'); }
                        });
                        allInd.forEach((el, idx) => {
                            if(idx === 0) { el.classList.remove('bg-slate-300', 'w-2.5'); el.classList.add('bg-indigo-600','w-6'); }
                            else { el.classList.remove('bg-indigo-600','w-6'); el.classList.add('bg-slate-300', 'w-2.5'); }
                        });
                        window.currentSlides[cartId] = 0;
                    }
                }, 200); // Wait for transition
            }
        }

        // Slide logic
        window.currentSlides = {};

        function changeSlide(cartId, direction, total) {
            if (window.currentSlides[cartId] === undefined) {
                window.currentSlides[cartId] = 0;
            }
            
            let currentIndex = window.currentSlides[cartId];
            
            // Hide current
            const currentElem = document.getElementById(`slide-${cartId}-${currentIndex}`);
            const currentInd = document.getElementById(`indicator-${cartId}-${currentIndex}`);
            if (currentElem) {
                currentElem.classList.remove('opacity-100', 'z-10');
                currentElem.classList.add('opacity-0', 'z-0');
            }
            if (currentInd) {
                currentInd.classList.remove('bg-indigo-600', 'w-6');
                currentInd.classList.add('bg-slate-300', 'w-2.5');
            }
            
            // Calculate next
            currentIndex = (currentIndex + direction + total) % total;
            window.currentSlides[cartId] = currentIndex;
            
            // Show next
            const nextElem = document.getElementById(`slide-${cartId}-${currentIndex}`);
            const nextInd = document.getElementById(`indicator-${cartId}-${currentIndex}`);
            if (nextElem) {
                nextElem.classList.remove('opacity-0', 'z-0');
                nextElem.classList.add('opacity-100', 'z-10');
            }
            if (nextInd) {
                nextInd.classList.remove('bg-slate-300', 'w-2.5');
                nextInd.classList.add('bg-indigo-600', 'w-6');
            }
        }

        // Close modal on click outside
        window.addEventListener('click', function(e) {
            if (e.target.id && e.target.id.startsWith('previewModal-')) {
                const cartId = e.target.id.replace('previewModal-', '');
                closePreviewModal(cartId);
            }
        });
    </script>
</x-app-layout>
