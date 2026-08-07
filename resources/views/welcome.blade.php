<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>DAILY.CO - Visualisasi Mockup Sablon Premium</title>
        <link rel="icon" href="{{ asset('images/logo-dailyco.png') }}" type="image/png">

        <!-- Fonts: Inter & Outfit -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            .font-outfit { font-family: 'Outfit', sans-serif; }
            @keyframes blob {
                0% { transform: translate(0px, 0px) scale(1); }
                33% { transform: translate(30px, -50px) scale(1.1); }
                66% { transform: translate(-20px, 20px) scale(0.9); }
                100% { transform: translate(0px, 0px) scale(1); }
            }
            .animate-blob { animation: blob 7s infinite; }
            .animation-delay-2000 { animation-delay: 2s; }
            .animation-delay-4000 { animation-delay: 4s; }
        </style>
    </head>
    <body class="bg-slate-50 text-slate-900 font-sans antialiased selection:bg-red-500 selection:text-white">
 
        <!-- Header / Navbar -->
        <header class="w-full py-4 px-4 sm:px-6 lg:px-8 sticky top-0 bg-white/70 backdrop-blur-xl border-b border-white/20 z-[100] shadow-sm">
            <div class="max-w-[1400px] mx-auto flex justify-between items-center">
                <a href="{{ url('/') }}" class="hover:opacity-80 transition duration-300 flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center text-white font-black font-outfit text-xl shadow-lg shadow-red-200">D</div>
                    <span class="font-outfit font-black text-2xl tracking-tighter text-slate-900">DAILY.CO</span>
                </a>
 
                <nav class="hidden md:flex gap-8 items-center bg-slate-100/50 px-6 py-2.5 rounded-full border border-slate-200 backdrop-blur-md">
                    <a href="#produk" class="text-xs font-black text-slate-600 hover:text-red-600 uppercase tracking-widest transition">Katalog</a>
                    <a href="#fitur" class="text-xs font-black text-slate-600 hover:text-red-600 uppercase tracking-widest transition">Fitur</a>
                </nav>
 
                <div class="flex gap-4 items-center">
                    @if (Route::has('login'))
                        @auth('customer')
                            <a href="{{ url('/customer/dashboard') }}" class="text-xs font-black text-red-600 bg-red-50 px-5 py-2.5 rounded-full hover:bg-red-100 uppercase tracking-widest transition border border-red-100">Dashboard</a>
                        @elseauth('admin')
                            <a href="{{ url('/admin/dashboard') }}" class="text-xs font-black text-red-600 bg-red-50 px-5 py-2.5 rounded-full hover:bg-red-100 uppercase tracking-widest transition border border-red-100">Dashboard Admin</a>
                        @else
                            <a href="{{ route('login') }}" class="text-xs font-black text-slate-600 hover:text-red-600 uppercase tracking-widest transition px-4">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-xs font-black bg-red-600 text-white px-6 py-3 rounded-full hover:bg-red-500 hover:-translate-y-0.5 transition-all shadow-xl shadow-red-200 uppercase tracking-widest">
                                    Mulai Desain
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="relative pt-20 overflow-hidden flex flex-col items-center min-h-[90vh] justify-center pb-20">
            <!-- Background Decoration -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-0 left-1/4 w-96 h-96 bg-red-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
                <div class="absolute top-0 right-1/4 w-96 h-96 bg-orange-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
                <div class="absolute -bottom-32 left-1/2 -z-10 w-96 h-96 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
            </div>

            <div class="max-w-5xl mx-auto px-6 text-center relative z-10">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/80 backdrop-blur-sm border border-red-100 mb-8 shadow-sm animate-bounce">
                    <span class="flex h-2 w-2 rounded-full bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.8)]"></span>
                    <span class="text-[10px] font-black text-red-700 uppercase tracking-widest">Platform Mockup Interaktif #1</span>
                </div>
                
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-black font-outfit text-slate-900 leading-[1.05] tracking-tighter mb-8">
                    Tuangkan Ide di <br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-orange-500 relative inline-block">
                        Canvas Interaktif.
                        <svg class="absolute w-full h-4 -bottom-1 left-0 text-red-200 -z-10" viewBox="0 0 100 10" preserveAspectRatio="none"><path d="M0 5 Q 50 10 100 5" stroke="currentColor" stroke-width="4" fill="none"/></svg>
                    </span>
                </h1>
                
                <p class="max-w-2xl mx-auto text-lg md:text-xl text-slate-600 font-medium leading-relaxed mb-12">
                    Visualisasikan sablon pakaianmu secara realtime, presisi, dan instan. DAILY.CO mengubah cara Anda memesan pakaian custom.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    @auth('customer')
                        <a href="{{ route('customer.dashboard') }}" class="group relative px-8 py-4 bg-slate-900 text-white font-black rounded-full hover:bg-slate-800 transition-all duration-300 shadow-2xl shadow-slate-300 overflow-hidden flex items-center gap-3 w-full sm:w-auto justify-center">
                            LANJUT KARYA ANDA
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="group relative px-8 py-4 bg-slate-900 text-white font-black rounded-full hover:bg-slate-800 hover:-translate-y-1 transition-all duration-300 shadow-2xl shadow-slate-300 overflow-hidden flex items-center gap-3 w-full sm:w-auto justify-center">
                            MULAI DESAIN SEKARANG
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @endauth
                    <a href="#produk" class="px-8 py-4 bg-white text-slate-900 border border-slate-200 rounded-full font-black hover:border-red-600 hover:text-red-600 transition-all duration-300 flex items-center gap-2 w-full sm:w-auto justify-center shadow-sm">
                        Lihat Katalog Katalog
                    </a>
                </div>
            </div>
        </main>

        <!-- Product Catalog Section -->
        <section id="produk" class="py-24 bg-white relative rounded-t-[3rem] -mt-10 z-20 shadow-[0_-20px_50px_rgba(0,0,0,0.05)] border-t border-slate-100">
            <div class="max-w-7xl mx-auto px-6">
                <!-- Header Katalog Seiras Dashboard -->
                <div class="flex items-center justify-between mb-12 pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-3xl lg:text-4xl font-bold flex items-center gap-3 text-slate-900">
                            <span class="p-2 bg-indigo-100 text-indigo-600 rounded-lg shadow-sm">✨</span>
                            Katalog Produk
                        </h2>
                        <p class="text-slate-500 mt-2 max-w-lg">Pilih basis material berkualitas dari kami, tempelkan desainmu, dan biarkan kami yang menyelesaikannya.</p>
                    </div>
                </div>
                
                <!-- Grid Seiras Dashboard -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($produks as $item)
                        <!-- Card Identik dengan Dashboard Customer -->
                        <div class="group relative bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(79,70,229,0.15)] hover:-translate-y-1.5 transition-all duration-300 flex flex-col h-full z-10">
                            
                            <!-- Image Container with Hover Effect -->
                            <div class="relative h-64 bg-slate-50 flex items-center justify-center p-6 overflow-hidden">
                                <!-- Image based on Product -->
                                @if($item->gambar_produk)
                                    <img src="{{ Storage::url($item->gambar_produk) }}" class="w-full h-full object-contain transform group-hover:scale-110 group-hover:-translate-y-2 transition-transform duration-500 drop-shadow-2xl select-none p-4" alt="{{ $item->nama_produk }}">
                                @else
                                    <!-- Placeholder Image based on Product Type -->
                                    @if($item->jenis_produk == 'kaos')
                                        <div class="text-7xl lg:text-8xl transform group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500 drop-shadow-xl select-none">👕</div>
                                    @elseif($item->jenis_produk == 'hoodie')
                                        <div class="text-7xl lg:text-8xl transform group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500 drop-shadow-xl select-none">🧥</div>
                                    @elseif($item->jenis_produk == 'topi')
                                        <div class="text-7xl lg:text-8xl transform group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500 drop-shadow-xl select-none">🧢</div>
                                    @else
                                        <div class="text-7xl lg:text-8xl transform group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500 drop-shadow-xl select-none">👕</div>
                                    @endif
                                @endif
                                
                                <!-- Badges -->
                                <div class="absolute top-4 right-4 flex flex-col gap-2">
                                    <div class="bg-white/90 backdrop-blur text-indigo-800 text-xs font-bold px-3 py-1.5 rounded-xl uppercase tracking-wide shadow-sm border border-indigo-100">
                                        {{ $item->jenis_produk }}
                                    </div>
                                </div>

                                <!-- Overlay CTA -->
                                <div class="absolute inset-0 bg-indigo-900/40 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center z-10">
                                    <a href="{{ route('katalog.show', $item->id_produk) }}" class="bg-white text-indigo-900 font-bold px-6 py-3 rounded-xl shadow-2xl transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                                        Lihat Detail & Mulai Desain
                                    </a>
                                </div>
                            </div>

                            <!-- Card Content -->
                            <div class="p-6 flex flex-col flex-grow relative bg-white z-20">
                                @php
                                    $parts = explode(' ', $item->nama_produk);
                                    $displayName = ($parts[0] === 'Kaos') ? $parts[0] . ' ' . ($parts[1] ?? '') : $parts[0];
                                @endphp
                                <h3 class="text-xl font-bold text-slate-900 mb-2 truncate" title="{{ $displayName }}">{{ $displayName }} Custom</h3>
                                <p class="text-slate-500 text-sm mb-6 flex-grow line-clamp-2 leading-relaxed">{{ $item->deskripsi }}</p>
                                
                                <div class="pt-4 border-t border-slate-100 flex items-center justify-between mt-auto">
                                    <div>
                                        <p class="text-xs text-slate-400 font-medium mb-0.5">Mulai dari</p>
                                        <span class="text-2xl font-black text-indigo-600 bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600">Rp {{ number_format($item->harga_dasar, 0, ',', '.') }}</span>
                                    </div>
                                    <a href="{{ route('katalog.show', $item->id_produk) }}" class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300 hover:shadow-lg hover:shadow-indigo-200">
                                        <svg class="w-5 h-5 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="w-full bg-slate-950 pt-20 pb-10 px-6 border-t border-slate-900">
            <div class="max-w-[1400px] mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
                 <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-slate-800 rounded-full flex items-center justify-center text-slate-400 font-black font-outfit text-sm">D</div>
                    <span class="font-outfit font-black text-xl text-slate-600">DAILY.CO</span>
                 </div>
                 <p class="text-slate-600 text-sm font-medium">&copy; 2026 DAILY.CO. Redefining your style.</p>
            </div>
        </footer>
    </body>
</html>
