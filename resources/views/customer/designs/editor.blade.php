<x-app-layout>
    <!-- Tambahkan library Fabric.js & Google Fonts (Gunakan Lokal untuk kestabilan) -->
    <script src="/js/fabric.min.js"></script>
    <script>
        // Alpine data binding bridge (Must be defined before x-data evaluates)
        window.activeBaseColorLocal = '#ffffff'; 
        window.canvasBackgroundChange = function(color) {
            window.activeBaseColorLocal = color;
            const printbox = document.getElementById('printAreaBox');
            if(printbox) {
                if(color === '#1e293b') {
                    printbox.classList.replace('border-slate-800/20', 'border-white/30');
                } else {
                    printbox.classList.replace('border-white/30', 'border-slate-800/20');
                }
            }
        };
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Dancing+Script:wght@700&family=Lobster&family=Montserrat:wght@400;700&family=Pacifico&family=Playfair+Display:wght@700&family=Roboto:wght@400;700&family=Oswald:wght@500&family=Anton&display=swap" rel="stylesheet">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Editor Desain Sablon') }} - {{ $produk->nama_produk }}
        </h2>
    </x-slot>

    <div class="h-[calc(100vh-65px)] overflow-hidden" 
         x-data="{ 
             activeTab: 'templates', 
             baseColor: new URLSearchParams(window.location.search).get('color') ? (selectedColorFromMap = {
                 'White': '#ffffff', 'Black': '#1e293b', 'Navy': '#1e3a8a', 
                 'Dark Grey': '#334155', 'Red': '#dc2626', 'Forest Green': '#14532d', 
                 'Maroon': '#7f1d1d', 'Military Green': '#4B5320', 'Sand': '#D2B48C',
                 'Light Blue': '#93c5fd', 'Pink': '#f472b6', 'Purple': '#6d28d9',
                 'Orange': '#f97316', 'Yellow': '#facc15', 'Teal': '#0d9488'
             }[new URLSearchParams(window.location.search).get('color')] || '#ffffff') : '#ffffff', 
             activeSide: 'front', 
             sidebarOpen: true,
             technique: new URLSearchParams(window.location.search).get('technique') || 'sablon'
         }" x-init="$nextTick(() => { if(typeof window.canvasBackgroundChange === 'function') window.canvasBackgroundChange(baseColor) })">
        <div class="flex h-full bg-slate-50">
            
            <!-- Navbar Kiri Tepi (Icon Only) -->
            <div class="w-16 bg-white border-r border-slate-200 flex flex-col items-center py-4 gap-4 z-30 shadow-sm flex-shrink-0">
                <button @click="activeTab = 'templates'; sidebarOpen = true" :class="activeTab === 'templates' && sidebarOpen ? 'text-red-600 bg-red-50' : 'text-slate-500 hover:text-red-600 hover:bg-slate-50'" class="w-12 h-12 flex flex-col items-center justify-center rounded-xl transition">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
                    <span class="text-[9px] font-bold uppercase tracking-tighter">Tools</span>
                </button>
                <button @click="activeTab = 'upload'; sidebarOpen = true" :class="activeTab === 'upload' && sidebarOpen ? 'text-red-600 bg-red-50' : 'text-slate-500 hover:text-red-600 hover:bg-slate-50'" class="w-12 h-12 flex flex-col items-center justify-center rounded-xl transition">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    <span class="text-[9px] font-bold uppercase tracking-tighter">Upload</span>
                </button>
                <button @click="activeTab = 'stickers'; sidebarOpen = true" :class="activeTab === 'stickers' && sidebarOpen ? 'text-red-600 bg-red-50' : 'text-slate-500 hover:text-red-600 hover:bg-slate-50'" class="w-12 h-12 flex flex-col items-center justify-center rounded-xl transition">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-[9px] font-bold uppercase tracking-tighter">Stiker</span>
                </button>
                <button @click="activeTab = 'text'; sidebarOpen = true" :class="activeTab === 'text' && sidebarOpen ? 'text-red-600 bg-red-50' : 'text-slate-500 hover:text-red-600 hover:bg-slate-50'" class="w-12 h-12 flex flex-col items-center justify-center rounded-xl transition">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    <span class="text-[9px] font-bold uppercase tracking-tighter">Teks</span>
                </button>
                
                <div class="mt-auto pb-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-100 transition text-slate-400 group" title="Toggle Sidebar">
                        <svg class="w-5 h-5 transition-transform duration-300" :class="!sidebarOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Panel Tools Kiri (Collapsible) -->
            <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="w-72 bg-white border-r border-slate-200 shadow-sm flex flex-col flex-shrink-0 relative z-20">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest" x-text="
                        activeTab === 'templates' ? 'Template Desain' :
                        (activeTab === 'upload' ? 'Upload Gambar' :
                        (activeTab === 'stickers' ? 'Cari Stiker' : 'Tambahkan Teks'))
                    "></h3>
                    <button @click="sidebarOpen = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto p-5 custom-scrollbar">

                    <!-- Tab: TEMPLATE -->
                    <div x-show="activeTab === 'templates'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" class="space-y-4">
                        <p class="text-[11px] font-bold text-slate-400 mb-2 uppercase tracking-tight">Katalog Aset Desain</p>
                        <div class="grid grid-cols-2 gap-3">
                            @forelse($templates as $template)
                                <div class="bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-red-500 hover:shadow-lg hover:shadow-red-100 transition aspect-square flex items-center justify-center bg-slate-50 p-2 template-item group" data-url="{{ Storage::url($template->file_template) }}">
                                    <img src="{{ Storage::url($template->file_template) }}" alt="Template" class="w-full h-full object-contain pointer-events-none group-hover:scale-110 transition-transform">
                                </div>
                            @empty
                                <div class="col-span-2 text-center text-sm text-slate-400 py-4">Belum ada template.</div>
                            @endforelse
                        </div>
                    </div>
 
                    <!-- Tab: UPLOAD -->
                    <div x-show="activeTab === 'upload'" style="display: none;" class="space-y-4">
                        <p class="text-[11px] font-bold text-slate-400 mb-2 uppercase tracking-tight">Unggah Aset Anda</p>
                        <label class="block w-full border-2 border-dashed border-red-100 rounded-2xl p-8 text-center hover:bg-red-50 hover:border-red-300 cursor-pointer transition group">
                            <input type="file" id="imageLoader" accept="image/png, image/jpeg, image/svg+xml" class="hidden"/>
                            <div class="text-red-500 mb-3 group-hover:scale-125 transition-transform duration-300">
                                <svg class="w-10 h-10 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            </div>
                            <span class="text-red-600 font-black block text-sm">KLIK UNTUK UNGGAH</span>
                            <span class="text-slate-400 text-[10px] mt-1 block font-bold">PNG / JPG / SVG (Max 2MB)</span>
                        </label>
                    </div>
 
                    <!-- Tab: STIKER -->
                    <div x-show="activeTab === 'stickers'" style="display: none;" class="flex flex-col h-full bg-white">
                         <div class="flex mb-4 group">
                            <input type="text" id="stickerSearchInput" placeholder="Cari ikon..." class="flex-1 border border-slate-200 rounded-l-xl px-3 py-2 text-sm focus:ring-red-500 focus:border-red-500 bg-slate-50">
                            <button id="searchStickerBtn" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-r-xl text-sm font-bold transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                        </div>
                        <div id="stickersContainer" class="grid grid-cols-2 gap-3 overflow-y-auto pb-4 custom-scrollbar">
                            <div class="col-span-2 text-center text-xs text-slate-400 py-4 italic tracking-widest">Memuat library...</div>
                        </div>
                    </div>
 
                    <!-- Tab: TEKS -->
                    <div x-show="activeTab === 'text'" style="display: none;" class="space-y-4">
                        <button id="addTextBtn" class="w-full bg-red-600 text-white font-black py-4 px-4 rounded-2xl shadow-xl shadow-red-100 hover:bg-red-500 transition hover:-translate-y-1">
                            + TAMBAH TEKS BARU
                        </button>
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                            <p class="text-[10px] text-slate-500 text-center font-bold leading-relaxed">Pilih objek teks pada kanvas untuk memunculkan panel pengaturan font dan warna.</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Canvas Area Tengah -->
            <div class="flex-1 flex flex-col relative bg-slate-100 overflow-hidden">
                
                <!-- Toolbar Atas (Floating Glass Style) -->
                <div class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-6 shadow-sm z-10 w-full flex-shrink-0">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('home') }}" class="flex items-center gap-2 hover:opacity-80 transition">
                                <img src="{{ asset('images/logo-dailyco.png') }}" class="h-10 w-auto" alt="Logo">
                            </a>
                        </div>
                        
                        <!-- Toggle Sidebar for Mobile/Small Screens if hidden -->
                        <button x-show="!sidebarOpen" @click="sidebarOpen = true" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition ml-2">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                        </button>
 
                        <!-- Sisi Baju Toggle -->
                        <div class="flex items-center bg-slate-100 rounded-xl p-1 border border-slate-200 ml-4 shadow-inner">
                            <button @click="activeSide = 'front'; window.switchCanvasSide('front')" :class="activeSide === 'front' ? 'bg-white shadow-sm text-red-700 font-extrabold' : 'text-slate-500 hover:text-slate-700'" class="px-5 py-1.5 text-xs rounded-lg transition-all">DEPAN</button>
                            <button @click="activeSide = 'back'; window.switchCanvasSide('back')" :class="activeSide === 'back' ? 'bg-white shadow-sm text-red-700 font-extrabold' : 'text-slate-500 hover:text-slate-700'" class="px-5 py-1.5 text-xs rounded-lg transition-all">BELAKANG</button>
                            @if($produk->jenis_produk == 'topi')
                            <button @click="activeSide = 'left'; window.switchCanvasSide('left')" :class="activeSide === 'left' ? 'bg-white shadow-sm text-red-700 font-extrabold' : 'text-slate-500 hover:text-slate-700'" class="px-5 py-1.5 text-xs rounded-lg transition-all">KIRI</button>
                            <button @click="activeSide = 'right'; window.switchCanvasSide('right')" :class="activeSide === 'right' ? 'bg-white shadow-sm text-red-700 font-extrabold' : 'text-slate-500 hover:text-slate-700'" class="px-5 py-1.5 text-xs rounded-lg transition-all">KANAN</button>
                            @endif
                        </div>
                        
                        <!-- Base Color Picker -->
                        <div class="flex items-center gap-2 border-l border-slate-200 pl-4 py-2">
                            <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Warna Dasar:</span>
                            <div class="flex flex-wrap gap-1.5 w-40 max-h-12 overflow-y-auto px-1 custom-scrollbar items-center">
                                <button @click="baseColor = '#ffffff'; canvasBackgroundChange('#ffffff')" class="shrink-0 w-5 h-5 rounded-full bg-[#ffffff] border border-slate-300 shadow-sm hover:ring-2 hover:ring-red-400 hover:ring-offset-1 transition" title="White"></button>
                                <button @click="baseColor = '#1e293b'; canvasBackgroundChange('#1e293b')" class="shrink-0 w-5 h-5 rounded-full bg-[#1e293b] border border-transparent shadow-sm hover:ring-2 hover:ring-red-400 hover:ring-offset-1 transition" title="Black"></button>
                                <button @click="baseColor = '#1e3a8a'; canvasBackgroundChange('#1e3a8a')" class="shrink-0 w-5 h-5 rounded-full bg-[#1e3a8a] border border-transparent shadow-sm hover:ring-2 hover:ring-red-400 hover:ring-offset-1 transition" title="Navy"></button>
                                <button @click="baseColor = '#334155'; canvasBackgroundChange('#334155')" class="shrink-0 w-5 h-5 rounded-full bg-[#334155] border border-transparent shadow-sm hover:ring-2 hover:ring-red-400 hover:ring-offset-1 transition" title="Dark Grey"></button>
                                <button @click="baseColor = '#dc2626'; canvasBackgroundChange('#dc2626')" class="shrink-0 w-5 h-5 rounded-full bg-[#dc2626] border border-transparent shadow-sm hover:ring-2 hover:ring-red-400 hover:ring-offset-1 transition" title="Red"></button>
                                <button @click="baseColor = '#14532d'; canvasBackgroundChange('#14532d')" class="shrink-0 w-5 h-5 rounded-full bg-[#14532d] border border-transparent shadow-sm hover:ring-2 hover:ring-red-400 hover:ring-offset-1 transition" title="Forest Green"></button>
                                <button @click="baseColor = '#7f1d1d'; canvasBackgroundChange('#7f1d1d')" class="shrink-0 w-5 h-5 rounded-full bg-[#7f1d1d] border border-transparent shadow-sm hover:ring-2 hover:ring-red-400 hover:ring-offset-1 transition" title="Maroon"></button>
                                <button @click="baseColor = '#4B5320'; canvasBackgroundChange('#4B5320')" class="shrink-0 w-5 h-5 rounded-full bg-[#4B5320] border border-transparent shadow-sm hover:ring-2 hover:ring-red-400 hover:ring-offset-1 transition" title="Military Green"></button>
                                <button @click="baseColor = '#D2B48C'; canvasBackgroundChange('#D2B48C')" class="shrink-0 w-5 h-5 rounded-full bg-[#D2B48C] border border-transparent shadow-sm hover:ring-2 hover:ring-red-400 hover:ring-offset-1 transition" title="Sand"></button>
                                <button @click="baseColor = '#93c5fd'; canvasBackgroundChange('#93c5fd')" class="shrink-0 w-5 h-5 rounded-full bg-[#93c5fd] border border-transparent shadow-sm hover:ring-2 hover:ring-red-400 hover:ring-offset-1 transition" title="Light Blue"></button>
                                <button @click="baseColor = '#f472b6'; canvasBackgroundChange('#f472b6')" class="shrink-0 w-5 h-5 rounded-full bg-[#f472b6] border border-transparent shadow-sm hover:ring-2 hover:ring-red-400 hover:ring-offset-1 transition" title="Pink"></button>
                                <button @click="baseColor = '#6d28d9'; canvasBackgroundChange('#6d28d9')" class="shrink-0 w-5 h-5 rounded-full bg-[#6d28d9] border border-transparent shadow-sm hover:ring-2 hover:ring-red-400 hover:ring-offset-1 transition" title="Purple"></button>
                                <button @click="baseColor = '#f97316'; canvasBackgroundChange('#f97316')" class="shrink-0 w-5 h-5 rounded-full bg-[#f97316] border border-transparent shadow-sm hover:ring-2 hover:ring-red-400 hover:ring-offset-1 transition" title="Orange"></button>
                                <button @click="baseColor = '#facc15'; canvasBackgroundChange('#facc15')" class="shrink-0 w-5 h-5 rounded-full bg-[#facc15] border border-transparent shadow-sm hover:ring-2 hover:ring-red-400 hover:ring-offset-1 transition" title="Yellow"></button>
                                <button @click="baseColor = '#0d9488'; canvasBackgroundChange('#0d9488')" class="shrink-0 w-5 h-5 rounded-full bg-[#0d9488] border border-transparent shadow-sm hover:ring-2 hover:ring-red-400 hover:ring-offset-1 transition" title="Teal"></button>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Simpan Desain -->
                    <div class="flex items-center gap-3 ml-auto">
                        <button id="saveDesignBtn" class="flex items-center gap-2 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black py-2.5 px-6 rounded-xl shadow-lg shadow-red-100 hover:shadow-red-200 transition-all text-sm hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                            <span>SIMPAN DESAIN</span>
                        </button>
                    </div>
                </div>

                @if($desainRevisi)
                <div class="bg-red-50 border-b border-red-200 p-4 sticky top-[73px] z-10 flex gap-3 shadow-inner">
                    <svg class="w-6 h-6 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h4 class="font-bold text-red-800">Revisi Permintaan Admin</h4>
                        <p class="text-sm text-red-700">Silakan buat ulang desain Anda sesuai catatan: <strong class="italic">'{{ \App\Models\OrderDetail::where('id_desain', $desainRevisi->id_desain)->value('catatan_admin') }}'</strong></p>
                        <p class="text-xs text-red-500 mt-1">*Desain lama Anda tidak dapat dimuat otomatis karena telah dirender jadi gambar permanen. Silakan desain baru.</p>
                    </div>
                </div>
                @endif

                <!-- Canvas Workspace Container -->
                <div class="flex-1 overflow-auto flex justify-center items-center py-8 relative bg-slate-50">


                    <!-- Layout Canvas + Base -->
                    <!-- Container Absolute 480x600 untuk Mockup Static Background -->
                    <div class="relative shadow-2xl rounded-xl overflow-hidden pointer-events-auto flex items-center justify-center bg-slate-100" id="mockupContainer" style="width: 480px; height: 600px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);">
                        
                        <!-- Indikator Sisi Baju -->
                        <div class="absolute top-4 left-4 z-30 pointer-events-none">
                            <span class="bg-indigo-600/90 text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-lg border border-indigo-400/50 uppercase tracking-widest backdrop-blur-sm" x-text="activeSide === 'front' ? 'Bagian Depan' : 'Bagian Belakang'">Bagian Depan</span>
                        </div>
                        
                        <!-- Latar Baju Fotorealistis (Statik/Locked Background) -->
                        <div class="absolute inset-0 z-0 pointer-events-none" id="baseColorContainer">
                             <div class="w-full h-full flex items-center justify-center relative overflow-hidden">
                                @php
                                    $hasBack = in_array($produk->jenis_produk, ['kaos', 'hoodie', 'polo', 'seragam']);
                                    $mockupBase = match($produk->jenis_produk) {
                                        'kaos' => 'kaos',
                                        'hoodie' => 'hoodie',
                                        'topi' => 'topi',
                                        'polo' => 'polo',
                                        'seragam' => 'seragam',
                                        default => 'kaos'
                                    };
                                @endphp

                                <!-- Hitung URL Gambar Sisi -->
                                <div x-data="{
                                    getMockupUrl() {
                                        if(activeSide === 'front') return '{{ asset('images/mockups/'.$mockupBase.'.png?v='.time()) }}';
                                        if(activeSide === 'left') return '{{ asset('images/mockups/'.$mockupBase.'_kiri.png?v='.time()) }}';
                                        if(activeSide === 'right') return '{{ asset('images/mockups/'.$mockupBase.'_kanan.png?v='.time()) }}';
                                        return '{{ asset('images/mockups/'.$mockupBase.'_belakang.png?v='.time()) }}';
                                    }
                                }" class="w-full h-full flex items-center justify-center">

                                    <!-- Base Mockup Texture -->
                                    <img :src="getMockupUrl()" 
                                         class="absolute object-contain drop-shadow-2xl opacity-90 transition-all duration-500"
                                         :class="technique === 'bordir' ? 'w-[100%] h-[100%]' : 'w-[85%] h-[85%]'"
                                         onerror="this.src='{{ asset('images/mockups/'.$mockupBase.'.png') }}'">
                                    
                                    <!-- Color Tint Layer -->
                                    <div class="absolute mix-blend-multiply transition-all duration-500"
                                         :class="technique === 'bordir' ? 'w-[100%] h-[100%]' : 'w-[85%] h-[85%]'"
                                         :style="{ 
                                            '-webkit-mask-image': `url(${getMockupUrl()})`, 
                                            '-webkit-mask-size': 'contain', 
                                            '-webkit-mask-position': 'center', 
                                            '-webkit-mask-repeat': 'no-repeat', 
                                            'mask-image': `url(${getMockupUrl()})`, 
                                            'mask-size': 'contain', 
                                            'mask-position': 'center', 
                                            'mask-repeat': 'no-repeat' 
                                         }"
                                         x-effect="if(getMockupUrl().includes('_belakang') && !'{{ $hasBack }}' && '{{ $produk->jenis_produk }}' !== 'topi') { $el.style.webkitMaskImage = `url({{ asset('images/mockups/'.$mockupBase.'.png') }})`; $el.style.maskImage = `url({{ asset('images/mockups/'.$mockupBase.'.png') }})`; }">
                                        <div class="w-full h-full transition-colors duration-300" :style="`background-color: ${baseColor};`"></div>
                                    </div>

                                </div>
                             </div>
                        </div>
                        
                        @php
                            // Dynamic Print Area Logic based on side
                            $getPrintArea = function($side) use ($produk) {
                                $jenis = strtolower($produk->jenis_produk);
                                if ($jenis === 'topi') {
                                    // Coordinat disesuaikan dengan posisi bidang gambar baru untuk setiap sisi topi
                                    if ($side === 'front') return ['width' => 120, 'height' => 85, 'top' => 280, 'left' => 180, 'label' => 'Bordir Depan'];
                                    if ($side === 'back')  return ['width' => 100, 'height' => 60, 'top' => 210, 'left' => 190, 'label' => 'Bordir Belakang'];
                                    if ($side === 'left')  return ['width' => 110, 'height' => 75, 'top' => 285, 'left' => 140, 'label' => 'Bordir Kiri'];
                                    if ($side === 'right') return ['width' => 110, 'height' => 75, 'top' => 285, 'left' => 230, 'label' => 'Bordir Kanan'];
                                }
                                if ($jenis === 'polo') return ['width' => 90, 'height' => 90, 'top' => 180, 'left' => 140, 'label' => 'Pocket'];
                                if ($jenis === 'seragam') return ['width' => 100, 'height' => 100, 'top' => 180, 'left' => 135, 'label' => 'Dada'];
                                
                                return ['width' => 220, 'height' => 320, 'top' => 120, 'left' => 130, 'label' => 'Area Cetak'];
                            };
                        @endphp
                        <script>
                            window.printAreaDims = {
                                'front': {!! json_encode($getPrintArea('front')) !!},
                                'back': {!! json_encode($getPrintArea('back')) !!},
                                'left': {!! json_encode($getPrintArea('left')) !!},
                                'right': {!! json_encode($getPrintArea('right')) !!}
                            };
                        </script>

                        <!-- Print Area Visualizer -->
                        <div class="absolute z-10 border border-dashed border-slate-600/40 pointer-events-none rounded transition-all duration-300 group" 
                             id="printAreaBox" 
                             x-effect="
                                const dims = window.printAreaDims ? (window.printAreaDims[activeSide] || window.printAreaDims['front']) : {width: 220, height: 320, top: 120, left: 130, label: 'Area Cetak'};
                                $el.style.width = dims.width + 'px';
                                $el.style.height = dims.height + 'px';
                                $el.style.top = dims.top + 'px';
                                $el.style.left = dims.left + 'px';
                                document.getElementById('printAreaLabelText').innerText = technique === 'bordir' ? 'Bordir Box' : dims.label;
                             ">
                             <span class="absolute -top-7 left-1/2 transform -translate-x-1/2 text-[10px] text-slate-600 font-black uppercase tracking-widest bg-red-50/80 px-3 py-1 rounded-full backdrop-blur border border-red-200/50 shadow-sm ">
                                 <span id="printAreaLabelText">Area</span>
                             </span>
                             <!-- Glow Corners -->
                             <div class="absolute -top-1 -left-1 w-3 h-3 border-t-2 border-l-2 border-red-400"></div>
                             <div class="absolute -top-1 -right-1 w-3 h-3 border-t-2 border-r-2 border-red-400"></div>
                             <div class="absolute -bottom-1 -left-1 w-3 h-3 border-b-2 border-l-2 border-red-400"></div>
                             <div class="absolute -bottom-1 -right-1 w-3 h-3 border-b-2 border-r-2 border-red-400"></div>
                        </div>

                        <!-- Fabric.js Canvas -->
                        @php $paFront = $getPrintArea('front'); @endphp
                        <div class="absolute z-20" 
                             style="top: {{ $paFront['top'] }}px; left: {{ $paFront['left'] }}px; width: {{ $paFront['width'] }}px; height: {{ $paFront['height'] }}px;" 
                             x-show="activeSide === 'front'">
                            <canvas id="tshirt-canvas-front" width="{{ $paFront['width'] }}" height="{{ $paFront['height'] }}"></canvas>
                        </div>
                        
                        @php $paBack = $getPrintArea('back'); @endphp
                        <div class="absolute z-20" 
                             style="top: {{ $paBack['top'] }}px; left: {{ $paBack['left'] }}px; width: {{ $paBack['width'] }}px; height: {{ $paBack['height'] }}px;" 
                             x-show="activeSide === 'back'" 
                             x-cloak>
                            <canvas id="tshirt-canvas-back" width="{{ $paBack['width'] }}" height="{{ $paBack['height'] }}"></canvas>
                        </div>
                        
                        @if(strtolower($produk->jenis_produk) == 'topi')
                        @php $paLeft = $getPrintArea('left'); @endphp
                        <div class="absolute z-20" 
                             style="top: {{ $paLeft['top'] }}px; left: {{ $paLeft['left'] }}px; width: {{ $paLeft['width'] }}px; height: {{ $paLeft['height'] }}px;" 
                             x-show="activeSide === 'left'" 
                             x-cloak>
                            <canvas id="tshirt-canvas-left" width="{{ $paLeft['width'] }}" height="{{ $paLeft['height'] }}"></canvas>
                        </div>
                        
                        @php $paRight = $getPrintArea('right'); @endphp
                        <div class="absolute z-20" 
                             style="top: {{ $paRight['top'] }}px; left: {{ $paRight['left'] }}px; width: {{ $paRight['width'] }}px; height: {{ $paRight['height'] }}px;" 
                             x-show="activeSide === 'right'" 
                             x-cloak>
                            <canvas id="tshirt-canvas-right" width="{{ $paRight['width'] }}" height="{{ $paRight['height'] }}"></canvas>
                        </div>
                        @endif
                    </div>

                </div>
            </div>

            <!-- Right Sidebar: Properties Panel (Sticky) -->
            <div id="editorControls" class="w-80 bg-white border-l border-slate-200 shadow-sm flex flex-col flex-shrink-0 z-30 hidden overflow-y-auto custom-scrollbar">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest">Pengaturan Objek</h3>
                    <button onclick="window.activeCanvas.discardActiveObject(); window.activeCanvas.requestRenderAll();" class="text-slate-400 hover:text-red-500 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-8">
                    <!-- Text Properties -->
                    <div id="textControls" class="hidden flex-col gap-6">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilih Gaya Font</label>
                            <select id="fontFamilyControl" class="w-full text-sm font-bold border-slate-200 rounded-xl py-3 pl-4 focus:ring-red-500 focus:border-red-500 bg-slate-50 cursor-pointer shadow-sm">
                                <optgroup label="Standard">
                                    <option value="Arial">Arial</option>
                                    <option value="Roboto">Roboto</option>
                                    <option value="Montserrat">Montserrat</option>
                                </optgroup>
                                <optgroup label="Display & Bold">
                                    <option value="'Bebas Neue'">Bebas Neue</option>
                                    <option value="Impact">Impact</option>
                                    <option value="Oswald">Oswald</option>
                                    <option value="Anton">Anton</option>
                                </optgroup>
                                <optgroup label="Script & Elegant">
                                    <option value="Pacifico">Pacifico</option>
                                    <option value="Lobster">Lobster</option>
                                    <option value="'Dancing Script'">Dancing Script</option>
                                    <option value="'Playfair Display'">Playfair Display</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Warna Teks</label>
                            <div class="flex items-center gap-4">
                                <input type="color" id="textColorControl" class="w-12 h-12 p-1 border border-slate-200 rounded-xl cursor-pointer bg-white shadow-sm" value="#000000">
                                <div class="flex flex-col text-xs font-bold text-slate-400">
                                    <span>HEX CODE</span>
                                    <span class="text-slate-800" id="textColorVal">#000000</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 pt-2 border-t border-slate-100">
                             <div class="flex justify-between items-center">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Outline (Stroke)</label>
                                <input type="color" id="textStrokeColor" class="w-8 h-8 p-0.5 border border-slate-200 rounded-lg cursor-pointer bg-white shadow-sm" value="#ffffff">
                             </div>
                             <input type="range" id="textStrokeWidth" min="0" max="10" value="0" class="w-full h-1.5 bg-slate-100 rounded-full appearance-none cursor-pointer accent-red-600">
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Efek Bayangan</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="textShadowToggle" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Image Properties -->
                    <div id="imageControls" class="hidden flex-col gap-4">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Opsi Gambar</p>
                        <button id="removeBgBtn" class="w-full bg-sky-50 border border-sky-100 text-sky-600 text-xs font-black py-4 rounded-xl hover:bg-sky-100 transition flex items-center justify-center gap-2 shadow-sm shadow-sky-50 uppercase tracking-widest">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <span>✨ Hapus Background</span>
                        </button>
                    </div>

                    <!-- SVG Vector Properties -->
                    <div id="svgControls" class="hidden flex-col gap-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Warna Vektor</label>
                        <div class="flex items-center gap-4">
                            <input type="color" id="svgColorControl" class="w-12 h-12 p-1 border border-slate-200 rounded-xl cursor-pointer bg-white shadow-sm" value="#000000">
                            <div class="flex flex-col text-xs font-bold text-slate-400">
                                <span>HEX CODE</span>
                                <span class="text-slate-800" id="svgColorVal">#000000</span>
                            </div>
                        </div>
                    </div>

                    <!-- Common Layer Management -->
                    <div class="pt-6 border-t border-slate-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 text-center">Urutan Lapisan</p>
                        <div class="grid grid-cols-2 gap-3">
                            <button id="bringForwardBtn" class="flex items-center justify-center gap-2 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold py-3 px-4 rounded-xl border border-slate-200 transition text-xs">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 11l7-7 7 7M5 19l7-7 7 7"></path></svg>
                                Ke Depan
                            </button>
                            <button id="sendBackwardBtn" class="flex items-center justify-center gap-2 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold py-3 px-4 rounded-xl border border-slate-200 transition text-xs">
                                <svg class="w-3 h-3 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 11l7-7 7 7M5 19l7-7 7 7"></path></svg>
                                Ke Belakang
                            </button>
                        </div>
                    </div>

                    <!-- Danger Zone -->
                    <div class="pt-8 mt-4 border-t-2 border-slate-50 border-dashed">
                        <button id="deleteObjBtn" class="w-full bg-red-50 hover:bg-red-600 hover:text-white text-red-600 font-black py-4 rounded-2xl transition border border-red-100 flex items-center justify-center gap-2 shadow-inner uppercase tracking-widest text-[11px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            <span>Hapus Objek</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <style>
        /* Custom stylings for Editor */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9; 
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1; 
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; 
        }
        /* Pattern util untuk background bg-slate-200 isometric */
        .pattern-isometric { background-image: linear-gradient(30deg, #e2e8f0 12%, transparent 12.5%, transparent 87%, #e2e8f0 87.5%, #e2e8f0), linear-gradient(150deg, #e2e8f0 12%, transparent 12.5%, transparent 87%, #e2e8f0 87.5%, #e2e8f0), linear-gradient(30deg, #e2e8f0 12%, transparent 12.5%, transparent 87%, #e2e8f0 87.5%, #e2e8f0), linear-gradient(150deg, #e2e8f0 12%, transparent 12.5%, transparent 87%, #e2e8f0 87.5%, #e2e8f0), linear-gradient(60deg, #cbd5e1 25%, transparent 25.5%, transparent 75%, #cbd5e1 75%, #cbd5e1), linear-gradient(60deg, #cbd5e1 25%, transparent 25.5%, transparent 75%, #cbd5e1 75%, #cbd5e1); background-size: 40px 70px; background-position: 0 0, 0 0, 20px 35px, 20px 35px, 0 0, 20px 35px; }
    </style>

    <!-- Script Logika Fabric.js & Kontrol Editor -->
    <script>


        function initFabricEditor() {
          try {
            // Inisialisasi Canvas Fabric (Ukuran baru: 480x600)
            const canvasFront = new fabric.Canvas('tshirt-canvas-front', {
                preserveObjectStacking: true,
                selection: true
            });
            const canvasBack = new fabric.Canvas('tshirt-canvas-back', {
                preserveObjectStacking: true,
                selection: true
            });
            let canvasLeft = null;
            let canvasRight = null;
            
            if (document.getElementById('tshirt-canvas-left')) {
                canvasLeft = new fabric.Canvas('tshirt-canvas-left', { preserveObjectStacking: true, selection: true });
                canvasRight = new fabric.Canvas('tshirt-canvas-right', { preserveObjectStacking: true, selection: true });
            }
            
            window.activeCanvas = canvasFront;

            window.switchCanvasSide = function(side) {
                if(window.activeCanvas) {
                    window.activeCanvas.discardActiveObject();
                    window.activeCanvas.renderAll();
                }
                if (typeof hideControls === 'function') hideControls();
                if(side === 'front') window.activeCanvas = canvasFront;
                else if(side === 'back') window.activeCanvas = canvasBack;
                else if(side === 'left') window.activeCanvas = canvasLeft;
                else if(side === 'right') window.activeCanvas = canvasRight;
                
                canvas = window.activeCanvas; // sync local helper
            };

            // Batasan Area Cetak (Dynamic berdasarkan PHP Match)
            @php $paDefault = $getPrintArea('front'); @endphp
            const printArea = { 
                top: {{ $paDefault['top'] }}, 
                left: {{ $paDefault['left'] }}, 
                width: {{ $paDefault['width'] }}, 
                height: {{ $paDefault['height'] }} 
            };

            // Referensi Elemen DOM
            const editorControls = document.getElementById('editorControls');
            const deleteObjBtn   = document.getElementById('deleteObjBtn');
            const bringForwardBtn = document.getElementById('bringForwardBtn');
            const sendBackwardBtn = document.getElementById('sendBackwardBtn');
            
            const textControls   = document.getElementById('textControls');
            const fontFamilyControl = document.getElementById('fontFamilyControl');
            const textColorControl  = document.getElementById('textColorControl');
            const textStrokeColor = document.getElementById('textStrokeColor');
            const textStrokeWidth = document.getElementById('textStrokeWidth');
            const textShadowToggle = document.getElementById('textShadowToggle');

            const imageControls = document.getElementById('imageControls');
            const removeBgBtn = document.getElementById('removeBgBtn');

            const svgControls = document.getElementById('svgControls');
            const svgColorControl = document.getElementById('svgColorControl');

            // --- FUNGSI GLOBAL CANVAS ---
            
            // Setup global style untuk corner control fabric
            fabric.Object.prototype.transparentCorners = false;
            fabric.Object.prototype.cornerColor = '#ffffff';
            fabric.Object.prototype.cornerStrokeColor = '#bae6fd';
            fabric.Object.prototype.borderColor = '#0284c7';
            fabric.Object.prototype.cornerSize = 14;
            fabric.Object.prototype.padding = 10;
            fabric.Object.prototype.borderDashArray = [4, 4];

            // Cukup gunakan default resize & rotate milik FabricJS. 
            // Control custom dihapus untuk stabilitas plugin pada teks dan stiker.
            if(fabric.Object.prototype.setControlsVisibility) {
                fabric.Object.prototype.setControlsVisibility({
                    mt: false, mb: false, ml: false, mr: false
                });
            }

            // Fungsi tambah TEXT
            const addTextBtn = document.getElementById('addTextBtn');
            if (addTextBtn) {
                addTextBtn.addEventListener('click', function() {
                    const text = new fabric.IText('Teks Anda', {
                        left: 20,
                        top: 20,
                        fontFamily: 'Arial',
                        fill: '#000000',
                        fontSize: 40,
                        fontWeight: 'bold',
                    });
                    window.activeCanvas.add(text);
                    window.activeCanvas.setActiveObject(text);
                    window.activeCanvas.requestRenderAll();
                });
            }

            // Ganti Font
            if(fontFamilyControl) {
                fontFamilyControl.addEventListener('change', function() {
                    const activeObj = window.activeCanvas.getActiveObject();
                    if(activeObj && activeObj.type === 'i-text') {
                        activeObj.set('fontFamily', this.value);
                        window.activeCanvas.renderAll();
                    }
                });
            }

            // Ganti Warna Font
            if(textColorControl) {
                textColorControl.addEventListener('input', function() {
                    const activeObj = window.activeCanvas.getActiveObject();
                    if(activeObj && activeObj.type === 'i-text') {
                        activeObj.set('fill', this.value);
                        window.activeCanvas.renderAll();
                    }
                });
            }

            // Stroke (Outline) Teks
            if(textStrokeColor) {
                textStrokeColor.addEventListener('input', function() {
                    const activeObj = window.activeCanvas.getActiveObject();
                    if(activeObj && activeObj.type === 'i-text') {
                        activeObj.set({ stroke: this.value, strokeWidth: parseInt(textStrokeWidth.value) });
                        window.activeCanvas.renderAll();
                    }
                });
            }
            if(textStrokeWidth) {
                textStrokeWidth.addEventListener('input', function() {
                    const activeObj = window.activeCanvas.getActiveObject();
                    if(activeObj && activeObj.type === 'i-text') {
                        activeObj.set({ stroke: textStrokeColor.value, strokeWidth: parseInt(this.value) });
                        window.activeCanvas.renderAll();
                    }
                });
            }

            // Shadow Teks
            textShadowToggle.addEventListener('change', function() {
                const activeObj = window.activeCanvas.getActiveObject();
                if(activeObj && activeObj.type === 'i-text') {
                    if(this.checked) {
                        activeObj.set('shadow', new fabric.Shadow({
                            color: 'rgba(0,0,0,0.6)',
                            blur: 4,
                            offsetX: 2,
                            offsetY: 2
                        }));
                    } else {
                        activeObj.set('shadow', null);
                    }
                    window.activeCanvas.renderAll();
                }
            });

            // Setup Layer Management
            bringForwardBtn.addEventListener('click', function() {
                const activeObj = window.activeCanvas.getActiveObject();
                if(activeObj) { window.activeCanvas.bringForward(activeObj); }
            });

            sendBackwardBtn.addEventListener('click', function() {
                const activeObj = window.activeCanvas.getActiveObject();
                if(activeObj) { window.activeCanvas.sendBackwards(activeObj); }
            });

            // Fungsi tambah STICKER/IMAGE ke canvas
            function addImageToCanvas(url) {
                // Ambil sisi aktif via Alpine.js v3
                let sideName = 'front';
                try {
                    const el = document.querySelector('[x-data]');
                    if (el && window.Alpine && window.Alpine.$data) {
                        sideName = window.Alpine.$data(el).activeSide || 'front';
                    }
                } catch(e) { /* fallback to front */ }
                
                const dims = window.printAreaDims[sideName] || window.printAreaDims['front']; 
                const pa_width = dims ? dims.width : window.activeCanvas.width;
                
                const imgEl = new Image();
                imgEl.crossOrigin = 'anonymous'; // Penting untuk avoid canvas taint saat export
                imgEl.onload = function() {
                    const img = new fabric.Image(imgEl);
                    if(img.width > pa_width) img.scaleToWidth(pa_width - 20);
                    else if(img.width < 40) img.scaleToWidth(80);
                    
                    img.set({ left: 10, top: 10 });
                    img.customType = 'custom-image';
                    window.activeCanvas.add(img);
                    window.activeCanvas.setActiveObject(img);
                    window.activeCanvas.requestRenderAll();
                };
                imgEl.onerror = function() {
                    // Coba tanpa crossOrigin jika gagal (untuk local assets)
                    const imgEl2 = new Image();
                    imgEl2.onload = function() {
                        const img = new fabric.Image(imgEl2);
                        if(img.width > pa_width) img.scaleToWidth(pa_width - 20);
                        else if(img.width < 40) img.scaleToWidth(80);
                        img.set({ left: 10, top: 10 });
                        img.customType = 'custom-image';
                        window.activeCanvas.add(img);
                        window.activeCanvas.setActiveObject(img);
                        window.activeCanvas.requestRenderAll();
                    };
                    imgEl2.onerror = function() { console.warn('Gagal memuat elemen desain:', url); };
                    imgEl2.src = url;
                };
                imgEl.src = url;
            }

            function addSVGToCanvas(url) {
                // Coba load SVG sebagai vektor parseable terlebih dahulu
                fabric.loadSVGFromURL(url, function(objects, options) {
                    if (objects && objects.length > 0) {
                        // Berhasil parse SVG sebagai vektor
                        const group = fabric.util.groupSVGElements(objects, options);
                        const targetSize = Math.min(60, window.activeCanvas.width - 20);
                        const maxDim = Math.max(group.width || 1, group.height || 1);
                        group.scale(targetSize / maxDim);
                        group.set({ left: 10, top: 10 });
                        group.customType = 'custom-svg';
                        window.activeCanvas.add(group);
                        window.activeCanvas.setActiveObject(group);
                        window.activeCanvas.requestRenderAll();
                    } else {
                        // Fallback: load sebagai Image biasa (untuk SVG dari CDN eksternal)
                        addImageToCanvas(url);
                    }
                }, null, { crossOrigin: 'anonymous' });
            }

            // --- TAB UPLOAD ---
            document.getElementById('imageLoader').addEventListener('change', function(e) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    var imgObj = new Image();
                    imgObj.src = event.target.result;
                    imgObj.onload = function() {
                        var img = new fabric.Image(imgObj);
                        const pa_width = window.activeCanvas.width;
                        if(img.width > pa_width) img.scaleToWidth(pa_width - 20);
                        img.set({ left: 10, top: 10 });
                        img.customType = 'custom-image';
                        window.activeCanvas.add(img);
                        window.activeCanvas.setActiveObject(img);
                    }
                }
                reader.readAsDataURL(e.target.files[0]);
                e.target.value = ''; // reset
            });

            // --- TAB TEMPLATE ---
            document.querySelectorAll('.template-item').forEach(item => {
                item.addEventListener('click', function() { addImageToCanvas(this.getAttribute('data-url')); });
            });

            // --- TAB STIKER (Iconify CDN Langsung - Tanpa Proxy Server) ---
            const stickerSearchInput = document.getElementById('stickerSearchInput');
            const searchStickerBtn   = document.getElementById('searchStickerBtn');
            const stickersContainer  = document.getElementById('stickersContainer');

            function loadStickers(query = 'heart') {
                if(!stickersContainer) return;
                stickersContainer.innerHTML = '<div class="col-span-2 text-center py-5"><svg class="animate-spin h-5 w-5 text-red-500 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><span class="text-xs text-red-600 block font-bold mt-1">Memuat ikon...</span></div>';
                
                // Gunakan Proxy Server Backend (Mendukung Iconify + Dicebear) via relative path to avoid port mismatch
                fetch(`/customer/api/stickers?q=${encodeURIComponent(query)}`)
                    .then(res => {
                        if (!res.ok) throw new Error('Proxy API error: ' + res.status);
                        return res.json();
                    })
                    .then(json => {
                        stickersContainer.innerHTML = '';
                        const icons = json.data || [];
                        if(icons.length > 0) {
                            icons.forEach(icon => {
                                const btn = document.createElement('div');
                                btn.className = 'relative bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:border-red-400 hover:shadow-md hover:shadow-red-50 transition-all duration-200 flex items-center justify-center w-full h-24 group overflow-hidden';
                                btn.title = icon.name;
                                btn.innerHTML = `<img src="${icon.url}" loading="lazy" 
                                    class="w-14 h-14 object-contain group-hover:scale-110 transition-transform duration-200"
                                    onerror="this.parentElement.style.display='none'">`;
                                btn.addEventListener('click', function() {
                                    if(icon.source === 'Iconify') {
                                        addSVGToCanvas(icon.url);
                                    } else {
                                        addImageToCanvas(icon.url);
                                    }
                                });
                                stickersContainer.appendChild(btn);
                            });
                        } else {
                            stickersContainer.innerHTML = '<div class="col-span-2 text-center text-xs text-slate-500 py-6"><div class="text-2xl mb-2">🔍</div>Ikon tidak ditemukan untuk kata kunci tersebut.</div>';
                        }
                    }).catch((e) => {
                        console.error("Sticker Fetch Error:", e);
                        // Fallback: tampilkan emoji/ikon bawaan jika API gagal
                        showFallbackStickers();
                    });
            }

            // Stiker fallback bawaan jika API tidak dapat diakses
            function showFallbackStickers() {
                if(!stickersContainer) return;
                const fallbackIcons = [
                    { name: 'bintang', url: 'https://api.iconify.design/twemoji/star.svg' },
                    { name: 'hati', url: 'https://api.iconify.design/twemoji/red-heart.svg' },
                    { name: 'api', url: 'https://api.iconify.design/twemoji/fire.svg' },
                    { name: 'mahkota', url: 'https://api.iconify.design/twemoji/crown.svg' },
                    { name: 'petir', url: 'https://api.iconify.design/twemoji/high-voltage.svg' },
                    { name: 'berlian', url: 'https://api.iconify.design/twemoji/gem-stone.svg' },
                    { name: 'bulseye', url: 'https://api.iconify.design/twemoji/bullseye.svg' },
                    { name: 'roket', url: 'https://api.iconify.design/twemoji/rocket.svg' },
                    { name: 'musik', url: 'https://api.iconify.design/twemoji/musical-notes.svg' },
                    { name: 'senyum', url: 'https://api.iconify.design/twemoji/smiling-face.svg' },
                ];
                stickersContainer.innerHTML = '<div class="col-span-2 text-[10px] text-slate-400 text-center mb-2 font-bold">Ikon Default</div>';
                fallbackIcons.forEach(ic => {
                    const btn = document.createElement('div');
                    btn.className = 'relative bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:border-red-400 hover:shadow-md transition-all duration-200 flex items-center justify-center w-full h-24 group overflow-hidden';
                    btn.title = ic.name;
                    btn.innerHTML = `<img src="${ic.url}" loading="lazy" class="w-14 h-14 object-contain group-hover:scale-110 transition-transform duration-200" onerror="this.parentElement.style.display='none'">`;
                    btn.addEventListener('click', () => addSVGToCanvas(ic.url));
                    stickersContainer.appendChild(btn);
                });
            }
            
            // Bungkus inisialisasi stiker agar error dari luar tidak membatasinya
            try {
                // Auto load default stickers (twemoji-based populer)
                loadStickers('star');
                if(searchStickerBtn) {
                    searchStickerBtn.addEventListener('click', () => {
                        const q = stickerSearchInput ? stickerSearchInput.value.trim() : '';
                        loadStickers(q || 'star');
                    });
                }
                if(stickerSearchInput) {
                    stickerSearchInput.addEventListener('keypress', (e) => {
                        if(e.key === 'Enter') loadStickers(stickerSearchInput.value.trim() || 'star');
                    });
                }
            } catch(e) {
                console.error("Error at Sticker Init:", e);
            }

            // --- EDITOR CONTROLS LOGIC ---
            const canvasesToHandle = [canvasFront, canvasBack];
            if (canvasLeft) canvasesToHandle.push(canvasLeft);
            if (canvasRight) canvasesToHandle.push(canvasRight);

            canvasesToHandle.forEach(c => {
                c.on('selection:created', showControls);
                c.on('selection:updated', showControls);
                c.on('selection:cleared', hideControls);
            });

            function showControls(e) {
                editorControls.classList.remove('hidden');
                editorControls.classList.add('flex');
                const activeObj = (e && e.selected) ? e.selected[0] : window.activeCanvas.getActiveObject();
                if(!activeObj) return;
                
                // Hide All Contextual Controls first
                textControls.classList.add('hidden'); textControls.classList.remove('flex');
                imageControls.classList.add('hidden'); imageControls.classList.remove('flex');
                svgControls.classList.add('hidden'); svgControls.classList.remove('flex');

                if(activeObj && activeObj.type === 'i-text') {
                    textControls.classList.remove('hidden');
                    textControls.classList.add('flex');
                    fontFamilyControl.value = activeObj.fontFamily.replace(/["']/g, "");
                    textColorControl.value = activeObj.fill;
                    document.getElementById('textColorVal').textContent = activeObj.fill.toUpperCase();
                    textStrokeColor.value = activeObj.stroke || '#ffffff';
                    textStrokeWidth.value = activeObj.strokeWidth || 0;
                    textShadowToggle.checked = !!activeObj.shadow;
                } 
                else if (activeObj && activeObj.type === 'image' && activeObj.customType === 'custom-image') {
                    imageControls.classList.remove('hidden');
                    imageControls.classList.add('flex');
                }
                else if (activeObj && (activeObj.type === 'group' || activeObj.type === 'path') && activeObj.customType === 'custom-svg') {
                    svgControls.classList.remove('hidden');
                    svgControls.classList.add('flex');
                    let targetColor = '#000000';
                    if(activeObj.type === 'group' && activeObj._objects && activeObj._objects.length > 0) {
                        targetColor = activeObj._objects[0].fill || '#000000';
                    } else {
                        targetColor = activeObj.fill || '#000000';
                    }
                    if(typeof targetColor === 'string' && targetColor.startsWith('#')) {
                        svgColorControl.value = targetColor;
                        document.getElementById('svgColorVal').textContent = targetColor.toUpperCase();
                    }
                }
            }

            // Sync color hex values
            if(textColorControl) { textColorControl.addEventListener('input', () => { document.getElementById('textColorVal').textContent = textColorControl.value.toUpperCase(); }); }
            if(svgColorControl) { svgColorControl.addEventListener('input', () => { document.getElementById('svgColorVal').textContent = svgColorControl.value.toUpperCase(); }); }

            // Logic Remove Background (Magic Eraser) for Images
            removeBgBtn.addEventListener('click', function() {
                const activeObj = window.activeCanvas.getActiveObject();
                if(activeObj && activeObj.type === 'image') {
                    // Cek jika filter RemoveColor sudah ada
                    const hasFilter = activeObj.filters.some(f => f.type === 'RemoveColor');
                    if(hasFilter) {
                        alert('Background sudah dihancurkan pada gambar ini.');
                        return;
                    }

                    const oldHtml = this.innerHTML;
                    this.innerHTML = 'Memproses...';
                    this.disabled = true;

                    // Apply Fabric.js RemoveColor filter untuk membuang warna putih/polos
                    // distance adalah tingkat sensitivitas toleransi warna putih (mirip magic wand tolerance)
                    const filter = new fabric.Image.filters.RemoveColor({
                        color: '#FFFFFF',
                        distance: 0.12 
                    });

                    activeObj.filters.push(filter);
                    activeObj.applyFilters();
                    window.activeCanvas.renderAll();

                    setTimeout(() => {
                        this.innerHTML = oldHtml;
                        this.disabled = false;
                    }, 500);
                }
            });

            // Logic Merubah Warna Dynamic pada SVG
            svgColorControl.addEventListener('input', function() {
                const activeObj = window.activeCanvas.getActiveObject();
                if(activeObj && activeObj.type === 'group' && activeObj.customType === 'custom-svg') {
                    const newColor = this.value;
                    
                    function applyDeepColor(obj, col) {
                        if(obj._objects) {
                            obj._objects.forEach(child => applyDeepColor(child, col));
                        } else {
                            if(obj.fill && obj.fill !== 'none' && obj.fill !== 'transparent') obj.set('fill', col);
                            if(obj.stroke && obj.stroke !== 'none' && obj.stroke !== 'transparent') obj.set('stroke', col);
                        }
                    }
                    
                    // Loop setiap elemen di dalam grup vektor SVG secara rekursif
                    applyDeepColor(activeObj, newColor);
                    window.activeCanvas.renderAll();
                }
            });

            function hideControls() {
                editorControls.classList.add('hidden');
                editorControls.classList.remove('flex');
            }

            deleteObjBtn.addEventListener('click', function() {
                const activeObj = window.activeCanvas.getActiveObject();
                if(activeObj) { window.activeCanvas.remove(activeObj); hideControls(); }
            });

            // --- SUBMIT SAVE TO SERVER ---
            document.getElementById('saveDesignBtn').addEventListener('click', function() {
                canvasFront.discardActiveObject(); 
                canvasFront.renderAll();
                canvasBack.discardActiveObject(); 
                canvasBack.renderAll();
                if (canvasLeft) {
                    canvasLeft.discardActiveObject();
                    canvasLeft.renderAll();
                }
                if (canvasRight) {
                    canvasRight.discardActiveObject();
                    canvasRight.renderAll();
                }

                const activeBaseColor = window.activeBaseColorLocal || '#ffffff';
                const lebarCm = 30; // Proporsi standar A3 sablon
                const tinggiCm = 45;

                let frontDataURL = '';
                let backDataURL = '';
                let leftDataURL = '';
                let rightDataURL = '';

                // Ambil data jika ada objek (atau jika canvas kosong, kita kirimkan blank untuk depan sebagai mandatory)
                frontDataURL = canvasFront.toDataURL({ format: 'png', quality: 1, multiplier: 4 });
                
                if (canvasBack && canvasBack.getObjects().length > 0) {
                    backDataURL = canvasBack.toDataURL({ format: 'png', quality: 1, multiplier: 4 });
                }
                if (canvasLeft && canvasLeft.getObjects().length > 0) {
                    leftDataURL = canvasLeft.toDataURL({ format: 'png', quality: 1, multiplier: 4 });
                }
                if (canvasRight && canvasRight.getObjects().length > 0) {
                    rightDataURL = canvasRight.toDataURL({ format: 'png', quality: 1, multiplier: 4 });
                }

                const payload = {
                    _token: '{{ csrf_token() }}',
                    id_produk: '{{ $produk->id_produk }}',
                    file_desain: frontDataURL, 
                    file_desain_belakang: backDataURL,
                    file_desain_kiri: leftDataURL,
                    file_desain_kanan: rightDataURL,
                    lebar_cm: lebarCm,
                    tinggi_cm: tinggiCm,
                    warna_baju: activeBaseColor,
                    tipe_proses: new URLSearchParams(window.location.search).get('technique') || 'sablon'
                };

                if (backDataURL !== '') {
                    payload.file_desain_belakang = backDataURL;
                    payload.lebar_cm_belakang = lebarCm;
                    payload.tinggi_cm_belakang = tinggiCm;
                }
                if (leftDataURL !== '') {
                    payload.file_desain_kiri = leftDataURL;
                    payload.lebar_cm_kiri = lebarCm;
                    payload.tinggi_cm_kiri = tinggiCm;
                }
                if (rightDataURL !== '') {
                    payload.file_desain_kanan = rightDataURL;
                    payload.lebar_cm_kanan = lebarCm;
                    payload.tinggi_cm_kanan = tinggiCm;
                }

                const oldText = this.innerHTML;
                this.innerHTML = 'Memproses... ⏳';
                this.disabled = true;

                let submitUrl = '/customer/design';
                let httpMethod = 'POST';
                
                @if($desainRevisi)
                    submitUrl = '/customer/design/{{ $desainRevisi->id_desain }}';
                    payload._method = 'PATCH';
                @endif

                fetch(submitUrl, {
                    method: httpMethod,
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        window.location.href = data.redirect_url;
                    } else { alert('Gagal menyimpan desain!'); }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan koneksi.');
                }).finally(() => {
                    this.innerHTML = oldText;
                    this.disabled = false;
                });
            });
          } catch(err) {
             console.error("FATAL ERROR IN EDITOR JS:", err);
             const errorDiv = document.createElement('div');
             errorDiv.style.cssText = 'position:fixed; top:0; left:0; right:0; background:red; color:white; z-index:9999; padding:20px;';
             errorDiv.innerHTML = "Error in JS initializing: " + err.message + "<br><pre>" + err.stack + "</pre>";
             document.body.appendChild(errorDiv);
             alert("Error in JS initializing: " + err.message);
          }
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFabricEditor);
        } else {
            initFabricEditor();
        }
    </script>
</x-app-layout>
