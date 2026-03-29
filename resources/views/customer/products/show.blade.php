<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $produk->nama_produk }} - Katalog SablonQu</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="bg-white text-slate-900 font-sans antialiased selection:bg-indigo-500 selection:text-white">
        
        <!-- Navbar -->
        <nav class="w-full py-4 px-4 sm:px-6 lg:px-8 border-b border-slate-200 sticky top-0 bg-white/95 backdrop-blur-sm z-50">
            <div class="max-w-[1400px] mx-auto flex justify-between items-center">
                <div class="flex items-center gap-12">
                    <a href="{{ route('home') }}" class="text-2xl font-black tracking-tight text-indigo-700">
                        SablonQu
                    </a>
                    <!-- Breadcrumbs -->
                    <div class="hidden md:flex items-center gap-2 text-sm font-medium text-slate-500">
                        <a href="{{ route('home') }}" class="hover:text-indigo-600 transition">Katalog Produk</a>
                        <span>/</span>
                        <a href="#" class="hover:text-indigo-600 transition capitalize">{{ $produk->jenis_produk }}</a>
                    </div>
                </div>
                <div class="flex gap-4 items-center">
                    @auth('customer')
                        <a href="{{ route('customer.dashboard') }}" class="font-semibold text-slate-600 hover:text-indigo-600 transition">Dashboard</a>
                    @else
                        @auth('admin')
                            <a href="{{ route('admin.dashboard') }}" class="font-semibold text-slate-600 hover:text-indigo-600 transition">Admin Panel</a>
                        @else
                            <a href="{{ route('login') }}" class="font-semibold text-slate-600 hover:text-indigo-600 transition">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="font-semibold bg-red-600 text-white px-5 py-2.5 rounded hover:bg-red-700 transition shadow-sm">Pesanan Baru</a>
                            @endif
                        @endauth
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Product Detail Area -->
        <main class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col lg:flex-row gap-12" x-data="{ selectedColor: 'White', selectedSize: 'L' }">
            
            <!-- Left Side: Gallery Viewer -->
            <div class="w-full lg:w-[60%] flex gap-4 xl:gap-6 sticky top-24 h-fit">
                <!-- Thumbnails (Vertical) -->
                <div class="hidden md:flex flex-col gap-3 w-20 xl:w-24 shrink-0">
                    @php
                        $baseImg = $produk->jenis_produk == 'kaos' ? 'kaos.png' : 'hoodie.png';
                    @endphp
                    <button class="w-full aspect-square border-2 border-slate-900 rounded-lg overflow-hidden bg-slate-100 flex items-center justify-center relative">
                        <img src="{{ asset('images/mockups/'.$baseImg) }}" class="w-[85%] object-contain" alt="Front Vew">
                    </button>
                    <button class="w-full aspect-square border border-slate-200 rounded-lg overflow-hidden bg-slate-100 hover:border-slate-400 transition flex items-center justify-center opacity-70 hover:opacity-100">
                        <img src="{{ asset('images/mockups/'.$baseImg) }}" class="w-[85%] object-contain" alt="Pattern" style="filter: hue-rotate(90deg);">
                    </button>
                    <button class="w-full aspect-square border border-slate-200 rounded-lg overflow-hidden bg-slate-100 hover:border-slate-400 transition flex items-center justify-center opacity-70 hover:opacity-100">
                        <img src="{{ asset('images/mockups/'.$baseImg) }}" class="w-[85%] object-contain" alt="Detail" style="transform: scale(1.5);">
                    </button>
                    <button class="w-full aspect-square border border-slate-200 rounded-lg overflow-hidden bg-slate-50 hover:border-slate-400 transition flex items-center justify-center opacity-70 hover:opacity-100 text-slate-400">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </button>
                </div>
                
                <!-- Main Image -->
                <div class="flex-1 bg-slate-100/80 rounded-xl overflow-hidden relative flex items-center justify-center p-8 lg:p-12 border border-slate-200 min-h-[400px] lg:min-h-[600px]">
                    <img src="{{ asset('images/mockups/'.$baseImg) }}" alt="{{ $produk->nama_produk }}" class="w-full h-auto max-h-[600px] object-contain drop-shadow-2xl transition duration-500 hover:scale-105">
                    
                    <!-- Favorite Icon -->
                    <button class="absolute top-6 right-6 p-2.5 bg-white/90 backdrop-blur rounded-full shadow-md text-slate-400 hover:text-red-500 transition border border-slate-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </button>
                    
                    <!-- Interactive Callout -->
                    <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex gap-2">
                        <button class="w-2.5 h-2.5 rounded-full bg-slate-800"></button>
                        <button class="w-2.5 h-2.5 rounded-full bg-slate-300"></button>
                        <button class="w-2.5 h-2.5 rounded-full bg-slate-300"></button>
                        <button class="w-2.5 h-2.5 rounded-full bg-slate-300"></button>
                    </div>
                </div>
            </div>

            <!-- Right Side: Product Details & Configurator -->
            <div class="w-full lg:w-[40%] flex flex-col pb-20">
                <!-- Title & Reviews -->
                <div class="mb-6 flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 leading-tight mb-2">{{ $produk->nama_produk }}</h1>
                        <div class="flex items-center gap-2 text-sm font-medium">
                            <div class="flex text-yellow-400">
                                @for($i=0; $i<5; $i++)
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            <span class="text-indigo-600 hover:underline cursor-pointer">4.8 (1,284 Ulasan)</span>
                        </div>
                    </div>
                </div>

                <!-- Info Box (Printful Style) -->
                <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 mb-8 flex gap-4 shadow-sm relative overflow-hidden">
                    <div class="absolute inset-y-0 left-0 w-1 bg-indigo-500"></div>
                    <div>
                        <h4 class="font-bold text-indigo-900 text-sm mb-1">Butuh bantuan tentang bahan & ukuran?</h4>
                        <p class="text-sm text-indigo-700/80 mb-2">Lihat panduan lengkap kami untuk memastikan brand Anda mendapatkan yang terbaik.</p>
                        <a href="#" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">Cari Tahu Lebih Lanjut &rarr;</a>
                    </div>
                </div>

                <hr class="border-slate-200 mb-8" />

                <!-- Configurator -->
                <div class="flex flex-col gap-6">
                    <!-- Technique -->
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <span class="font-bold text-sm text-slate-800">Teknik Cetak</span>
                            <a href="#" class="text-xs text-indigo-600 font-semibold">Panduan File</a>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <button class="py-2.5 px-4 rounded-md border-2 border-slate-900 bg-slate-50 font-bold text-sm text-center">Sablon Digital (DTG)</button>
                            <button class="py-2.5 px-4 rounded-md border border-slate-300 bg-white hover:bg-slate-50 font-medium text-sm text-slate-600 text-center transition">Bordir (Embroidery)</button>
                        </div>
                    </div>

                    <!-- Setup Type (Radio List) -->
                    <!-- Printful style options -->
                    <div class="mt-2 border border-slate-200 rounded-lg divide-y divide-slate-200">
                        <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-slate-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-4 h-4 rounded-full border-4 border-indigo-600 bg-white"></div>
                                <span class="font-medium text-sm text-slate-900">Area Cetak Standar</span>
                            </div>
                            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </label>
                        <label class="flex items-center justify-between p-3.5 cursor-pointer hover:bg-slate-50 transition opacity-60">
                            <div class="flex items-center gap-3">
                                <div class="w-4 h-4 rounded-full border border-slate-300 bg-white"></div>
                                <span class="font-medium text-sm text-slate-600 flex items-center gap-2">All-Over Print <span class="bg-red-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded">NEW</span></span>
                            </div>
                            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </label>
                    </div>

                    <!-- Colors -->
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <span class="font-bold text-sm text-slate-800">Warna Produk <span class="text-slate-500 font-normal ml-1" x-text="selectedColor"></span></span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $colors = [
                                    'White' => 'bg-white', 'Black' => 'bg-slate-900', 'Navy' => 'bg-blue-900', 
                                    'Dark Grey' => 'bg-slate-700', 'Red' => 'bg-red-600', 'Forest Green' => 'bg-green-800', 
                                    'Maroon' => 'bg-red-900', 'Military Green' => 'bg-[#4B5320]', 'Sand' => 'bg-[#D2B48C]',
                                    'Light Blue' => 'bg-blue-300', 'Pink' => 'bg-pink-400', 'Purple' => 'bg-purple-700',
                                    'Orange' => 'bg-orange-500', 'Yellow' => 'bg-yellow-400', 'Teal' => 'bg-teal-600'
                                ];
                            @endphp
                            @foreach($colors as $name => $class)
                                <button x-on:click="selectedColor = '{{ $name }}'"
                                        x-bind:class="{ 'ring-2 ring-indigo-600 scale-110 z-10': selectedColor === '{{ $name }}', 'ring-1 ring-slate-200': selectedColor !== '{{ $name }}' }"
                                        class="w-6 h-6 sm:w-8 sm:h-8 rounded {{ $class }} hover:scale-110 transition cursor-pointer shadow-sm relative group"
                                        title="{{ $name }}">
                                    <!-- Tooltip hover -->
                                    <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-slate-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 pointer-events-none transition whitespace-nowrap z-20">
                                        {{ $name }}
                                    </div>
                                    @if($name == 'White')
                                      <!-- Untuk outline border -->
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Sizes -->
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <span class="font-bold text-sm text-slate-800">Ukuran</span>
                            <a href="#" class="text-xs text-indigo-600 font-semibold hover:underline border-b border-indigo-200">Size guide</a>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['XS','S','M','L','XL','2XL','3XL','4XL'] as $size)
                                <button x-on:click="selectedSize = '{{ $size }}'"
                                        x-bind:class="{ 'border-slate-900 text-slate-900 font-bold bg-slate-50': selectedSize === '{{ $size }}', 'border-slate-200 text-slate-600 bg-white hover:border-slate-400': selectedSize !== '{{ $size }}' }"
                                        class="w-10 h-10 sm:w-11 sm:h-11 rounded border flex items-center justify-center text-sm transition">
                                    {{ $size }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <hr class="border-slate-200 my-8" />

                <!-- Price and Fulfillment -->
                <div class="bg-slate-50 rounded-xl p-5 border border-slate-100 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Harga Dasar</p>
                        <div class="text-3xl font-black text-slate-900">
                            Rp {{ number_format($produk->harga_dasar, 0, ',', '.') }}
                        </div>
                    </div>
                    <!-- Info Printful -->
                    <div class="sm:text-right border-l-2 border-slate-200 pl-4 sm:border-none sm:pl-0">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1 text-left sm:text-right">Fulfillment</p>
                        <p class="text-sm text-slate-700 font-medium">Estimasi 2-5 hari kerja</p>
                    </div>
                </div>

                <!-- Call to Action -->
                <div class="mt-auto">
                    <a href="{{ route('customer.designs.editor', $produk->id_produk) }}" class="block w-full text-center py-4 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold text-lg shadow-lg shadow-red-200 hover:-translate-y-1 transition transform active:scale-95 duration-200 mb-3">
                        Mulai Desain Sekarang
                    </a>
                    
                    <p class="text-xs text-center text-slate-500 font-medium">*Harga sablon dan biaya pengiriman dihitung saat Check Out.</p>
                </div>

                <div class="mt-6 border border-slate-200 rounded-lg p-4 bg-white shadow-sm flex items-center justify-between">
                    <div>
                        <h5 class="text-sm font-bold text-slate-800 mb-0.5">Diskon Partai Besar</h5>
                        <p class="text-xs text-slate-500">Hemat hingga 20% untuk pembelian grosir.</p>
                    </div>
                    <button class="px-3 py-1.5 bg-slate-100 font-semibold text-xs text-slate-700 rounded hover:bg-slate-200 transition">Lihat Promo</button>
                </div>
            </div>
        </main>
    </body>
</html>
