<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistem Visualisasi Mockup Sablon</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 text-slate-900 font-sans antialiased selection:bg-indigo-500 selection:text-white">
        <!-- Navigation -->
        <nav class="w-full py-6 px-4 sm:px-6 lg:px-8 absolute top-0 z-50">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div class="flex items-center">
                    <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-violet-600">
                        SablonQu
                    </span>
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
                                <a href="{{ route('register') }}" class="font-semibold bg-indigo-600 text-white px-5 py-2.5 rounded-lg hover:bg-indigo-700 transition shadow-md shadow-indigo-200">Daftar</a>
                            @endif
                        @endauth
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden flex items-center min-h-screen">
            <!-- Decorative background elements -->
            <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
                <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>

            <div class="mx-auto max-w-7xl px-6 lg:px-8 relative z-10 w-full">
                <div class="max-w-3xl mx-auto text-center">
                    <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 sm:text-6xl mb-6">
                        Desain Sablon Impianmu <br/> Pada <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-violet-600">Kaos & Hoodie</span>
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-slate-600 mb-10">
                        Tidak perlu jago desain grafis! Buat pesanan custom dengan editor mockup visual yang interaktif. Atur posisi, rotasi, warna, dan ukuran desain secara <i>realtime</i>!
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        @auth('customer')
                            <a href="{{ route('customer.dashboard') }}" class="rounded-xl bg-indigo-600 px-8 py-4 text-base font-semibold text-white shadow-xl shadow-indigo-200 hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all transform hover:-translate-y-1">
                                Lanjut Desain
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="rounded-xl bg-indigo-600 px-8 py-4 text-base font-semibold text-white shadow-xl shadow-indigo-200 hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all transform hover:-translate-y-1">
                                Mulai Buat Desain
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Feature Mockup preview image or decoration -->
                <div class="mt-20 sm:mt-24 w-full flex justify-center">
                    <div class="relative w-full max-w-4xl p-2 rounded-2xl bg-white border border-slate-200 shadow-2xl shadow-indigo-100 flex items-center justify-center bg-slate-50 min-h-[400px]">
                        <div class="text-slate-400 font-medium">✨ Realtime Interactive Canvas Editor (Segera Hadir) ✨</div>
                    </div>
                </div>
            </div>
            
            <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-40rem)]" aria-hidden="true">
                <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>
        </main>

        <!-- Product Recommendations Section -->
        <section class="py-24 bg-white relative z-20">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex items-end justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight sm:text-4xl">Pilihan untuk Pelanggan Baru</h2>
                        <p class="mt-2 text-lg text-slate-600">Rekomendasi terbaik berdasarkan data dan tren terkini.</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Promo Card -->
                    <div class="bg-gradient-to-br from-indigo-50 via-indigo-100/50 to-violet-100 rounded-3xl p-8 flex flex-col justify-between relative overflow-hidden shadow-sm border border-indigo-200/60 group hover:shadow-lg hover:border-indigo-300 transition duration-300">
                        <div>
                            <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-md bg-indigo-600 font-semibold text-white text-xs mb-6 shadow-sm shadow-indigo-200">
                                <svg class="w-3.5 h-3.5 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                                Rekomendasi AI
                            </span>
                            <h3 class="text-3xl font-extrabold text-indigo-900 leading-[1.15]">
                                Pilihan unggulan<br/>untuk memulai<br/>kreasi
                            </h3>
                            <p class="text-sm text-slate-600 mt-4 leading-relaxed">Pilihan ini sangat direkomendasikan berdasarkan penilaian interaksi <i>merchants</i> kami.</p>
                        </div>
                        <div class="mt-8 flex gap-2">
                            <div class="h-2 w-2 rounded-full bg-indigo-600"></div>
                            <div class="h-2 w-2 rounded-full bg-indigo-300"></div>
                            <div class="h-2 w-2 rounded-full bg-indigo-300"></div>
                        </div>
                        <!-- Background decoration inside card -->
                        <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-gradient-to-tr from-indigo-300 to-violet-300 rounded-full mix-blend-multiply filter blur-2xl opacity-50 group-hover:opacity-70 transition-opacity"></div>
                    </div>

                    <!-- Dynamic Product Cards -->
                    @foreach($produks as $item)
                    <a href="{{ route('katalog.show', $item->id_produk) }}" class="bg-white rounded-3xl p-4 border border-slate-200 shadow-sm hover:shadow-xl hover:border-indigo-100 hover:-translate-y-1 transition-all duration-300 cursor-pointer group flex flex-col">
                        <div class="relative w-full rounded-2xl mb-5 overflow-hidden flex items-center justify-center p-4">
                            <div class="absolute inset-0 bg-slate-100/80 group-hover:bg-indigo-50/50 transition-colors duration-300"></div>
                            <!-- Jika tidak ada image di DB, kita pakai default mockup dari public/images/mockups -->
                            @php
                                $imgFile = $item->jenis_produk == 'kaos' ? 'kaos.png' : 'hoodie.png';
                            @endphp
                            <img src="{{ asset('images/mockups/' . $imgFile) }}" alt="{{ $item->nama_produk }}" class="w-full h-auto object-contain z-10 group-hover:scale-110 drop-shadow-md transition-transform duration-500">
                            <!-- Favorite Btn -->
                            <button type="button" class="absolute top-4 right-4 p-2 bg-white rounded-full shadow-sm text-slate-400 hover:text-red-500 hover:shadow-md z-20 transition-all opacity-0 group-hover:opacity-100 translate-y-1 group-hover:translate-y-0" onclick="event.preventDefault();">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            </button>
                            @if($loop->first)
                            <span class="absolute bottom-4 left-4 bg-red-600 text-white text-[10px] uppercase font-bold px-2 py-0.5 rounded shadow z-20">Bestseller</span>
                            @else
                            <span class="absolute bottom-4 left-4 bg-indigo-600 text-white text-[10px] uppercase font-bold px-2 py-0.5 rounded shadow z-20">Populer</span>
                            @endif
                        </div>
                        <div class="px-1 flex flex-col flex-1">
                            <h4 class="text-slate-900 font-bold mb-1 leading-snug">{{ $item->nama_produk }}</h4>
                            <p class="text-slate-900 font-extrabold text-lg mt-auto pt-2">Rp {{ number_format($item->harga_dasar, 0, ',', '.') }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </section>
    </body>
</html>
