<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>DAILY.CO - Visualisasi Mockup Sablon Premium</title>

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
    <body class="bg-gray-50 text-slate-900 font-sans antialiased selection:bg-red-500 selection:text-white">
 
        <!-- Header / Navbar -->
        <header class="w-full py-4 px-4 sm:px-6 lg:px-8 border-b border-slate-200 sticky top-0 bg-white/80 backdrop-blur-md z-[100]">
            <div class="max-w-[1400px] mx-auto flex justify-between items-center">
                <a href="{{ url('/') }}" class="hover:opacity-80 transition duration-300">
                    <img src="{{ asset('images/logo-dailyco.png') }}" class="h-14 w-auto" alt="DAILY.CO Logo">
                </a>
 
                <nav class="hidden md:flex gap-8 items-center">
                    <a href="#produk" class="text-xs font-black text-slate-400 hover:text-red-600 uppercase tracking-[0.2em] transition">Katalog</a>
                    <a href="#testimoni" class="text-xs font-black text-slate-400 hover:text-red-600 uppercase tracking-[0.2em] transition">Testimoni</a>
                    <a href="#tentang" class="text-xs font-black text-slate-400 hover:text-red-600 uppercase tracking-[0.2em] transition">Tentang Kami</a>
                </nav>
 
                <div class="flex gap-4 items-center">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-xs font-black text-slate-600 hover:text-red-600 uppercase tracking-widest transition">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-xs font-black text-slate-600 hover:text-red-600 uppercase tracking-widest transition">Masuk</a>
 
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-xs font-black bg-red-600 text-white px-6 py-3 rounded-2xl hover:bg-red-500 transition shadow-xl shadow-red-100 uppercase tracking-widest">
                                    Mulai Desain
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </header>

        <!-- Hero: Premium Sky Blue Theme -->
        <main class="relative pt-20 overflow-hidden flex flex-col items-center min-h-screen">
            <!-- Background Decoration -->
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full -z-10 pointer-events-none">
                <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-red-200/30 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-[10%] right-[-5%] w-[40%] h-[40%] bg-violet-200/20 rounded-full blur-[100px]"></div>
            </div>

            <div class="max-w-7xl mx-auto px-6 pt-24 lg:pt-32 pb-20 text-center relative z-10">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-50 border border-red-100 mb-8 animate-bounce">
                    <span class="flex h-2 w-2 rounded-full bg-red-500"></span>
                    <span class="text-xs font-bold text-red-700 uppercase tracking-widest">Platform Mockup Interaktif #1</span>
                </div>
                
                <h1 class="text-5xl lg:text-7xl font-black font-outfit text-slate-900 leading-[1.05] tracking-tight mb-8">
                    Tuangkan Ide Kreatifmu <br/> di <span class="text-red-600 relative">Canvas Interaktif<span class="absolute bottom-1 left-0 w-full h-3 bg-red-100 -z-10"></span></span>
                </h1>
                
                <p class="max-w-2xl mx-auto text-lg lg:text-xl text-slate-600 leading-relaxed mb-12">
                    Visualisasikan setiap detail desain sablonmu secara langsung. 
                    <span class="font-bold text-slate-800">DAILY.CO</span> mempermudah proses custom kaos dan hoodie dengan teknologi mockup berbasis web yang presisi.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    @auth('customer')
                        <a href="{{ route('customer.dashboard') }}" class="group relative px-10 py-5 bg-red-600 text-white font-black rounded-2xl hover:bg-red-500 transition-all duration-300 shadow-2xl shadow-red-200 overflow-hidden">
                            <span class="relative z-10 flex items-center gap-2 text-lg">
                                LANJUT KARYA ANDA
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </span>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="group relative px-10 py-5 bg-red-600 text-white font-black rounded-2xl hover:bg-red-500 transition-all duration-300 shadow-2xl shadow-red-200 overflow-hidden">
                            <span class="relative z-10 flex items-center gap-2 text-lg">
                                MULAI DESAIN SEKARANG
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </span>
                        </a>
                    @endauth
                    <a href="#katalog" class="px-8 py-5 text-slate-600 font-bold hover:text-red-600 transition flex items-center gap-2">
                        Lihat Katalog Produk
                    </a>
                </div>

                <!-- Showcase Image -->
                <div class="mt-24 relative max-w-5xl mx-auto group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-red-400 to-indigo-400 rounded-[2.5rem] blur opacity-25 group-hover:opacity-40 transition duration-1000"></div>
                    <div class="relative bg-white border border-slate-200 rounded-[2rem] p-4 shadow-2xl overflow-hidden aspect-[16/9] flex items-center justify-center bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] bg-slate-50">
                        <div class="flex flex-col items-center gap-4">
                            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center text-red-600">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </div>
                            <span class="font-black font-outfit text-slate-400 tracking-widest uppercase">Live Mockup Preview</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Product Catalog Section -->
        <section id="katalog" class="py-32 bg-white relative">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-20">
                    <h2 class="text-3xl lg:text-5xl font-black font-outfit text-slate-900 mb-4">Pilih Produk Dasar</h2>
                    <div class="h-1.5 w-24 bg-red-600 mx-auto rounded-full"></div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    @foreach($produks as $item)
                    <div class="group relative flex flex-col">
                        <div class="relative aspect-[4/5] bg-slate-100 rounded-[2.5rem] overflow-hidden mb-6 border border-slate-200 group-hover:border-red-300 transition-all duration-500 shadow-sm">
                            <div class="absolute inset-0 bg-gradient-to-t from-red-900/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            
                            @php $imgFile = $item->jenis_produk == 'kaos' ? 'kaos.png' : 'hoodie.png'; @endphp
                            <img src="{{ asset('images/mockups/' . $imgFile) }}" alt="{{ $item->nama_produk }}" class="w-full h-full object-contain p-8 transform group-hover:scale-110 transition-transform duration-700 drop-shadow-xl">
                            
                            <div class="absolute bottom-6 left-6 right-6 translate-y-10 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-500">
                                <a href="{{ route('katalog.show', $item->id_produk) }}" class="block w-full py-4 bg-white text-slate-900 font-black text-center rounded-2xl shadow-xl hover:bg-red-600 hover:text-white transition">
                                    PILIH & DESAIN
                                </a>
                            </div>
                        </div>
                        <div class="px-2">
                             <div class="flex justify-between items-start mb-2">
                                <h3 class="text-xl font-black text-slate-900">{{ $item->nama_produk }}</h3>
                                <span class="bg-red-100 text-red-700 text-[10px] font-black px-2 py-1 rounded-md uppercase">{{ $item->jenis_produk }}</span>
                             </div>
                             <p class="text-red-600 font-black text-xl italic uppercase tracking-tighter">Rp {{ number_format($item->harga_dasar, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="w-full bg-slate-900 pt-20 pb-10 px-4 sm:px-6 lg:px-8 border-t border-slate-800">
            <div class="max-w-[1400px] mx-auto text-center">
                 <img src="{{ asset('images/logo-dailyco.png') }}" class="h-16 w-auto mx-auto mb-8 contrast-200 brightness-200" alt="DAILY.CO Logo">
                 <p class="text-slate-500 text-sm italic mb-10">&copy; 2026 DAILY.CO - Built for self-expression.</p>
            </div>
        </footer>

    </body>
</html>
