<x-app-layout>
    <div class="min-h-screen bg-slate-50 pb-16">
        
        <!-- Hero Section & Quick Stats -->
        <div class="bg-indigo-900 py-16 relative overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"></div>
                <div class="absolute top-24 -left-24 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000"></div>
                <div class="absolute -bottom-8 left-48 w-80 h-80 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-4000"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-8">
                    <!-- Greeting Text -->
                    <div class="text-white">
                        <h1 class="text-4xl sm:text-5xl font-black tracking-tight mb-3">
                            Selamat Datang, 
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-200 to-pink-200">
                                {{ Auth::guard('customer')->user()->nama_customer ?? 'Sobat' }}!
                            </span>
                        </h1>
                        <p class="text-indigo-200 text-lg sm:text-xl font-medium max-w-xl">
                            Wujudkan ide kreatifmu menjadi nyata. Mulai custom desain pakaian berkualitas tinggi secara instan di DAILY.CO.
                        </p>
                    </div>

                    <!-- Quick Stats Cards (Glassmorphism) -->
                    <div class="flex gap-4">
                        <a href="{{ route('customer.orders.index') }}" class="group bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 rounded-2xl p-5 w-40 transition-all duration-300">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-2xl">📦</span>
                                <span class="bg-indigo-500/50 text-indigo-100 text-xs font-bold px-2 py-1 rounded-lg">{{ $activeOrdersCount }}</span>
                            </div>
                            <h4 class="text-indigo-100 font-semibold group-hover:text-white transition-colors">Pesanan Aktif</h4>
                            <p class="text-indigo-300 text-xs mt-1">Cek status produksi</p>
                        </a>
                        
                        <a href="{{ route('customer.cart.index') }}" class="group bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 rounded-2xl p-5 w-40 transition-all duration-300">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-2xl">🛒</span>
                                <span class="bg-pink-500/50 text-pink-100 text-xs font-bold px-2 py-1 rounded-lg">{{ $cartCount }}</span>
                            </div>
                            <h4 class="text-indigo-100 font-semibold group-hover:text-white transition-colors">Keranjang</h4>
                            <p class="text-indigo-300 text-xs mt-1">Lanjutkan checkout</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Catalog Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 -translate-y-6">
            
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-200">
                <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                    <span class="p-2 bg-indigo-100 text-indigo-600 rounded-lg shadow-sm">✨</span>
                    Katalog Produk Unggulan
                </h2>
                <div class="flex gap-2">
                    <span class="px-4 py-1.5 bg-slate-800 text-slate-100 text-sm font-semibold rounded-full shadow-sm hover:bg-slate-700 cursor-pointer transition">Semua</span>
                    <span class="px-4 py-1.5 bg-white text-slate-600 border border-slate-200 text-sm font-medium rounded-full shadow-sm hover:bg-slate-50 cursor-pointer transition">Kaos</span>
                    <span class="px-4 py-1.5 bg-white text-slate-600 border border-slate-200 text-sm font-medium rounded-full shadow-sm hover:bg-slate-50 cursor-pointer transition">Bordir</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse ($produks as $produk)
                    <div class="group relative bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(79,70,229,0.15)] hover:-translate-y-1.5 transition-all duration-300 flex flex-col h-full">
                        
                        <!-- Image Container with Hover Effect -->
                        <div class="relative h-64 bg-slate-50 flex items-center justify-center p-6 overflow-hidden">
                            <!-- Placeholder Image based on Product Type -->
                            @if($produk->jenis_produk == 'kaos')
                                <div class="text-7xl lg:text-8xl transform group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500 drop-shadow-xl">👕</div>
                            @elseif($produk->jenis_produk == 'hoodie')
                                <div class="text-7xl lg:text-8xl transform group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500 drop-shadow-xl">🧥</div>
                            @else
                                <div class="text-7xl lg:text-8xl transform group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500 drop-shadow-xl">🧢</div>
                            @endif
                            
                            <!-- Badges -->
                            <div class="absolute top-4 right-4 flex flex-col gap-2">
                                <div class="bg-white/90 backdrop-blur text-indigo-800 text-xs font-bold px-3 py-1.5 rounded-xl uppercase tracking-wide shadow-sm border border-indigo-100">
                                    {{ $produk->jenis_produk }}
                                </div>
                            </div>
                            
                            <!-- Overlay CTA -->
                            <div class="absolute inset-0 bg-indigo-900/40 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                <a href="{{ route('customer.products.show', $produk->id_produk) }}" class="bg-white text-indigo-900 font-bold px-6 py-3 rounded-xl shadow-2xl transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                                    Lihat Detail & Mulai Desain
                                </a>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-6 flex flex-col flex-grow">
                            <h3 class="text-xl font-bold text-slate-900 mb-2 truncate" title="{{ $produk->nama_produk }}">{{ $produk->nama_produk }}</h3>
                            <p class="text-slate-500 text-sm mb-6 flex-grow line-clamp-2 leading-relaxed">{{ $produk->deskripsi }}</p>
                            
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between mt-auto">
                                <div>
                                    <p class="text-xs text-slate-400 font-medium mb-0.5">Mulai dari</p>
                                    <span class="text-2xl font-black text-indigo-600 bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600">Rp {{ number_format($produk->harga_dasar, 0, ',', '.') }}</span>
                                </div>
                                <a href="{{ route('customer.products.show', $produk->id_produk) }}" class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300 hover:shadow-lg hover:shadow-indigo-200">
                                    <svg class="w-5 h-5 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full border-2 border-dashed border-slate-200 rounded-3xl p-16 flex flex-col items-center justify-center text-center bg-slate-50/50">
                        <div class="w-24 h-24 bg-white rounded-full shadow-sm flex items-center justify-center text-5xl mb-6">📦</div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-2">Katalog Masih Kosong</h3>
                        <p class="text-slate-500 max-w-md mx-auto">Kami sedang mempersiapkan produk-produk sablon terbaik untuk Anda. Silakan cek kembali beberapa saat lagi!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Inject Custom CSS for subtle animations -->
    @push('styles')
    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;  
            overflow: hidden;
        }
    </style>
    @endpush
</x-app-layout>
