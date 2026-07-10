<x-app-layout>
    <div class="min-h-screen bg-slate-50 pb-16">
        
        <!-- Hero Section & Quick Stats -->
        <div class="bg-indigo-900 py-14 relative overflow-hidden">
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
                        <a href="{{ route('customer.orders.index') }}" class="group bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 rounded-2xl p-5 w-40 transition-all duration-300 hover:-translate-y-1">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-2xl">📦</span>
                                <span class="bg-indigo-500/50 text-indigo-100 text-xs font-bold px-2.5 py-1 rounded-lg min-w-[28px] text-center">{{ $activeOrdersCount }}</span>
                            </div>
                            <h4 class="text-indigo-100 font-semibold group-hover:text-white transition-colors">Pesanan Aktif</h4>
                            <p class="text-indigo-300 text-xs mt-1">Cek status produksi</p>
                        </a>
                        
                        <a href="{{ route('customer.cart.index') }}" class="group bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 rounded-2xl p-5 w-40 transition-all duration-300 hover:-translate-y-1">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-2xl">🛒</span>
                                <span class="bg-pink-500/50 text-pink-100 text-xs font-bold px-2.5 py-1 rounded-lg min-w-[28px] text-center">{{ $cartCount }}</span>
                            </div>
                            <h4 class="text-indigo-100 font-semibold group-hover:text-white transition-colors">Keranjang</h4>
                            <p class="text-indigo-300 text-xs mt-1">Lanjutkan checkout</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Catalog Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
            
            <!-- Section Header with Search -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 bg-indigo-100 text-indigo-600 rounded-xl shadow-sm text-lg">✨</span>
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Katalog Produk</h2>
                        <p class="text-slate-500 text-sm mt-0.5">{{ count($produks) }} produk tersedia untuk kamu</p>
                    </div>
                </div>
                
                <!-- Search Bar -->
                <div class="relative w-full sm:w-80">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" id="searchInput" placeholder="Cari produk..." 
                        class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-300 shadow-sm transition-all duration-200">
                </div>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7" id="productGrid">
                @forelse ($produks as $produk)
                    <div class="product-card group relative bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-[0_4px_24px_rgb(0,0,0,0.04)] hover:shadow-[0_12px_40px_rgb(79,70,229,0.12)] hover:-translate-y-2 transition-all duration-300 flex flex-col h-full"
                         data-name="{{ strtolower($produk->nama_produk) }}" 
                         data-jenis="{{ $produk->jenis_produk }}"
                         data-tipe="{{ $produk->tipe_produk }}">
                        
                        <!-- Image Container -->
                        <div class="relative h-60 bg-gradient-to-br {{ $produk->tipe_produk == 'jadi' ? 'from-emerald-50 to-teal-50' : 'from-indigo-50 to-purple-50' }} flex items-center justify-center p-6 overflow-hidden">
                            @if($produk->gambar_produk)
                                <img src="{{ Storage::url($produk->gambar_produk) }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500" alt="{{ $produk->nama_produk }}">
                            @else
                                <!-- Mockup Illustration -->
                                @php
                                    $isPanjang = \Illuminate\Support\Str::contains(strtolower($produk->nama_produk), 'panjang');
                                    $baseImg = match($produk->jenis_produk) {
                                        'kaos' => $isPanjang ? 'kaos_panjang.png' : 'kaos.png',
                                        'hoodie' => 'hoodie.png',
                                        'topi' => 'topi.png',
                                        'polo' => 'polo.png',
                                        'seragam' => 'seragam.png',
                                        default => 'kaos.png'
                                    };
                                @endphp
                                <img src="{{ asset('images/mockups/'.$baseImg) }}" class="w-full h-full object-contain transform group-hover:scale-105 transition-transform duration-500 drop-shadow-2xl" style="filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));" alt="{{ $produk->nama_produk }}">
                            @endif
                            
                            <!-- Category & Type Badges -->
                            <div class="absolute top-4 left-4 flex flex-col gap-2">
                                @if($produk->tipe_produk == 'jadi')
                                    <div class="bg-emerald-500 text-white text-xs font-bold px-3 py-1.5 rounded-xl flex items-center gap-1.5 shadow-lg shadow-emerald-500/20">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Siap Beli
                                    </div>
                                @else
                                    <div class="bg-indigo-500 text-white text-xs font-bold px-3 py-1.5 rounded-xl flex items-center gap-1.5 shadow-lg shadow-indigo-500/20">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        Kustom Desain
                                    </div>
                                @endif
                            </div>

                            <div class="absolute top-4 right-4">
                                <div class="bg-white/90 backdrop-blur text-slate-600 text-xs font-semibold px-3 py-1.5 rounded-xl uppercase tracking-wide shadow-sm border border-slate-100">
                                    {{ $produk->jenis_produk }}
                                </div>
                            </div>
                            
                            <!-- Hover Overlay CTA -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-end justify-center pb-6">
                                <a href="{{ route('customer.products.show', $produk->id_produk) }}" 
                                   class="bg-white text-slate-900 font-bold px-6 py-3 rounded-2xl shadow-2xl transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 flex items-center gap-2 hover:bg-indigo-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Lihat Detail
                                </a>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-6 flex flex-col flex-grow">
                            @php
                                $parts = explode(' ', $produk->nama_produk);
                                $displayName = ($parts[0] === 'Kaos') ? $parts[0] . ' ' . ($parts[1] ?? '') : $parts[0];
                            @endphp
                            <h3 class="text-lg font-bold text-slate-900 mb-1.5 truncate" title="{{ $displayName }}">{{ $displayName }} Custom</h3>
                            <p class="text-slate-400 text-sm mb-5 flex-grow line-clamp-2 leading-relaxed">{{ $produk->deskripsi ?: 'Produk berkualitas tinggi dengan bahan pilihan terbaik.' }}</p>
                            
                            <!-- Price & CTA Row -->
                            <div class="pt-4 border-t border-slate-100 mt-auto">
                                <div class="flex items-end justify-between mb-4">
                                    <div>
                                        <p class="text-xs text-slate-400 font-medium mb-0.5">Mulai dari</p>
                                        <span class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r {{ $produk->tipe_produk == 'jadi' ? 'from-emerald-600 to-teal-600' : 'from-indigo-600 to-purple-600' }}">
                                            Rp {{ number_format($produk->harga_dasar, 0, ',', '.') }}
                                        </span>
                                        <span class="text-xs text-slate-400 ml-1">/ pcs</span>
                                    </div>
                                </div>
                                
                                <!-- Action Button — different style per product type -->
                                <a href="{{ route('customer.products.show', $produk->id_produk) }}" 
                                   class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-2xl font-bold text-sm transition-all duration-200 
                                   {{ $produk->tipe_produk == 'jadi' 
                                       ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-500 hover:text-white hover:shadow-lg hover:shadow-emerald-200' 
                                       : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white hover:shadow-lg hover:shadow-indigo-200' }}">
                                    @if($produk->tipe_produk == 'jadi')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path></svg>
                                        Lihat & Beli Langsung
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        Mulai Desain Kustom
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full border-2 border-dashed border-slate-200 rounded-3xl p-16 flex flex-col items-center justify-center text-center bg-white/50">
                        <div class="w-28 h-28 bg-indigo-50 rounded-3xl flex items-center justify-center mb-6">
                            <svg class="w-14 h-14 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-2">Katalog Masih Kosong</h3>
                        <p class="text-slate-500 max-w-md mx-auto leading-relaxed">Kami sedang mempersiapkan produk-produk sablon terbaik untuk Anda. Silakan cek kembali beberapa saat lagi!</p>
                    </div>
                @endforelse
            </div>

            <!-- No Results Message (hidden by default, shown via JS) -->
            <div id="noResults" class="hidden col-span-full border-2 border-dashed border-slate-200 rounded-3xl p-12 flex flex-col items-center justify-center text-center bg-white/50 mt-7">
                <div class="w-20 h-20 bg-amber-50 rounded-2xl flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-1">Produk Tidak Ditemukan</h3>
                <p class="text-slate-500 text-sm">Coba kata kunci lain atau hapus pencarian</p>
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

    <!-- Client-side Search Script -->
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const productGrid = document.getElementById('productGrid');
            const noResults = document.getElementById('noResults');
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase().trim();
                    const cards = productGrid.querySelectorAll('.product-card');
                    let visibleCount = 0;
                    
                    cards.forEach(card => {
                        const name = card.dataset.name || '';
                        const jenis = card.dataset.jenis || '';
                        const tipe = card.dataset.tipe || '';
                        const matches = name.includes(query) || jenis.includes(query) || tipe.includes(query);
                        
                        card.style.display = matches ? '' : 'none';
                        if (matches) visibleCount++;
                    });
                    
                    // Show/hide no results message
                    if (noResults) {
                        noResults.classList.toggle('hidden', visibleCount > 0);
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
