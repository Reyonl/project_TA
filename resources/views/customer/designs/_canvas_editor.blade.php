{{-- Canvas Editor Partial - used in Step 2 (front) and Step 3 (others) --}}
<script src="{{ asset('js/fabric-smart-guides.js') }}"></script>
@php
    $hasBack = in_array($produk->jenis_produk, ['kaos', 'hoodie', 'polo', 'seragam']);
    $isPanjang = \Illuminate\Support\Str::contains(strtolower($produk->nama_produk), 'panjang');
    $mockupBase = match($produk->jenis_produk) {
        'kaos' => $isPanjang ? 'kaos_panjang' : 'kaos',
        'hoodie' => 'hoodie',
        'polo' => 'polo',
        'seragam' => 'seragam',
        default => 'kaos'
    };
    $getPrintArea = function($side) use ($produk) {
        $jenis = strtolower($produk->jenis_produk);
        if ($side === 'front') {
            if ($jenis === 'polo') return ['width' => 90, 'height' => 90, 'top' => 160, 'left' => 265, 'label' => 'Pocket'];
            if ($jenis === 'seragam') return ['width' => 100, 'height' => 100, 'top' => 180, 'left' => 135, 'label' => 'Dada'];
        }
        if (in_array($side, ['left', 'right'])) {
            return ['width' => 140, 'height' => 320, 'top' => 140, 'left' => 170, 'label' => 'Samping'];
        }
        return ['width' => 220, 'height' => 320, 'top' => 120, 'left' => 130, 'label' => 'Area Cetak'];
    };
@endphp

