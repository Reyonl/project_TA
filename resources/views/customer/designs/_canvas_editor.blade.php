{{-- Canvas Editor Partial - Tab-based multi-side editor --}}
@php
    $hasBack = in_array($produk->jenis_produk, ['kaos', 'hoodie', 'polo', 'seragam']);
    $hasSides = in_array($produk->jenis_produk, ['kaos', 'hoodie']);
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

<div class="flex h-[calc(100vh-57px)] editor-main-container flex-col md:flex-row relative bg-slate-50 overflow-hidden">
    
    {{-- Mobile Backdrop for Sidebar --}}
    <div x-show="sidebarOpen" x-transition.opacity class="mobile-backdrop md:hidden" @click="sidebarOpen = false" x-cloak></div>

    {{-- Left Icon Navbar (Desktop) / Bottom Toolbar (Mobile) --}}
    <div class="w-full md:w-16 bg-white border-t md:border-t-0 md:border-r border-slate-200 flex flex-row md:flex-col items-center py-2 md:py-3 px-2 md:px-0 gap-1 md:gap-2 z-[60] md:z-30 flex-shrink-0 order-last md:order-first justify-around md:justify-start fixed md:relative bottom-0 left-0 right-0 h-[60px] md:h-auto pb-[env(safe-area-inset-bottom)] md:pb-0">
        {{-- Icon 1: UPLOAD (Default Primary Action) --}}
        <button @click="activeTab = 'upload'; sidebarOpen = true" :class="activeTab === 'upload' && sidebarOpen ? 'text-slate-900 bg-slate-100 font-semibold' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'" class="w-12 h-12 flex flex-col items-center justify-center rounded-lg transition" title="Upload Foto / Logo">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            <span class="text-[9px] font-medium tracking-tight hidden md:block">Upload</span>
        </button>

        {{-- Icon 2: TEKS --}}
        <button @click="activeTab = 'text'; sidebarOpen = true" :class="activeTab === 'text' && sidebarOpen ? 'text-slate-900 bg-slate-100 font-semibold' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'" class="w-12 h-12 flex flex-col items-center justify-center rounded-lg transition" title="Tambah Teks Tulisan">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            <span class="text-[9px] font-medium tracking-tight hidden md:block">Teks</span>
        </button>

        {{-- Icon 3: STIKER --}}
        <button @click="activeTab = 'stickers'; sidebarOpen = true" :class="activeTab === 'stickers' && sidebarOpen ? 'text-slate-900 bg-slate-100 font-semibold' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'" class="w-12 h-12 flex flex-col items-center justify-center rounded-lg transition" title="Cari Ikon & Stiker">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-[9px] font-medium tracking-tight hidden md:block">Stiker</span>
        </button>

        {{-- Icon 4: TEMPLATES --}}
        <button @click="activeTab = 'templates'; sidebarOpen = true" :class="activeTab === 'templates' && sidebarOpen ? 'text-slate-900 bg-slate-100 font-semibold' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'" class="w-12 h-12 flex flex-col items-center justify-center rounded-lg transition" title="Template Desain">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
            <span class="text-[9px] font-medium tracking-tight hidden md:block">Template</span>
        </button>

        {{-- Icon 5: LAYERS --}}
        <button @click="activeTab = 'layers'; sidebarOpen = true" :class="activeTab === 'layers' && sidebarOpen ? 'text-slate-900 bg-slate-100 font-semibold' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'" class="w-12 h-12 flex flex-col items-center justify-center rounded-lg transition" title="Daftar Objek / Lapisan">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 2l-8 4 8 4 8-4-8-4z M2 10l10 5 10-5 M2 15l10 5 10-5"></path></svg>
            <span class="text-[9px] font-medium tracking-tight hidden md:block">Layers</span>
        </button>

        <button id="mobileEditBtn" type="button" onclick="if(typeof toggleMobileProperties === 'function') toggleMobileProperties();" class="w-12 h-12 md:hidden flex flex-col items-center justify-center rounded-lg transition text-slate-500 hover:text-slate-900 hover:bg-slate-50 hidden">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
        </button>

        <div class="mt-auto pb-3 hidden md:block">
            <button @click="sidebarOpen = !sidebarOpen" class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-slate-100 transition text-slate-400 hover:text-slate-600" title="Buka/Tutup Panel">
                <svg class="w-4 h-4 transition-transform duration-200" :class="!sidebarOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
            </button>
        </div>
    </div>

    {{-- Sidebar Tools Panel --}}
    <div x-show="sidebarOpen" 
         x-transition:enter="transition ease-out duration-200 transform"
         x-transition:enter-start="translate-y-full md:translate-y-0 md:-translate-x-full"
         x-transition:enter-end="translate-y-0 md:translate-x-0"
         x-transition:leave="transition ease-in duration-150 transform"
         x-transition:leave-start="translate-y-0 md:translate-x-0"
         x-transition:leave-end="translate-y-full md:translate-y-0 md:-translate-x-full"
         class="w-full md:w-80 bg-white border-r border-slate-200 shadow-sm flex flex-col flex-shrink-0 z-50 md:z-20 editor-sidebar">
        
        {{-- Mobile Drag Handle --}}
        <div class="md:hidden w-full pt-2.5 pb-1 bg-slate-50/80 rounded-t-xl cursor-pointer" @click="sidebarOpen = false">
            <div class="w-10 h-1 bg-slate-300 rounded-full mx-auto"></div>
        </div>

        <div class="p-3.5 sm:p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="font-semibold text-slate-800 text-xs tracking-wider uppercase" x-text="activeTab === 'upload' ? 'Upload Foto / Logo' : (activeTab === 'text' ? 'Tambahkan Teks' : (activeTab === 'stickers' ? 'Cari Stiker & Ikon' : (activeTab === 'templates' ? 'Template Desain' : 'Lapisan Desain')))"></h3>
            <button @click="sidebarOpen = false" class="text-slate-400 hover:text-slate-600 hidden md:block">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 pb-24 md:pb-5 custom-scrollbar">
            {{-- Tab: UPLOAD (Default) --}}
            <div x-show="activeTab === 'upload'" class="space-y-3">
                <p class="text-[11px] font-medium text-slate-400">Unggah file dari perangkat Anda</p>
                <label class="block w-full border-2 border-dashed border-slate-200 hover:border-slate-400 rounded-xl p-6 text-center hover:bg-slate-50/60 cursor-pointer transition group shadow-2xs">
                    <input type="file" id="imageLoader" accept="image/png, image/jpeg, image/svg+xml" class="hidden"/>
                    <div class="w-10 h-10 bg-slate-100 text-slate-600 rounded-xl mx-auto flex items-center justify-center mb-2.5 group-hover:scale-105 group-hover:bg-slate-200 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    </div>
                    <span class="text-slate-800 font-semibold block text-xs group-hover:text-slate-900">+ Pilih Foto atau Logo</span>
                    <span class="text-slate-400 text-[10px] mt-1 block">PNG, JPG, SVG (Maks. 2MB)</span>
                </label>

                <div class="p-2.5 bg-slate-50 border border-slate-200/70 rounded-lg text-[10px] text-slate-500 leading-relaxed">
                    <span class="font-semibold text-slate-700">Tip:</span> Gunakan format <strong>PNG transparan</strong> atau foto resolusi tajam untuk hasil sablon terbaik.
                </div>
            </div>

            {{-- Tab: TEKS --}}
            <div x-show="activeTab === 'text'" style="display: none;" class="space-y-3">
                <p class="text-[11px] font-medium text-slate-400">Tambahkan teks ke area cetak</p>
                <button id="addTextBtn" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold py-2.5 px-4 rounded-lg transition text-xs shadow-xs flex items-center justify-center gap-1.5">
                    <span class="text-sm leading-none">+</span>
                    <span>Tambah Teks Baru</span>
                </button>
                <div class="bg-slate-50 border border-slate-200/70 rounded-lg p-3">
                    <p class="text-[11px] text-slate-500 text-center font-normal leading-relaxed">Klik teks yang ada pada kaos untuk mengatur jenis font, warna, dan perataan.</p>
                </div>
            </div>

            {{-- Tab: STIKER --}}
            <div x-show="activeTab === 'stickers'" style="display: none;" class="flex flex-col h-full bg-white">
                <div class="flex mb-3 group">
                    <input type="text" id="stickerSearchInput" placeholder="Cari ikon atau stiker..." class="flex-1 border border-slate-200 rounded-l-lg px-3 py-1.5 text-xs focus:ring-1 focus:ring-slate-900 focus:border-slate-900 bg-slate-50">
                    <button id="searchStickerBtn" class="bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 rounded-r-lg text-xs font-semibold transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </div>
                <div id="stickersContainer" class="grid grid-cols-3 md:grid-cols-2 gap-2.5 overflow-y-auto pb-4 custom-scrollbar">
                    <div class="col-span-3 md:col-span-2 text-center text-xs text-slate-400 py-4 italic">Memuat library...</div>
                </div>
            </div>

            {{-- Tab: TEMPLATE --}}
            <div x-show="activeTab === 'templates'" style="display: none;" class="space-y-3">
                <p class="text-[11px] font-medium text-slate-400">Katalog aset grafis siap pakai</p>
                <div class="grid grid-cols-2 gap-2.5">
                    @forelse($templates as $template)
                    <div class="bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-slate-400 hover:shadow-xs transition aspect-square flex items-center justify-center bg-slate-50 p-2 template-item group" data-url="{{ Storage::url($template->file_template) }}">
                        <img src="{{ Storage::url($template->file_template) }}" alt="Template" class="w-full h-full object-contain pointer-events-none group-hover:scale-105 transition-transform">
                    </div>
                    @empty
                    <div class="col-span-2 text-center text-xs text-slate-400 py-4">Belum ada template.</div>
                    @endforelse
                </div>
            </div>

            {{-- Tab: LAYERS --}}
            <div x-show="activeTab === 'layers'" style="display: none;" class="flex flex-col h-full bg-white">
                <p class="text-[11px] font-medium text-slate-400 mb-2">Daftar objek pada sisi aktif</p>
                <div id="layersListContainer" class="flex-1 overflow-y-auto pb-4 custom-scrollbar">
                    <div class="text-center text-xs text-slate-400 py-4 italic">Belum ada objek.</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Canvas Area --}}
    <div class="flex-1 flex flex-col relative bg-slate-100 overflow-hidden order-first md:order-none z-10 pb-16 md:pb-0">
        
        {{-- Canvas Header: Professional Segmented Side Switcher & Preview Action --}}
        <div class="h-12 bg-white border-b border-slate-200 flex items-center justify-between px-3 sm:px-6 z-10 w-full flex-shrink-0">
            {{-- Minimal Segmented Side Navigation --}}
            <div class="inline-flex items-center p-0.5 bg-slate-100 border border-slate-200/80 rounded-lg">
                <button type="button" @click="selectSide('front')"
                        class="px-3 py-1 rounded-md text-xs transition-all duration-150 flex items-center gap-1.5"
                        :class="activeSide === 'front' 
                            ? 'bg-white text-slate-900 font-semibold shadow-xs' 
                            : 'text-slate-500 hover:text-slate-800 hover:bg-white/40 font-medium'">
                    <span>Depan</span>
                    <span x-show="hasFrontDesign" class="w-1.5 h-1.5 rounded-full bg-emerald-500" title="Ada Desain" x-cloak></span>
                </button>

                @if($hasBack)
                <button type="button" @click="selectSide('back')"
                        class="px-3 py-1 rounded-md text-xs transition-all duration-150 flex items-center gap-1.5"
                        :class="activeSide === 'back' 
                            ? 'bg-white text-slate-900 font-semibold shadow-xs' 
                            : 'text-slate-500 hover:text-slate-800 hover:bg-white/40 font-medium'">
                    <span>Belakang</span>
                    <span x-show="hasBackDesign" class="w-1.5 h-1.5 rounded-full bg-emerald-500" title="Ada Desain" x-cloak></span>
                </button>
                @endif

                @if($hasSides)
                <button type="button" @click="selectSide('left')"
                        class="px-2.5 sm:px-3 py-1 rounded-md text-xs transition-all duration-150 flex items-center gap-1.5 hidden sm:flex"
                        :class="activeSide === 'left' 
                            ? 'bg-white text-slate-900 font-semibold shadow-xs' 
                            : 'text-slate-500 hover:text-slate-800 hover:bg-white/40 font-medium'">
                    <span>Kiri</span>
                    <span x-show="hasLeftDesign" class="w-1.5 h-1.5 rounded-full bg-emerald-500" title="Ada Desain" x-cloak></span>
                </button>

                <button type="button" @click="selectSide('right')"
                        class="px-2.5 sm:px-3 py-1 rounded-md text-xs transition-all duration-150 flex items-center gap-1.5 hidden sm:flex"
                        :class="activeSide === 'right' 
                            ? 'bg-white text-slate-900 font-semibold shadow-xs' 
                            : 'text-slate-500 hover:text-slate-800 hover:bg-white/40 font-medium'">
                    <span>Kanan</span>
                    <span x-show="hasRightDesign" class="w-1.5 h-1.5 rounded-full bg-emerald-500" title="Ada Desain" x-cloak></span>
                </button>
                @endif
            </div>

            {{-- Clean Professional Preview CTA --}}
            <button type="button" @click="openPreview()"
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg transition-colors shadow-xs">
                <span>Lihat Preview</span>
                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>

        {{-- Canvas Workspace --}}
        <div class="flex-1 overflow-hidden flex justify-center items-center relative bg-slate-50 mobile-canvas-scaler" id="canvasScalerWrapper">
            <div class="relative shadow-xl rounded-xl overflow-hidden pointer-events-auto flex items-center justify-center bg-slate-100 origin-center border border-slate-200/60" id="mockupContainer" style="width: 480px; height: 600px;">
                {{-- Minimal Subtle Side Indicator Badge --}}
                <div class="absolute top-3 left-3 z-30 pointer-events-none">
                    <span class="bg-white/85 text-slate-500 text-[10px] font-medium px-2 py-0.5 rounded border border-slate-200/80 backdrop-blur-xs tracking-wider" x-text="activeSide === 'front' ? 'Depan' : (activeSide === 'back' ? 'Belakang' : (activeSide === 'left' ? 'Kiri' : 'Kanan'))">Depan</span>
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
                     <span class="absolute -top-6 left-1/2 transform -translate-x-1/2 text-[9px] text-slate-500 font-medium tracking-wider bg-white/90 px-2 py-0.5 rounded border border-slate-200 shadow-2xs whitespace-nowrap">Area Cetak</span>
                    <div class="absolute -top-1 -left-1 w-2.5 h-2.5 border-t-2 border-l-2 border-slate-400"></div>
                    <div class="absolute -top-1 -right-1 w-2.5 h-2.5 border-t-2 border-r-2 border-slate-400"></div>
                    <div class="absolute -bottom-1 -left-1 w-2.5 h-2.5 border-b-2 border-l-2 border-slate-400"></div>
                    <div class="absolute -bottom-1 -right-1 w-2.5 h-2.5 border-b-2 border-r-2 border-slate-400"></div>
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
            <div class="absolute bottom-4 right-4 flex flex-col gap-1.5 z-30">
                <button type="button" id="zoomInBtn" class="w-8 h-8 bg-white rounded-lg shadow-xs text-slate-600 hover:text-slate-900 hover:bg-slate-50 flex items-center justify-center transition border border-slate-200" title="Zoom In">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </button>
                <button type="button" id="zoomResetBtn" class="w-8 h-8 bg-white rounded-lg shadow-xs text-slate-600 hover:text-slate-900 hover:bg-slate-50 flex items-center justify-center transition border border-slate-200 text-[9px] font-semibold" title="Reset Zoom">
                    100%
                </button>
                <button type="button" id="zoomOutBtn" class="w-8 h-8 bg-white rounded-lg shadow-xs text-slate-600 hover:text-slate-900 hover:bg-slate-50 flex items-center justify-center transition border border-slate-200" title="Zoom Out">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </button>
            </div>

        </div>
    </div>

    {{-- Right Sidebar: Properties Panel --}}
    {{-- Mobile Backdrop --}}
    <div id="editorControlsBackdrop" class="mobile-backdrop md:hidden hidden z-40" onclick="window.activeCanvas.discardActiveObject(); window.activeCanvas.requestRenderAll();"></div>
    
    <div id="editorControls" class="w-full md:w-80 bg-white border-l border-slate-200 shadow-sm flex flex-col flex-shrink-0 z-50 md:z-30 hidden overflow-y-auto custom-scrollbar editor-properties">
        {{-- Mobile Drag Handle --}}
        <div class="md:hidden w-full pt-2.5 pb-1 bg-slate-50/80 rounded-t-xl cursor-pointer" onclick="window.activeCanvas.discardActiveObject(); window.activeCanvas.requestRenderAll();">
            <div class="w-10 h-1 bg-slate-300 rounded-full mx-auto"></div>
        </div>

        <div class="p-3.5 sm:p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="font-semibold text-slate-800 text-xs uppercase tracking-wider">Pengaturan Objek</h3>
            <button onclick="window.activeCanvas.discardActiveObject(); window.activeCanvas.requestRenderAll();" class="text-slate-400 hover:text-slate-600 transition hidden md:block">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="p-4 sm:p-5 space-y-5 pb-[max(1.25rem,env(safe-area-inset-bottom))]">
            {{-- Sablon Size Picker for Selected Object --}}
            <div id="objectSablonSizeControl" class="space-y-2.5 pb-4 border-b border-slate-100" style="display: none !important;">
                <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Ukuran Sablon</label>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" id="btnSizeA5" onclick="setObjectSablonSize('a5')" class="size-btn flex flex-col items-center justify-center p-2 rounded-lg border border-slate-200 bg-slate-50 hover:border-slate-400 transition cursor-pointer">
                        <span class="text-xs font-semibold text-slate-800">A5 Logo</span>
                        <span class="text-[9px] text-slate-400 mt-0.5">10x10 cm</span>
                        <span class="text-[9px] font-semibold text-slate-600 mt-1">Rp 10k</span>
                    </button>
                    <button type="button" id="btnSizeA4" onclick="setObjectSablonSize('a4')" class="size-btn flex flex-col items-center justify-center p-2 rounded-lg border border-slate-200 bg-slate-50 hover:border-slate-400 transition cursor-pointer">
                        <span class="text-xs font-semibold text-slate-800">A4</span>
                        <span class="text-[9px] text-slate-400 mt-0.5">20x25 cm</span>
                        <span class="text-[9px] font-semibold text-slate-600 mt-1">Rp 25k</span>
                    </button>
                    <button type="button" id="btnSizeA3" onclick="setObjectSablonSize('a3')" class="size-btn flex flex-col items-center justify-center p-2 rounded-lg border border-slate-200 bg-slate-50 hover:border-slate-400 transition cursor-pointer">
                        <span class="text-xs font-semibold text-slate-800">A3</span>
                        <span class="text-[9px] text-slate-400 mt-0.5">25x35 cm</span>
                        <span class="text-[9px] font-semibold text-slate-600 mt-1">Rp 35k</span>
                    </button>
                </div>
            </div>

            {{-- Text Properties --}}
            <div id="textControls" class="hidden flex-col gap-4">
                {{-- 1-Click Preset Styles --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Preset Gaya</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" class="text-preset-btn group p-2.5 bg-white border border-slate-200 hover:border-slate-400 rounded-lg flex flex-col items-center justify-center gap-0.5 transition" data-preset="varsity">
                            <span class="font-bold text-xs uppercase text-slate-800" style="font-family: 'Bebas Neue', sans-serif;">ATHLETIC</span>
                            <span class="text-[9px] text-slate-400">Varsity</span>
                        </button>
                        <button type="button" class="text-preset-btn group p-2.5 bg-slate-900 border border-slate-800 hover:border-cyan-400 rounded-lg flex flex-col items-center justify-center gap-0.5 transition" data-preset="neon">
                            <span class="font-bold text-xs uppercase text-cyan-400">GLOWING</span>
                            <span class="text-[9px] text-slate-400">Neon</span>
                        </button>
                        <button type="button" class="text-preset-btn group p-2.5 bg-white border border-slate-200 hover:border-slate-400 rounded-lg flex flex-col items-center justify-center gap-0.5 transition" data-preset="retro">
                            <span class="font-bold text-xs uppercase text-amber-600" style="font-family: Impact, sans-serif;">VINTAGE</span>
                            <span class="text-[9px] text-slate-400">Retro</span>
                        </button>
                        <button type="button" class="text-preset-btn group p-2.5 bg-white border border-slate-200 hover:border-slate-400 rounded-lg flex flex-col items-center justify-center gap-0.5 transition" data-preset="badge">
                            <span class="bg-slate-900 text-white font-bold text-[9px] px-1.5 py-0.5 rounded">LABEL</span>
                            <span class="text-[9px] text-slate-400">Box</span>
                        </button>
                    </div>
                </div>

                {{-- Font Family --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Font</label>
                    <select id="fontFamilyControl" class="w-full text-xs font-semibold border-slate-200 rounded-lg py-2 pl-3 focus:ring-1 focus:ring-slate-900 focus:border-slate-900 bg-slate-50 cursor-pointer shadow-2xs">
                        <optgroup label="Standard"><option value="Arial">Arial</option><option value="Roboto">Roboto</option><option value="Montserrat">Montserrat</option></optgroup>
                        <optgroup label="Display & Bold"><option value="'Bebas Neue'">Bebas Neue</option><option value="Impact">Impact</option><option value="Oswald">Oswald</option><option value="Anton">Anton</option></optgroup>
                        <optgroup label="Script & Elegant"><option value="Pacifico">Pacifico</option><option value="Lobster">Lobster</option><option value="'Dancing Script'">Dancing Script</option><option value="'Playfair Display'">Playfair Display</option></optgroup>
                    </select>
                </div>

                {{-- Quick Typography Styles (B, I, U, S, TT) --}}
                <div class="space-y-1.5">
                    <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Format</label>
                    <div class="flex items-center gap-1 bg-slate-100 p-0.5 rounded-lg border border-slate-200/50">
                        <button type="button" id="toggleBoldBtn" class="flex-1 py-1 rounded font-bold text-xs text-slate-600 hover:bg-white hover:text-slate-900 transition" title="Tebal (Bold)">B</button>
                        <button type="button" id="toggleItalicBtn" class="flex-1 py-1 rounded font-serif italic font-semibold text-xs text-slate-600 hover:bg-white hover:text-slate-900 transition" title="Miring (Italic)">I</button>
                        <button type="button" id="toggleUnderlineBtn" class="flex-1 py-1 rounded underline font-medium text-xs text-slate-600 hover:bg-white hover:text-slate-900 transition" title="Garis Bawah (Underline)">U</button>
                        <button type="button" id="toggleLinethroughBtn" class="flex-1 py-1 rounded line-through font-medium text-xs text-slate-600 hover:bg-white hover:text-slate-900 transition" title="Coret (Strikethrough)">S</button>
                        <button type="button" id="toggleAllCapsBtn" class="flex-1 py-1 rounded font-black text-[10px] text-slate-600 hover:bg-white hover:text-slate-900 transition" title="Kapital Semua (ALL CAPS)">TT</button>
                    </div>
                </div>

                {{-- Text Content --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Isi Teks</label>
                    <textarea id="textValueControl" rows="2" class="w-full text-xs font-semibold border-slate-200 rounded-lg py-2 px-3 focus:ring-1 focus:ring-slate-900 focus:border-slate-900 bg-slate-50 shadow-2xs" placeholder="Ketik teks..."></textarea>
                </div>

                {{-- Alignment --}}
                <div class="space-y-1.5">
                    <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Perataan</label>
                    <div class="flex items-center gap-1 bg-slate-100 p-0.5 rounded-lg border border-slate-200/50">
                        <button type="button" id="textAlignLeft" class="flex-1 py-1 bg-white text-slate-800 rounded text-xs font-medium shadow-2xs">Kiri</button>
                        <button type="button" id="textAlignCenter" class="flex-1 py-1 text-slate-600 rounded text-xs font-medium hover:bg-white">Tengah</button>
                        <button type="button" id="textAlignRight" class="flex-1 py-1 text-slate-600 rounded text-xs font-medium hover:bg-white">Kanan</button>
                    </div>
                </div>

                {{-- Spacing --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Spasi Baris (<span id="lineHeightVal">1.2</span>)</label>
                    <input type="range" id="lineHeightControl" min="5" max="30" value="12" class="w-full h-1 bg-slate-200 rounded-full appearance-none cursor-pointer accent-slate-800">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Spasi Huruf (<span id="charSpacingVal">0</span>)</label>
                    <input type="range" id="charSpacingControl" min="-50" max="300" value="0" class="w-full h-1 bg-slate-200 rounded-full appearance-none cursor-pointer accent-slate-800">
                </div>

                {{-- Curvature --}}
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Lengkungan Teks (<span id="textCurvatureVal" class="text-slate-800 font-semibold">0°</span>)</label>
                        <button type="button" id="resetCurvatureBtn" class="text-[10px] font-medium text-slate-400 hover:text-slate-800 transition">Reset</button>
                    </div>
                    <input type="range" id="textCurvatureControl" min="-100" max="100" value="0" class="w-full h-1 bg-slate-200 rounded-full appearance-none cursor-pointer accent-slate-800">
                    <div class="flex justify-between text-[9px] font-medium text-slate-400">
                        <span>Cekung</span>
                        <span>Lurus</span>
                        <span>Cembung</span>
                    </div>
                </div>

                {{-- Text Color & Gradient Mode --}}
                <div class="space-y-2.5 pt-2 border-t border-slate-100">
                    <div class="flex justify-between items-center">
                        <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Warna Teks</label>
                        <div class="flex items-center gap-1 bg-slate-100 p-0.5 rounded-md text-[10px] font-medium">
                            <button type="button" id="textColorModeSolid" class="px-2 py-0.5 rounded bg-white text-slate-800 shadow-2xs">Solid</button>
                            <button type="button" id="textColorModeGradient" class="px-2 py-0.5 rounded text-slate-500 hover:text-slate-800">Gradasi</button>
                        </div>
                    </div>
                    {{-- Solid Color Picker --}}
                    <div id="solidColorGroup" class="flex items-center gap-3">
                        <input type="color" id="textColorControl" class="w-9 h-9 p-0.5 border border-slate-200 rounded-lg cursor-pointer bg-white shadow-2xs" value="#000000">
                        <div class="flex flex-col text-xs font-medium text-slate-400"><span>HEX</span><span class="text-slate-800 font-semibold" id="textColorVal">#000000</span></div>
                    </div>
                    {{-- Gradient Color Pickers --}}
                    <div id="gradientColorGroup" class="hidden space-y-2.5 bg-slate-50 p-2.5 rounded-lg border border-slate-200/80">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <input type="color" id="textGradColor1" class="w-7 h-7 p-0.5 border border-slate-200 rounded cursor-pointer bg-white" value="#f97316">
                                <span class="text-[10px] font-medium text-slate-600">Warna 1</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="color" id="textGradColor2" class="w-7 h-7 p-0.5 border border-slate-200 rounded cursor-pointer bg-white" value="#ef4444">
                                <span class="text-[10px] font-medium text-slate-600">Warna 2</span>
                            </div>
                        </div>
                        <div class="flex gap-1.5">
                            <button type="button" id="gradDirH" class="flex-1 py-1 text-[9px] font-medium bg-white border border-slate-200 rounded text-slate-700 hover:bg-slate-50">Horizontal</button>
                            <button type="button" id="gradDirV" class="flex-1 py-1 text-[9px] font-medium bg-white border border-slate-200 rounded text-slate-700 hover:bg-slate-50">Vertikal</button>
                            <button type="button" id="gradDirD" class="flex-1 py-1 text-[9px] font-medium bg-white border border-slate-200 rounded text-slate-700 hover:bg-slate-50">Diagonal</button>
                        </div>
                    </div>
                </div>

                {{-- Text Background Box (Badge) --}}
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <div class="flex justify-between items-center">
                        <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Kotak Latar</label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="textBgToggle" class="sr-only peer">
                            <div class="w-8 h-4.5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-slate-400 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-slate-900"></div>
                        </label>
                    </div>
                    <div id="textBgColorGroup" class="hidden items-center gap-3 bg-slate-50 p-2 rounded-lg border border-slate-200">
                        <input type="color" id="textBgColorControl" class="w-7 h-7 p-0.5 border border-slate-200 rounded cursor-pointer bg-white" value="#dc2626">
                        <span class="text-xs font-medium text-slate-700">Warna Latar</span>
                    </div>
                </div>

                {{-- Outline / Stroke --}}
                <div class="space-y-2.5 pt-2 border-t border-slate-100">
                    <div class="flex justify-between items-center">
                        <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Garis Tepi (Outline)</label>
                        <input type="color" id="textStrokeColor" class="w-7 h-7 p-0.5 border border-slate-200 rounded cursor-pointer bg-white" value="#ffffff">
                    </div>
                    <input type="range" id="textStrokeWidth" min="0" max="10" value="0" class="w-full h-1 bg-slate-200 rounded-full appearance-none cursor-pointer accent-slate-800">
                </div>

                {{-- Shadow --}}
                <div class="space-y-2.5 pt-2 border-t border-slate-100">
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Bayangan (Shadow)</label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="textShadowToggle" class="sr-only peer">
                            <div class="w-8 h-4.5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-slate-400 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-slate-900"></div>
                        </label>
                    </div>
                    <div id="textShadowColorGroup" class="hidden items-center justify-between bg-slate-50 p-2 rounded-lg border border-slate-200">
                        <span class="text-xs font-medium text-slate-700">Warna Bayangan</span>
                        <input type="color" id="textShadowColor" class="w-7 h-7 p-0.5 border border-slate-200 rounded cursor-pointer bg-white" value="#000000">
                    </div>
                </div>
            </div>

            {{-- Image Properties --}}
            <div id="imageControls" class="hidden flex-col gap-3">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Hapus Latar Belakang</p>
                <div class="flex items-center gap-3">
                    <div class="flex-1"><label class="text-[9px] font-medium text-slate-400 uppercase block mb-1">Warna Target</label><input type="color" id="removeColorTarget" class="w-full h-8 p-0.5 border border-slate-200 rounded cursor-pointer bg-white" value="#ffffff"></div>
                    <div class="flex-1"><label class="text-[9px] font-medium text-slate-400 uppercase block mb-1">Toleransi (<span id="tolValue">15</span>%)</label><input type="range" id="removeColorTolerance" min="0" max="50" value="15" class="w-full h-1 bg-slate-200 rounded-full appearance-none accent-slate-800 cursor-pointer"></div>
                </div>
                <div class="flex gap-2">
                    <button id="detectBgColorBtn" class="flex-1 bg-slate-100 text-slate-700 text-xs font-semibold py-2 rounded-lg hover:bg-slate-200 transition border border-slate-200">Auto Deteksi</button>
                    <button id="removeBgBtn" class="flex-1 bg-slate-900 text-white text-xs font-semibold py-2 rounded-lg hover:bg-slate-800 transition">Hapus Background</button>
                </div>
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Transparansi (<span id="imageOpacityVal">100</span>%)</label>
                    <input type="range" id="imageOpacityControl" min="0" max="100" value="100" class="w-full h-1 bg-slate-200 rounded-full appearance-none accent-slate-800 cursor-pointer">
                </div>
                <div class="flex gap-2 pt-2 border-t border-slate-100">
                    <button type="button" id="flipHBtn" class="flex-1 bg-slate-50 text-slate-700 text-xs font-medium py-1.5 rounded-lg hover:bg-slate-100 transition border border-slate-200">Flip Horizontal</button>
                    <button type="button" id="flipVBtn" class="flex-1 bg-slate-50 text-slate-700 text-xs font-medium py-1.5 rounded-lg hover:bg-slate-100 transition border border-slate-200">Flip Vertikal</button>
                </div>
                <button id="resetBgBtn" class="w-full text-[10px] font-medium text-slate-400 hover:text-slate-700 transition py-1 underline mt-1">Undo Filter Gambar</button>
            </div>

            {{-- SVG Properties --}}
            <div id="svgControls" class="hidden flex-col gap-3">
                <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Warna Vektor</label>
                <div class="flex items-center gap-3">
                    <input type="color" id="svgColorControl" class="w-9 h-9 p-0.5 border border-slate-200 rounded-lg cursor-pointer bg-white shadow-2xs" value="#000000">
                    <div class="flex flex-col text-xs font-medium text-slate-400"><span>HEX</span><span class="text-slate-800 font-semibold" id="svgColorVal">#000000</span></div>
                </div>
            </div>

            {{-- Layer Management --}}
            <div class="pt-4 border-t border-slate-100">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2.5 text-center">Urutan Lapisan</p>
                <div class="grid grid-cols-2 gap-2">
                    <button id="bringForwardBtn" class="flex items-center justify-center gap-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium py-2 px-3 rounded-lg border border-slate-200 transition text-xs">↑ Ke Depan</button>
                    <button id="sendBackwardBtn" class="flex items-center justify-center gap-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium py-2 px-3 rounded-lg border border-slate-200 transition text-xs">↓ Ke Belakang</button>
                </div>
            </div>

            {{-- Alignment --}}
            <div class="pt-4 border-t border-slate-100">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2.5 text-center">Perataan Posisi</p>
                <div class="grid grid-cols-2 gap-2">
                    <button id="alignCenterHBtn" class="flex items-center justify-center gap-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium py-2 px-3 rounded-lg border border-slate-200 transition text-xs" title="Tengah Horizontal">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16M12 4v16"></path></svg> Tengah H
                    </button>
                    <button id="alignCenterVBtn" class="flex items-center justify-center gap-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium py-2 px-3 rounded-lg border border-slate-200 transition text-xs" title="Tengah Vertikal">
                        <svg class="w-3.5 h-3.5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16M12 4v16"></path></svg> Tengah V
                    </button>
                </div>
            </div>

            {{-- Group & Ungroup --}}
            <div id="groupingControls" class="hidden pt-4 border-t border-slate-100">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2.5 text-center">Grup Objek</p>
                <div class="grid grid-cols-2 gap-2">
                    <button id="groupBtn" class="hidden flex items-center justify-center gap-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium py-2 px-3 rounded-lg border border-slate-200 transition text-xs">
                        Group
                    </button>
                    <button id="ungroupBtn" class="hidden flex items-center justify-center gap-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium py-2 px-3 rounded-lg border border-slate-200 transition text-xs">
                        Ungroup
                    </button>
                </div>
            </div>

            {{-- Delete & Duplicate --}}
            <div class="pt-4 border-t border-slate-100 grid grid-cols-2 gap-2">
                <button id="duplicateObjBtn" class="bg-white hover:bg-slate-50 text-slate-700 font-medium py-2.5 rounded-lg transition border border-slate-200 flex items-center justify-center gap-1.5 shadow-2xs text-xs">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                    <span>Duplikat</span>
                </button>
                <button id="deleteObjBtn" class="bg-rose-50 hover:bg-rose-100 text-rose-700 font-medium py-2.5 rounded-lg transition border border-rose-200/60 flex items-center justify-center gap-1.5 text-xs">
                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    <span>Hapus</span>
                </button>
            </div>
        </div>
    </div>
</div>
