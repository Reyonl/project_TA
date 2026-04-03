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
    <body class="bg-slate-50 text-slate-900 font-sans antialiased selection:bg-sky-500 selection:text-white">
        
        <!-- Premium Navigation -->
        <nav class="fixed w-full py-4 px-6 z-[100] transition-all duration-300">
            <div class="max-w-7xl mx-auto glass rounded-2xl px-6 py-3 flex justify-between items-center shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-sky-600 rounded-xl flex items-center justify-center shadow-lg shadow-sky-200">
                        <span class="text-white font-black text-xl font-outfit">D</span>
                    </div>
                    <span class="text-2xl font-black font-outfit tracking-tighter text-slate-900">
                        DAILY<span class="text-sky-600">.CO</span>
                    </span>
                </div>
                <div class="hidden md:flex gap-8 items-center">
                    <a href="#" class="text-sm font-bold text-slate-600 hover:text-sky-600 transition">Tentang Kami</a>
                    <a href="#" class="text-sm font-bold text-slate-600 hover:text-sky-600 transition">Panduan</a>
                    <div class="h-4 w-px bg-slate-200"></div>
                    @auth('customer')
                        <a href="{{ route('customer.dashboard') }}" class="text-sm font-bold bg-sky-600 text-white px-6 py-2.5 rounded-xl hover:bg-sky-500 transition shadow-lg shadow-sky-100">Dashboard</a>
                    @else
                        @auth('admin')
                            <a href="{{ route('admin.dashboard') }}" class="text-sm font-bold bg-slate-800 text-white px-6 py-2.5 rounded-xl hover:bg-slate-900 transition">Admin Panel</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-bold text-slate-600 hover:text-sky-600 transition">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-sm font-bold bg-sky-600 text-white px-6 py-2.5 rounded-xl hover:bg-sky-500 transition shadow-lg shadow-sky-100 italic">Mulai Desain</a>
                            @endif
                        @endauth
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Hero: Premium Sky Blue Theme -->
        <main class="relative pt-20 overflow-hidden flex flex-col items-center min-h-screen">
            <!-- Background Decoration -->
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full -z-10 pointer-events-none">
                <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-sky-200/30 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-[10%] right-[-5%] w-[40%] h-[40%] bg-violet-200/20 rounded-full blur-[100px]"></div>
            </div>

            <div class="max-w-7xl mx-auto px-6 pt-24 lg:pt-32 pb-20 text-center relative z-10">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-sky-50 border border-sky-100 mb-8 animate-bounce">
                    <span class="flex h-2 w-2 rounded-full bg-sky-500"></span>
                    <span class="text-xs font-bold text-sky-700 uppercase tracking-widest">Platform Mockup Interaktif #1</span>
                </div>
                
                <h1 class="text-5xl lg:text-7xl font-black font-outfit text-slate-900 leading-[1.05] tracking-tight mb-8">
                    Tuangkan Ide Kreatifmu <br/> di <span class="text-sky-600 relative">Canvas Interaktif<span class="absolute bottom-1 left-0 w-full h-3 bg-sky-100 -z-10"></span></span>
                </h1>
                
                <p class="max-w-2xl mx-auto text-lg lg:text-xl text-slate-600 leading-relaxed mb-12">
                    Visualisasikan setiap detail desain sablonmu secara langsung. 
                    <span class="font-bold text-slate-800">DAILY.CO</span> mempermudah proses custom kaos dan hoodie dengan teknologi mockup berbasis web yang presisi.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    @auth('customer')
                        <a href="{{ route('customer.dashboard') }}" class="group relative px-10 py-5 bg-sky-600 text-white font-black rounded-2xl hover:bg-sky-500 transition-all duration-300 shadow-2xl shadow-sky-200 overflow-hidden">
                            <span class="relative z-10 flex items-center gap-2 text-lg">
                                LANJUT KARYA ANDA
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </span>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="group relative px-10 py-5 bg-sky-600 text-white font-black rounded-2xl hover:bg-sky-500 transition-all duration-300 shadow-2xl shadow-sky-200 overflow-hidden">
                            <span class="relative z-10 flex items-center gap-2 text-lg">
                                MULAI DESAIN SEKARANG
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </span>
                        </a>
                    @endauth
                    <a href="#katalog" class="px-8 py-5 text-slate-600 font-bold hover:text-sky-600 transition flex items-center gap-2">
                        Lihat Katalog Produk
                    </a>
                </div>

                <!-- Showcase Image -->
                <div class="mt-24 relative max-w-5xl mx-auto group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-sky-400 to-indigo-400 rounded-[2.5rem] blur opacity-25 group-hover:opacity-40 transition duration-1000"></div>
                    <div class="relative bg-white border border-slate-200 rounded-[2rem] p-4 shadow-2xl overflow-hidden aspect-[16/9] flex items-center justify-center bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] bg-slate-50">
                        <div class="flex flex-col items-center gap-4">
                            <div class="w-20 h-20 bg-sky-100 rounded-full flex items-center justify-center text-sky-600">
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
                    <div class="h-1.5 w-24 bg-sky-600 mx-auto rounded-full"></div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    @foreach($produks as $item)
                    <div class="group relative flex flex-col">
                        <div class="relative aspect-[4/5] bg-slate-100 rounded-[2.5rem] overflow-hidden mb-6 border border-slate-200 group-hover:border-sky-300 transition-all duration-500 shadow-sm">
                            <div class="absolute inset-0 bg-gradient-to-t from-sky-900/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            
                            @php $imgFile = $item->jenis_produk == 'kaos' ? 'kaos.png' : 'hoodie.png'; @endphp
                            <img src="{{ asset('images/mockups/' . $imgFile) }}" alt="{{ $item->nama_produk }}" class="w-full h-full object-contain p-8 transform group-hover:scale-110 transition-transform duration-700 drop-shadow-xl">
                            
                            <div class="absolute bottom-6 left-6 right-6 translate-y-10 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-500">
                                <a href="{{ route('katalog.show', $item->id_produk) }}" class="block w-full py-4 bg-white text-slate-900 font-black text-center rounded-2xl shadow-xl hover:bg-sky-600 hover:text-white transition">
                                    PILIH & DESAIN
                                </a>
                            </div>
                        </div>
                        <div class="px-2">
                             <div class="flex justify-between items-start mb-2">
                                <h3 class="text-xl font-black text-slate-900">{{ $item->nama_produk }}</h3>
                                <span class="bg-sky-100 text-sky-700 text-[10px] font-black px-2 py-1 rounded-md uppercase">{{ $item->jenis_produk }}</span>
                             </div>
                             <p class="text-sky-600 font-black text-xl italic uppercase tracking-tighter">Rp {{ number_format($item->harga_dasar, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-slate-900 py-20 px-6">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-10">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-sky-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-black text-sm font-outfit">D</span>
                    </div>
                    <span class="text-xl font-black font-outfit tracking-tighter text-white">
                        DAILY<span class="text-sky-600">.CO</span>
                    </span>
                </div>
                <p class="text-slate-400 text-sm font-medium">© 2026 DAILY.CO - Skripsi Research Project. All rights reserved.</p>
                <div class="flex gap-6">
                    <a href="#" class="text-slate-400 hover:text-sky-400 transition text-sm font-bold">Instagram</a>
                    <a href="#" class="text-slate-400 hover:text-sky-400 transition text-sm font-bold">WhatsApp</a>
                </div>
            </div>
        </footer>

    </body>
</html>