<div class="flex h-[calc(100vh-140px)] editor-main-container flex-col md:flex-row relative bg-slate-50 overflow-hidden">
    
    {{-- Mobile Backdrop for Sidebar --}}
    <div x-show="sidebarOpen" x-transition.opacity class="mobile-backdrop md:hidden" @click="sidebarOpen = false" x-cloak></div>

    {{-- Left Icon Navbar (Desktop) / Bottom Toolbar (Mobile) --}}
    <div class="w-full md:w-16 bg-white border-t md:border-t-0 md:border-r border-slate-200 flex flex-row md:flex-col items-center py-2 md:py-4 px-2 md:px-0 gap-2 md:gap-4 z-[60] md:z-30 shadow-[0_-4px_10px_rgba(0,0,0,0.05)] md:shadow-sm flex-shrink-0 order-last md:order-first justify-around md:justify-start fixed md:relative bottom-0 left-0 right-0 h-[64px] md:h-auto pb-[env(safe-area-inset-bottom)] md:pb-0">
        <button @click="activeTab = 'templates'; sidebarOpen = true" :class="activeTab === 'templates' && sidebarOpen ? 'text-red-600 bg-red-50' : 'text-slate-500 hover:text-red-600 hover:bg-slate-50'" class="w-12 h-12 flex flex-col items-center justify-center rounded-xl transition">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
            <span class="text-[9px] font-bold uppercase tracking-tighter hidden md:block">Tools</span>
        </button>
        <button @click="activeTab = 'upload'; sidebarOpen = true" :class="activeTab === 'upload' && sidebarOpen ? 'text-red-600 bg-red-50' : 'text-slate-500 hover:text-red-600 hover:bg-slate-50'" class="w-12 h-12 flex flex-col items-center justify-center rounded-xl transition">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            <span class="text-[9px] font-bold uppercase tracking-tighter hidden md:block">Upload</span>
        </button>
        <button @click="activeTab = 'stickers'; sidebarOpen = true" :class="activeTab === 'stickers' && sidebarOpen ? 'text-red-600 bg-red-50' : 'text-slate-500 hover:text-red-600 hover:bg-slate-50'" class="w-12 h-12 flex flex-col items-center justify-center rounded-xl transition">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-[9px] font-bold uppercase tracking-tighter hidden md:block">Stiker</span>
        </button>
        <button @click="activeTab = 'text'; sidebarOpen = true" :class="activeTab === 'text' && sidebarOpen ? 'text-red-600 bg-red-50' : 'text-slate-500 hover:text-red-600 hover:bg-slate-50'" class="w-12 h-12 flex flex-col items-center justify-center rounded-xl transition">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            <span class="text-[9px] font-bold uppercase tracking-tighter hidden md:block">Teks</span>
        </button>
        <button @click="activeTab = 'layers'; sidebarOpen = true" :class="activeTab === 'layers' && sidebarOpen ? 'text-red-600 bg-red-50' : 'text-slate-500 hover:text-red-600 hover:bg-slate-50'" class="w-12 h-12 flex flex-col items-center justify-center rounded-xl transition">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l-8 4 8 4 8-4-8-4z M2 10l10 5 10-5 M2 15l10 5 10-5"></path></svg>
            <span class="text-[9px] font-bold uppercase tracking-tighter hidden md:block">Layers</span>
        </button>
        <button id="mobileEditBtn" type="button" onclick="if(typeof toggleMobileProperties === 'function') toggleMobileProperties();" class="w-12 h-12 md:hidden flex flex-col items-center justify-center rounded-xl transition text-slate-500 hover:text-red-600 hover:bg-slate-50 hidden">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
        </button>
        <div class="mt-auto pb-4 hidden md:block">
            <button @click="sidebarOpen = !sidebarOpen" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-100 transition text-slate-400">
                <svg class="w-5 h-5 transition-transform duration-300" :class="!sidebarOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
            </button>
        </div>
    </div>

    {{-- Sidebar Tools Panel --}}
    <div x-show="sidebarOpen" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full md:translate-y-0 md:-translate-x-full"
         x-transition:enter-end="translate-y-0 md:translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0 md:translate-x-0"
         x-transition:leave-end="translate-y-full md:translate-y-0 md:-translate-x-full"
         class="w-full md:w-72 bg-white border-r border-slate-200 shadow-sm flex flex-col flex-shrink-0 z-50 md:z-20 editor-sidebar">
        
        {{-- Mobile Drag Handle --}}
        <div class="md:hidden w-full pt-3 pb-1 bg-slate-50/50 rounded-t-[1.25rem] cursor-pointer" @click="sidebarOpen = false">
            <div class="sheet-handle"></div>
        </div>

        <div class="p-4 md:p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest" x-text="activeTab === 'templates' ? 'Template Desain' : (activeTab === 'upload' ? 'Upload Gambar' : (activeTab === 'stickers' ? 'Cari Stiker' : (activeTab === 'layers' ? 'Lapisan Desain' : 'Tambahkan Teks')))"></h3>
            <button @click="sidebarOpen = false" class="text-slate-400 hover:text-slate-600 hidden md:block">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-4 md:p-5 pb-24 md:pb-5 custom-scrollbar">
            {{-- Tab: TEMPLATE --}}
            <div x-show="activeTab === 'templates'" class="space-y-4">
                <p class="text-[11px] font-bold text-slate-400 mb-2 uppercase tracking-tight">Katalog Aset Desain</p>
                <div class="grid grid-cols-2 md:grid-cols-2 gap-3">
                    @forelse($templates as $template)
                    <div class="bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-red-500 hover:shadow-lg hover:shadow-red-100 transition aspect-square flex items-center justify-center bg-slate-50 p-2 template-item group" data-url="{{ Storage::url($template->file_template) }}">
                        <img src="{{ Storage::url($template->file_template) }}" alt="Template" class="w-full h-full object-contain pointer-events-none group-hover:scale-110 transition-transform">
                    </div>
                    @empty
                    <div class="col-span-2 text-center text-sm text-slate-400 py-4">Belum ada template.</div>
                    @endforelse
                </div>
            </div>
            {{-- Tab: UPLOAD --}}
            <div x-show="activeTab === 'upload'" style="display: none;" class="space-y-4">
                <label class="block w-full border-2 border-dashed border-red-100 rounded-2xl p-6 md:p-8 text-center hover:bg-red-50 hover:border-red-300 cursor-pointer transition group">
                    <input type="file" id="imageLoader" accept="image/png, image/jpeg, image/svg+xml" class="hidden"/>
                    <div class="text-red-500 mb-3 group-hover:scale-125 transition-transform duration-300">
                        <svg class="w-10 h-10 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    </div>
                    <span class="text-red-600 font-black block text-sm">KLIK UNTUK UNGGAH</span>
                    <span class="text-slate-400 text-[10px] mt-1 block font-bold">PNG / JPG / SVG (Max 2MB)</span>
                </label>
            </div>
            {{-- Tab: STIKER --}}
            <div x-show="activeTab === 'stickers'" style="display: none;" class="flex flex-col h-full bg-white">
                <div class="flex mb-4 group">
                    <input type="text" id="stickerSearchInput" placeholder="Cari ikon..." class="flex-1 border border-slate-200 rounded-l-xl px-3 py-2 text-sm focus:ring-red-500 focus:border-red-500 bg-slate-50">
                    <button id="searchStickerBtn" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-r-xl text-sm font-bold transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </div>
                <div id="stickersContainer" class="grid grid-cols-3 md:grid-cols-2 gap-3 overflow-y-auto pb-4 custom-scrollbar">
                    <div class="col-span-3 md:col-span-2 text-center text-xs text-slate-400 py-4 italic tracking-widest">Memuat library...</div>
                </div>
            </div>
            {{-- Tab: TEKS --}}
            <div x-show="activeTab === 'text'" style="display: none;" class="space-y-4">
                <button id="addTextBtn" class="w-full bg-red-600 text-white font-black py-4 px-4 rounded-2xl shadow-xl shadow-red-100 hover:bg-red-500 transition hover:-translate-y-1">+ TAMBAH TEKS BARU</button>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                    <p class="text-[10px] text-slate-500 text-center font-bold leading-relaxed">Pilih objek teks pada kanvas untuk memunculkan panel pengaturan font dan warna.</p>
                </div>
            </div>
            {{-- Tab: LAYERS --}}
            <div x-show="activeTab === 'layers'" style="display: none;" class="flex flex-col h-full bg-white">
                <p class="text-[11px] font-bold text-slate-400 mb-2 uppercase tracking-tight">Daftar Lapisan Desain</p>
                <div id="layersListContainer" class="flex-1 overflow-y-auto pb-4 custom-scrollbar">
                    <div class="text-center text-xs text-slate-400 py-4 italic tracking-widest">Belum ada objek.</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Canvas Area --}}
    <div class="flex-1 flex flex-col relative bg-slate-100 overflow-hidden order-first md:order-none z-10 pb-16 md:pb-0">
        {{-- Step Label --}}
        <div class="h-14 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-4 md:px-6 shadow-sm z-10 w-full flex-shrink-0">
            <div class="flex items-center gap-2 md:gap-4">
                <span class="text-xs md:text-sm font-black text-slate-800 uppercase tracking-widest" x-text="activeSide === 'front' ? 'Bagian Depan' : (activeSide === 'back' ? 'Bagian Belakang' : (activeSide === 'left' ? 'Samping Kiri' : 'Samping Kanan'))"></span>
                <span class="text-[10px] md:text-xs font-bold px-2 md:px-3 py-1 rounded-full bg-red-100 text-red-700" x-text="'STEP ' + currentStep"></span>
            </div>
        </div>

        {{-- Canvas Workspace --}}
        <div class="flex-1 overflow-hidden flex justify-center items-center relative bg-slate-50 mobile-canvas-scaler" id="canvasScalerWrapper">
            <div class="relative shadow-2xl rounded-xl overflow-hidden pointer-events-auto flex items-center justify-center bg-slate-100 origin-center" id="mockupContainer" style="width: 480px; height: 600px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);">
                {{-- Side Indicator --}}
                <div class="absolute top-4 left-4 z-30 pointer-events-none">
                    <span class="bg-indigo-600/90 text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-lg border border-indigo-400/50 uppercase tracking-widest backdrop-blur-sm" x-text="activeSide === 'front' ? 'Depan' : (activeSide === 'back' ? 'Belakang' : activeSide.toUpperCase())">Depan</span>
                </div>

                {{-- Background Mockup --}}
                <div class="absolute inset-0 z-0 pointer-events-none" id="baseColorContainer">
                    <div class="w-full h-full flex items-center justify-center relative overflow-hidden">
                        <div x-data="{
                            getMockupUrl() {
                                if(activeSide === 'front') return '{{ asset('images/mockups/'.$mockupBase.'.png?v='.time()) }}';
                                if(activeSide === 'left') return '{{ asset('images/mockups/'.$mockupBase.'_samping_kiri.png?v='.time()) }}';
                                if(activeSide === 'right') return '{{ asset('images/mockups/'.$mockupBase.'_samping_kanan.png?v='.time()) }}';
                                return '{{ asset('images/mockups/'.$mockupBase.'_belakang.png?v='.time()) }}';
                            }
                        }" class="w-full h-full flex items-center justify-center">
                            <div class="absolute inset-0 m-auto transition-all duration-500 drop-shadow-2xl bg-contain bg-center bg-no-repeat opacity-90 w-[85%] h-[85%]"
                                 :style="{ 
                                     'background-color': baseColor,
                                     'background-image': `url(${getMockupUrl()})`,
                                     'background-blend-mode': 'multiply',
                                     '-webkit-mask-image': `url(${getMockupUrl()})`, '-webkit-mask-size': 'contain', '-webkit-mask-position': 'center', '-webkit-mask-repeat': 'no-repeat',
                                     'mask-image': `url(${getMockupUrl()})`, 'mask-size': 'contain', 'mask-position': 'center', 'mask-repeat': 'no-repeat'
                                 }"></div>
                        </div>
                    </div>
                </div>

                @php $pa = $getPrintArea('front'); @endphp
                <script>
                    window.printAreaDims = window.printAreaDims || {};
                    window.printAreaDims['front'] = {!! json_encode($getPrintArea('front')) !!};
                    window.printAreaDims['back'] = {!! json_encode($getPrintArea('back')) !!};
                    window.printAreaDims['left'] = {!! json_encode($getPrintArea('left')) !!};
                    window.printAreaDims['right'] = {!! json_encode($getPrintArea('right')) !!};
                </script>

                {{-- Print Area Visualizer --}}
                <div class="absolute z-10 border border-dashed border-slate-600/40 pointer-events-none rounded transition-all duration-300" 
                     id="printAreaBox"
                     x-effect="
                        const dims = window.printAreaDims ? (window.printAreaDims[activeSide] || window.printAreaDims['front']) : {width: 220, height: 320, top: 120, left: 130};
                        $el.style.width = dims.width + 'px'; $el.style.height = dims.height + 'px'; $el.style.top = dims.top + 'px'; $el.style.left = dims.left + 'px';
                     ">
                     <span class="absolute -top-7 left-1/2 transform -translate-x-1/2 text-[9px] text-slate-600 font-black uppercase tracking-widest bg-red-50/80 px-2.5 py-1 rounded-full backdrop-blur border border-red-200/50 shadow-sm whitespace-nowrap">Area Cetak</span>
                    <div class="absolute -top-1 -left-1 w-3 h-3 border-t-2 border-l-2 border-red-400"></div>
                    <div class="absolute -top-1 -right-1 w-3 h-3 border-t-2 border-r-2 border-red-400"></div>
                    <div class="absolute -bottom-1 -left-1 w-3 h-3 border-b-2 border-l-2 border-red-400"></div>
                    <div class="absolute -bottom-1 -right-1 w-3 h-3 border-b-2 border-r-2 border-red-400"></div>
                </div>

                {{-- Fabric Canvas Front --}}
                <div class="absolute z-20" style="top: {{ $pa['top'] }}px; left: {{ $pa['left'] }}px; width: {{ $pa['width'] }}px; height: {{ $pa['height'] }}px;" x-show="activeSide === 'front'">
                    <canvas id="tshirt-canvas-front" width="{{ $pa['width'] }}" height="{{ $pa['height'] }}"></canvas>
                </div>
                @php $paBack = $getPrintArea('back'); @endphp
                {{-- Fabric Canvas Back --}}
                <div class="absolute z-20" style="top: {{ $paBack['top'] }}px; left: {{ $paBack['left'] }}px; width: {{ $paBack['width'] }}px; height: {{ $paBack['height'] }}px;" x-show="activeSide === 'back'" x-cloak>
                    <canvas id="tshirt-canvas-back" width="{{ $paBack['width'] }}" height="{{ $paBack['height'] }}"></canvas>
                </div>
                @php $paLeft = $getPrintArea('left'); @endphp
                {{-- Fabric Canvas Left --}}
                <div class="absolute z-20" style="top: {{ $paLeft['top'] }}px; left: {{ $paLeft['left'] }}px; width: {{ $paLeft['width'] }}px; height: {{ $paLeft['height'] }}px;" x-show="activeSide === 'left'" x-cloak>
                    <canvas id="tshirt-canvas-left" width="{{ $paLeft['width'] }}" height="{{ $paLeft['height'] }}"></canvas>
                </div>
                @php $paRight = $getPrintArea('right'); @endphp
                {{-- Fabric Canvas Right --}}
                <div class="absolute z-20" style="top: {{ $paRight['top'] }}px; left: {{ $paRight['left'] }}px; width: {{ $paRight['width'] }}px; height: {{ $paRight['height'] }}px;" x-show="activeSide === 'right'" x-cloak>
                    <canvas id="tshirt-canvas-right" width="{{ $paRight['width'] }}" height="{{ $paRight['height'] }}"></canvas>
                </div>
            </div>
            
            {{-- Zoom & Pan Controls --}}
            <div class="absolute bottom-4 right-4 flex flex-col gap-2 z-30">
                <button type="button" id="zoomInBtn" class="w-10 h-10 bg-white/90 backdrop-blur rounded-full shadow-md text-slate-700 hover:text-sky-500 hover:bg-sky-50 flex items-center justify-center transition border border-slate-200" title="Zoom In (Scroll Up)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </button>
                <button type="button" id="zoomResetBtn" class="w-10 h-10 bg-white/90 backdrop-blur rounded-full shadow-md text-slate-700 hover:text-sky-500 hover:bg-sky-50 flex items-center justify-center transition border border-slate-200 text-[10px] font-black" title="Reset Zoom">
                    100%
                </button>
                <button type="button" id="zoomOutBtn" class="w-10 h-10 bg-white/90 backdrop-blur rounded-full shadow-md text-slate-700 hover:text-sky-500 hover:bg-sky-50 flex items-center justify-center transition border border-slate-200" title="Zoom Out (Scroll Down)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </button>
            </div>

        </div>
    </div>

    {{-- Right Sidebar: Properties Panel --}}
    {{-- Mobile Backdrop --}}
    <div id="editorControlsBackdrop" class="mobile-backdrop md:hidden hidden z-40" onclick="window.activeCanvas.discardActiveObject(); window.activeCanvas.requestRenderAll();"></div>
    
    <div id="editorControls" class="w-full md:w-80 bg-white border-l border-slate-200 shadow-sm flex flex-col flex-shrink-0 z-50 md:z-30 hidden overflow-y-auto custom-scrollbar editor-properties">
        {{-- Mobile Drag Handle --}}
        <div class="md:hidden w-full pt-3 pb-1 bg-slate-50/50 rounded-t-[1.25rem] cursor-pointer" onclick="window.activeCanvas.discardActiveObject(); window.activeCanvas.requestRenderAll();">
            <div class="sheet-handle"></div>
        </div>

        <div class="p-4 md:p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="font-black text-slate-800 text-sm uppercase tracking-widest">Pengaturan Objek</h3>
            <button onclick="window.activeCanvas.discardActiveObject(); window.activeCanvas.requestRenderAll();" class="text-slate-400 hover:text-red-500 transition hidden md:block">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="p-5 md:p-6 space-y-6 md:space-y-8 pb-[max(1.25rem,env(safe-area-inset-bottom))]">
            {{-- Sablon Size Picker for Selected Object --}}
            {{-- HIDDEN: Ukuran sablon disembunyikan sementara, data tetap tersimpan di JS --}}
            <div id="objectSablonSizeControl" class="space-y-3 pb-5 md:pb-6 border-b border-slate-100" style="display: none !important;">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Ukuran Sablon Objek</label>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" id="btnSizeA5" onclick="setObjectSablonSize('a5')" class="size-btn flex flex-col items-center justify-center p-2 rounded-xl border border-slate-200 bg-slate-50 hover:border-red-400 transition cursor-pointer">
                        <span class="text-[11px] md:text-xs font-black text-slate-800">A5 Logo</span>
                        <span class="text-[9px] text-slate-400 mt-0.5">10x10 cm</span>
                        <span class="text-[9px] md:text-[10px] font-bold text-red-600 mt-1">Rp 10k</span>
                    </button>
                    <button type="button" id="btnSizeA4" onclick="setObjectSablonSize('a4')" class="size-btn flex flex-col items-center justify-center p-2 rounded-xl border border-slate-200 bg-slate-50 hover:border-red-400 transition cursor-pointer">
                        <span class="text-[11px] md:text-xs font-black text-slate-800">A4</span>
                        <span class="text-[9px] text-slate-400 mt-0.5">20x25 cm</span>
                        <span class="text-[9px] md:text-[10px] font-bold text-red-600 mt-1">Rp 25k</span>
                    </button>
                    <button type="button" id="btnSizeA3" onclick="setObjectSablonSize('a3')" class="size-btn flex flex-col items-center justify-center p-2 rounded-xl border border-slate-200 bg-slate-50 hover:border-red-400 transition cursor-pointer">
                        <span class="text-[11px] md:text-xs font-black text-slate-800">A3</span>
                        <span class="text-[9px] text-slate-400 mt-0.5">25x35 cm</span>
                        <span class="text-[9px] md:text-[10px] font-bold text-red-600 mt-1">Rp 35k</span>
                    </button>
                </div>
            </div>

            {{-- Text Properties --}}
            <div id="textControls" class="hidden flex-col gap-5 md:gap-6">
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilih Gaya Font</label>
                    <select id="fontFamilyControl" class="w-full text-sm font-bold border-slate-200 rounded-xl py-3 pl-4 focus:ring-red-500 focus:border-red-500 bg-slate-50 cursor-pointer shadow-sm">
                        <optgroup label="Standard"><option value="Arial">Arial</option><option value="Roboto">Roboto</option><option value="Montserrat">Montserrat</option></optgroup>
                        <optgroup label="Display & Bold"><option value="'Bebas Neue'">Bebas Neue</option><option value="Impact">Impact</option><option value="Oswald">Oswald</option><option value="Anton">Anton</option></optgroup>
                        <optgroup label="Script & Elegant"><option value="Pacifico">Pacifico</option><option value="Lobster">Lobster</option><option value="'Dancing Script'">Dancing Script</option><option value="'Playfair Display'">Playfair Display</option></optgroup>
                    </select>
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Isi Teks</label>
                    <textarea id="textValueControl" rows="2" class="w-full text-sm font-bold border-slate-200 rounded-xl py-3 px-4 focus:ring-red-500 focus:border-red-500 bg-slate-50 shadow-sm" placeholder="Ketik teks di sini..."></textarea>
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Perataan Teks</label>
                    <div class="flex gap-2">
                        <button type="button" id="textAlignLeft" class="flex-1 py-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition text-slate-600 text-center font-bold">Kiri</button>
                        <button type="button" id="textAlignCenter" class="flex-1 py-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition text-slate-600 text-center font-bold">Tengah</button>
                        <button type="button" id="textAlignRight" class="flex-1 py-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition text-slate-600 text-center font-bold">Kanan</button>
                    </div>
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Spasi Baris (<span id="lineHeightVal">1.2</span>)</label>
                    <input type="range" id="lineHeightControl" min="5" max="30" value="12" class="w-full h-1.5 bg-slate-100 rounded-full appearance-none cursor-pointer accent-red-600">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Spasi Huruf (<span id="charSpacingVal">0</span>)</label>
                    <input type="range" id="charSpacingControl" min="-50" max="300" value="0" class="w-full h-1.5 bg-slate-100 rounded-full appearance-none cursor-pointer accent-red-600">
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Lengkungan Teks (<span id="textCurvatureVal">0°</span>)</label>
                        <button type="button" id="resetCurvatureBtn" class="text-[10px] font-bold text-red-500 hover:text-red-700 transition">Reset</button>
                    </div>
                    <input type="range" id="textCurvatureControl" min="-100" max="100" value="0" class="w-full h-1.5 bg-slate-100 rounded-full appearance-none cursor-pointer accent-red-600">
                    <div class="flex justify-between text-[9px] font-bold text-slate-400">
                        <span>⌒ Lengkung Bawah</span>
                        <span>Lurus (0)</span>
                        <span>◡ Lengkung Atas</span>
                    </div>
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Warna Teks</label>
                    <div class="flex items-center gap-4">
                        <input type="color" id="textColorControl" class="w-12 h-12 p-1 border border-slate-200 rounded-xl cursor-pointer bg-white shadow-sm" value="#000000">
                        <div class="flex flex-col text-xs font-bold text-slate-400"><span>HEX</span><span class="text-slate-800" id="textColorVal">#000000</span></div>
                    </div>
                </div>
                <div class="space-y-4 pt-4 md:pt-2 border-t border-slate-100">
                    <div class="flex justify-between items-center">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Outline</label>
                        <input type="color" id="textStrokeColor" class="w-8 h-8 p-0.5 border border-slate-200 rounded-lg cursor-pointer bg-white" value="#ffffff">
                    </div>
                    <input type="range" id="textStrokeWidth" min="0" max="10" value="0" class="w-full h-1.5 bg-slate-100 rounded-full appearance-none cursor-pointer accent-red-600">
                </div>
                <div class="flex items-center justify-between pt-4 md:pt-2 border-t border-slate-100">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Bayangan</label>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="textShadowToggle" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-red-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                    </label>
                </div>
            </div>
            {{-- Image Properties --}}
            <div id="imageControls" class="hidden flex-col gap-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 md:mb-2">Hapus Latar</p>
                <div class="flex items-center gap-4">
                    <div class="flex-1"><label class="text-[9px] font-black text-slate-400 uppercase block mb-1">Warna Target</label><input type="color" id="removeColorTarget" class="w-full h-8 md:h-10 p-0.5 border border-slate-200 rounded-lg cursor-pointer bg-white" value="#ffffff"></div>
                    <div class="flex-1"><label class="text-[9px] font-black text-slate-400 uppercase block mb-1">Toleransi (<span id="tolValue">15</span>%)</label><input type="range" id="removeColorTolerance" min="0" max="50" value="15" class="w-full h-1.5 bg-slate-200 rounded-full appearance-none accent-sky-500 cursor-pointer"></div>
                </div>
                <div class="flex gap-2">
                    <button id="detectBgColorBtn" class="flex-1 bg-slate-100 text-slate-600 text-xs font-bold py-3 md:py-4 rounded-xl hover:bg-slate-200 transition border border-slate-200 shadow-sm">🔍 Auto</button>
                    <button id="removeBgBtn" class="flex-1 bg-sky-50 border border-sky-100 text-sky-600 text-xs font-black py-3 md:py-4 rounded-xl hover:bg-sky-100 transition uppercase tracking-widest">✨ EKSEKUSI</button>
                </div>
                <div class="space-y-3 pt-3 border-t border-slate-100">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Transparansi (<span id="imageOpacityVal">100</span>%)</label>
                    <input type="range" id="imageOpacityControl" min="0" max="100" value="100" class="w-full h-1.5 bg-slate-200 rounded-full appearance-none accent-sky-500 cursor-pointer">
                </div>
                <div class="flex gap-2 pt-2 border-t border-slate-100">
                    <button type="button" id="flipHBtn" class="flex-1 bg-slate-50 text-slate-600 text-xs font-bold py-2 rounded-xl hover:bg-slate-100 transition border border-slate-200 shadow-sm">Flip Horizontal</button>
                    <button type="button" id="flipVBtn" class="flex-1 bg-slate-50 text-slate-600 text-xs font-bold py-2 rounded-xl hover:bg-slate-100 transition border border-slate-200 shadow-sm">Flip Vertikal</button>
                </div>
                <button id="resetBgBtn" class="w-full text-[10px] uppercase font-bold text-slate-400 hover:text-red-500 transition py-2 underline mt-2">Undo Filter Gambar</button>
            </div>
            {{-- SVG Properties --}}
            <div id="svgControls" class="hidden flex-col gap-4">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Warna Vektor</label>
                <div class="flex items-center gap-4">
                    <input type="color" id="svgColorControl" class="w-12 h-12 p-1 border border-slate-200 rounded-xl cursor-pointer bg-white shadow-sm" value="#000000">
                    <div class="flex flex-col text-xs font-bold text-slate-400"><span>HEX</span><span class="text-slate-800" id="svgColorVal">#000000</span></div>
                </div>
            </div>
            {{-- Layer Management --}}
            <div class="pt-5 md:pt-6 border-t border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 md:mb-4 text-center">Urutan Lapisan</p>
                <div class="grid grid-cols-2 gap-3">
                    <button id="bringForwardBtn" class="flex items-center justify-center gap-2 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold py-3 px-2 md:px-4 rounded-xl border border-slate-200 transition text-[11px] md:text-xs">↑ Ke Depan</button>
                    <button id="sendBackwardBtn" class="flex items-center justify-center gap-2 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold py-3 px-2 md:px-4 rounded-xl border border-slate-200 transition text-[11px] md:text-xs">↓ Ke Belakang</button>
                </div>
            </div>
            {{-- Alignment --}}
            <div class="pt-5 md:pt-6 border-t border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 md:mb-4 text-center">Posisi & Perataan</p>
                <div class="grid grid-cols-2 gap-3">
                    <button id="alignCenterHBtn" class="flex items-center justify-center gap-2 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold py-3 px-2 md:px-4 rounded-xl border border-slate-200 transition text-[11px] md:text-xs" title="Tengah Horizontal">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16M12 4v16"></path></svg> Tengah H
                    </button>
                    <button id="alignCenterVBtn" class="flex items-center justify-center gap-2 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold py-3 px-2 md:px-4 rounded-xl border border-slate-200 transition text-[11px] md:text-xs" title="Tengah Vertikal">
                        <svg class="w-4 h-4 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16M12 4v16"></path></svg> Tengah V
                    </button>
                </div>
            </div>
            {{-- Group & Ungroup --}}
            <div id="groupingControls" class="hidden pt-5 md:pt-6 border-t border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 md:mb-4 text-center">Grup Objek</p>
                <div class="grid grid-cols-2 gap-3">
                    <button id="groupBtn" class="hidden flex items-center justify-center gap-2 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold py-3 px-2 md:px-4 rounded-xl border border-slate-200 transition text-[11px] md:text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg> Group
                    </button>
                    <button id="ungroupBtn" class="hidden flex items-center justify-center gap-2 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold py-3 px-2 md:px-4 rounded-xl border border-slate-200 transition text-[11px] md:text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Ungroup
                    </button>
                </div>
            </div>
            {{-- Delete & Duplicate --}}
            <div class="pt-6 md:pt-8 mt-2 border-t-2 border-slate-50 border-dashed grid grid-cols-2 gap-3">
                <button id="duplicateObjBtn" class="flex-1 bg-white hover:bg-slate-50 text-slate-600 font-black py-4 rounded-2xl transition border border-slate-200 flex items-center justify-center gap-2 shadow-sm uppercase tracking-widest text-[11px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                    <span>Duplikat</span>
                </button>
                <button id="deleteObjBtn" class="flex-1 bg-red-50 hover:bg-red-600 hover:text-white text-red-600 font-black py-4 rounded-2xl transition border border-red-100 flex items-center justify-center gap-2 shadow-inner uppercase tracking-widest text-[11px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    <span>Hapus</span>
                </button>
            </div>
        </div>
    </div>
</div>
