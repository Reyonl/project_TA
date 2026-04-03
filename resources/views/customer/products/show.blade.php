<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $produk->nama_produk }} - DAILY.CO Premium Mockup</title>
        <!-- Fonts: Inter & Outfit -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .font-outfit { font-family: 'Outfit', sans-serif; }
            .glass {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.3);
            }
        </style>
    </head>
    <body class="bg-slate-50 text-slate-900 font-sans antialiased selection:bg-sky-500 selection:text-white">
        
        <!-- Navbar -->
        <nav class="w-full py-4 px-4 sm:px-6 lg:px-8 border-b border-slate-200 sticky top-0 bg-white/80 backdrop-blur-md z-[100]">
            <div class="max-w-[1400px] mx-auto flex justify-between items-center">
                <div class="flex items-center gap-12">
                    <a href="{{ route('home') }}" class="hover:opacity-80 transition duration-300">
                        <img src="{{ asset('images/logo-dailyco.png') }}" class="h-12 w-auto" alt="DAILY.CO Logo">
                    </a>
                    <!-- Breadcrumbs -->
                    <div class="hidden md:flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400">
                        <a href="{{ route('home') }}" class="hover:text-red-600 transition">Katalog</a>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span class="text-slate-600">{{ $produk->nama_produk }}</span>
                    </div>
                </div>
                <div class="flex gap-6 items-center">
                    @auth('customer')
                        <a href="{{ route('customer.dashboard') }}" class="text-xs font-bold text-slate-600 hover:text-red-600 transition uppercase tracking-widest">Dashboard</a>
                    @else
                        @auth('admin')
                            <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-slate-600 hover:text-red-600 transition uppercase tracking-widest">Admin Panel</a>
                        @else
                            <a href="{{ route('login') }}" class="text-xs font-bold text-slate-600 hover:text-red-600 transition uppercase tracking-widest">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-xs font-black bg-red-600 text-white px-5 py-2.5 rounded-xl hover:bg-red-500 transition shadow-lg shadow-red-100 uppercase tracking-widest">Mulai Desain</a>
                            @endif
                        @endauth
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Product Detail Area -->
        <main class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col lg:flex-row gap-12" 
              x-data="{ 
                  selectedColor: 'White', 
                  selectedSize: 'L',
                  selectedTechnique: 'sablon',
                  colorMap: {
                      'White': '#ffffff', 'Black': '#1e293b', 'Navy': '#1e3a8a', 
                      'Dark Grey': '#334155', 'Red': '#dc2626', 'Forest Green': '#14532d', 
                      'Maroon': '#7f1d1d', 'Military Green': '#4B5320', 'Sand': '#D2B48C',
                      'Light Blue': '#93c5fd', 'Pink': '#f472b6', 'Purple': '#6d28d9',
                      'Orange': '#f97316', 'Yellow': '#facc15', 'Teal': '#0d9488'
                  }
              }">
            
            <!-- Left Side: Gallery Viewer -->
            <div class="w-full lg:w-[60%] flex gap-4 xl:gap-6 sticky top-24 h-fit">
                <!-- Thumbnails (Vertical) -->
                <div class="hidden md:flex flex-col gap-3 w-20 xl:w-24 shrink-0">
                    @php
                        $baseImg = match($produk->jenis_produk) {
                            'kaos' => 'kaos.png',
                            'hoodie' => 'hoodie.png',
                            'topi' => 'topi.png',
                            'polo' => 'polo.png',
                            'seragam' => 'seragam.png',
                            default => 'kaos.png'
                        };
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
                <div class="flex-1 bg-slate-50/50 rounded-[3rem] overflow-hidden relative flex items-center justify-center p-8 lg:p-12 border border-slate-100 min-h-[400px] lg:min-h-[600px] group shadow-sm">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-200/20 to-transparent"></div>
                    
                    @php $maskUrl = asset('images/mockups/'.$baseImg); @endphp
                    <!-- Interactive Tinted Preview -->
                    <div class="relative w-full h-full max-h-[500px] aspect-square flex items-center justify-center group-hover:scale-105 transition-transform duration-700">
                        <!-- Shadow/Texture Layer (Transparent Overlay) -->
                        <img src="{{ $maskUrl }}" class="absolute w-full h-full object-contain mix-blend-multiply opacity-30 z-20 pointer-events-none">
                        
                        <!-- Base Colored Layer with Mask -->
                        <div class="absolute inset-0 transition-colors duration-500 z-10"
                             :style="{ 
                                 backgroundColor: colorMap[selectedColor],
                                 WebkitMaskImage: 'url({{ $maskUrl }})',
                                 maskImage: 'url({{ $maskUrl }})',
                                 WebkitMaskSize: 'contain',
                                 maskSize: 'contain',
                                 WebkitMaskPosition: 'center',
                                 maskPosition: 'center',
                                 WebkitMaskRepeat: 'no-repeat',
                                 maskRepeat: 'no-repeat'
                             }">
                        </div>
                        
                        <!-- Highlights Layer (Additive) -->
                        <img src="{{ $maskUrl }}" class="absolute w-full h-full object-contain opacity-20 mix-blend-screen z-30 pointer-events-none">
                    </div>
                    
                    <!-- Favorite Icon -->
                    <button class="absolute top-8 right-8 p-3.5 bg-white/90 backdrop-blur rounded-2xl shadow-xl shadow-slate-200/50 text-slate-300 hover:text-red-500 transition border border-slate-100 group/fav">
                        <svg class="w-6 h-6 transform group-hover/fav:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </button>
 
                    <!-- Interactive Callout -->
                    <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 flex gap-3">
                        <template x-for="i in 4">
                            <button class="w-2.5 h-2.5 rounded-full bg-slate-200 hover:bg-red-400 transition-colors"></button>
                        </template>
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

                <!-- Info Box (Premium Style) -->
                <div class="bg-red-50 border border-red-100 rounded-2xl p-6 mb-8 flex gap-4 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-2 -bottom-2 text-red-200/40 group-hover:scale-110 transition-transform duration-700">
                         <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-black text-red-900 text-sm mb-1 uppercase tracking-tight">Panduan Bahan & Ukuran</h4>
                        <p class="text-sm text-red-700/80 mb-3 leading-relaxed font-medium italic">Pastikan brand Anda mendapatkan kualitas terbaik dengan panduan presisi kami.</p>
                        <a href="#" class="text-xs font-black text-red-600 hover:text-red-800 flex items-center gap-1 uppercase tracking-widest">
                            LIHAT DETAIL
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                    </div>
                </div>

                <hr class="border-slate-200 mb-8" />

                <!-- Configurator -->
                <div class="flex flex-col gap-6">
                    <!-- Technique -->
                    <div x-show="{{ $produk->tersedia_bordir ? 'true' : 'false' }}">
                        <div class="flex justify-between items-center mb-3">
                            <span class="font-bold text-sm text-slate-800 uppercase tracking-widest">Pilih Teknik Cetak</span>
                            <a href="#" class="text-[10px] text-red-600 font-black uppercase tracking-widest border-b border-red-200">Panduan Teknik</a>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <button @click="selectedTechnique = 'sablon'" 
                                    :class="selectedTechnique === 'sablon' ? 'border-red-600 bg-red-50 text-red-700' : 'border-slate-200 bg-white text-slate-500'"
                                    class="py-3.5 px-4 rounded-xl border-2 font-black text-[10px] uppercase tracking-widest text-center transition-all">
                                Sablon Digital (DTG)
                            </button>
                            <button @click="selectedTechnique = 'bordir'" 
                                    :class="selectedTechnique === 'bordir' ? 'border-red-600 bg-red-50 text-red-700' : 'border-slate-200 bg-white text-slate-500'"
                                    class="py-3.5 px-4 rounded-xl border-2 font-black text-[10px] uppercase tracking-widest text-center transition-all">
                                Bordir (Embroidery)
                            </button>
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
                    <a :href="`{{ route('customer.designs.editor', $produk->id_produk) }}?technique=${selectedTechnique}&color=${selectedColor}&size=${selectedSize}`" class="group relative block w-full text-center py-5 rounded-2xl bg-red-600 hover:bg-red-500 text-white font-black text-xl shadow-2xl shadow-red-200 transition-all duration-300 transform active:scale-[0.98] overflow-hidden">
                        <span class="relative z-10 flex items-center justify-center gap-3">
                            MULAI DESAIN SEKARANG
                            <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </span>
                    </a>
                    
                    <p class="text-[10px] text-center text-slate-400 font-bold mt-4 uppercase tracking-widest leading-relaxed">
                        *Estimasi biaya akhir akan dikalkulasi secara otomatis saat checkout.
                    </p>
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
