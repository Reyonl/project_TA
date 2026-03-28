<x-app-layout>
    <!-- Tambahkan library Fabric.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Editor Desain Sablon') }} - {{ $produk->nama_produk }}
        </h2>
    </x-slot>

    <div class="h-[calc(100vh-65px)] overflow-hidden" x-data="{ activeTab: 'templates', baseColor: '#ffffff', activeSide: 'front' }" x-init="if(typeof switchCanvasSide === 'function') $watch('activeSide', value => switchCanvasSide(value))">
        <div class="flex h-full bg-slate-50">
            
            <!-- Navbar Kiri Tepi (Icon Only) -->
            <div class="w-16 bg-white border-r border-slate-200 flex flex-col items-center py-4 gap-4 z-20 shadow-sm flex-shrink-0">
                <button @click="activeTab = 'templates'" :class="activeTab === 'templates' ? 'text-indigo-600 bg-indigo-50' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50'" class="w-12 h-12 flex flex-col items-center justify-center rounded-xl transition">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
                    <span class="text-[9px] font-bold">Template</span>
                </button>
                <button @click="activeTab = 'upload'" :class="activeTab === 'upload' ? 'text-indigo-600 bg-indigo-50' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50'" class="w-12 h-12 flex flex-col items-center justify-center rounded-xl transition">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    <span class="text-[9px] font-bold">Upload</span>
                </button>
                <button @click="activeTab = 'stickers'" :class="activeTab === 'stickers' ? 'text-indigo-600 bg-indigo-50' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50'" class="w-12 h-12 flex flex-col items-center justify-center rounded-xl transition">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-[9px] font-bold">Stiker</span>
                </button>
                <button @click="activeTab = 'text'" :class="activeTab === 'text' ? 'text-indigo-600 bg-indigo-50' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50'" class="w-12 h-12 flex flex-col items-center justify-center rounded-xl transition">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    <span class="text-[9px] font-bold">Teks</span>
                </button>
            </div>

            <!-- Panel Tools Kiri (Membuka sesuai Tab yang Aktif) -->
            <div class="w-72 bg-white border-r border-slate-200 shadow-sm flex flex-col flex-shrink-0 relative z-10">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 text-lg" x-text="
                        activeTab === 'templates' ? 'Template Desain' :
                        (activeTab === 'upload' ? 'Upload Gambar' :
                        (activeTab === 'stickers' ? 'Cari Stiker' : 'Tambahkan Teks'))
                    "></h3>
                </div>
                
                <div class="flex-1 overflow-y-auto p-5 custom-scrollbar">

                    <!-- Tab: TEMPLATE -->
                    <div x-show="activeTab === 'templates'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" class="space-y-4">
                        <p class="text-xs text-slate-500 mb-2">Pilih template desain yang sudah disediakan admin.</p>
                        <div class="grid grid-cols-2 gap-3">
                            @forelse($templates as $template)
                                <div class="bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-indigo-500 hover:shadow-md transition aspect-square flex items-center justify-center bg-slate-50 p-2 template-item" data-url="{{ Storage::url($template->file_template) }}">
                                    <img src="{{ Storage::url($template->file_template) }}" alt="Template" class="w-full h-full object-contain pointer-events-none">
                                </div>
                            @empty
                                <div class="col-span-2 text-center text-sm text-slate-400 py-4">Belum ada template terdaftar.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Tab: UPLOAD -->
                    <div x-show="activeTab === 'upload'" style="display: none;" class="space-y-4">
                        <p class="text-xs text-slate-500 mb-2">Unggah logo atau desain Anda sendiri (gunakan format transparan PNG/SVG untuk hasil sempurna).</p>
                        <label class="block w-full border-2 border-dashed border-indigo-200 rounded-xl p-6 text-center hover:bg-indigo-50 hover:border-indigo-400 cursor-pointer transition">
                            <input type="file" id="imageLoader" accept="image/png, image/jpeg, image/svg+xml" class="hidden"/>
                            <div class="text-indigo-500 mb-2 font-bold text-2xl">☁️</div>
                            <span class="text-indigo-600 font-bold block">Klik untuk Upload</span>
                            <span class="text-slate-400 text-xs mt-1 block">Max 2MB.</span>
                        </label>
                    </div>

                    <!-- Tab: STIKER -->
                    <div x-show="activeTab === 'stickers'" style="display: none;" class="flex flex-col h-full bg-white">
                         <!-- Search Box Stiker -->
                         <div class="flex mb-4">
                            <input type="text" id="stickerSearchInput" placeholder="Cari ikon (cth: cat, fire, car)" class="flex-1 border border-slate-300 rounded-l-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <button id="searchStickerBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-r-lg text-sm font-medium transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                        </div>
                        <div id="stickersContainer" class="grid grid-cols-2 gap-3 overflow-y-auto pb-4 custom-scrollbar">
                            <div class="col-span-2 text-center text-xs text-slate-400 py-4">Memuat stiker...</div>
                        </div>
                    </div>

                    <!-- Tab: TEKS -->
                    <div x-show="activeTab === 'text'" style="display: none;" class="space-y-4">
                        <button id="addTextBtn" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg hover:bg-indigo-500 transition shadow-indigo-200">
                            + Tambahkan Teks Baru
                        </button>
                        <p class="text-xs text-slate-400 text-center">Modifikasi warna dan font dari menu kontrol saat teks di-klik.</p>
                    </div>

                </div>
            </div>

            <!-- Canvas Area Tengah -->
            <div class="flex-1 flex flex-col relative bg-slate-100 overflow-hidden">
                
                <!-- Toolbar Atas (Ganti Warna Base & Info) -->
                <div class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 shadow-sm z-10 w-full flex-shrink-0">
                    <div class="flex items-center gap-4">
                        <span class="font-bold text-slate-700">{{ $produk->nama_produk }}</span>
                        <!-- Sisi Baju Toggle -->
                        <div class="flex items-center bg-slate-100 rounded-lg p-1 border border-slate-200 ml-4">
                            <button @click="activeSide = 'front'" :class="activeSide === 'front' ? 'bg-white shadow-sm text-indigo-700 font-bold' : 'text-slate-500 hover:text-slate-700'" class="px-4 py-1 text-sm rounded-md transition-all">Depan</button>
                            <button @click="activeSide = 'back'" :class="activeSide === 'back' ? 'bg-white shadow-sm text-indigo-700 font-bold' : 'text-slate-500 hover:text-slate-700'" class="px-4 py-1 text-sm rounded-md transition-all">Belakang</button>
                        </div>
                        <!-- Base Color Picker -->
                        <div class="flex items-center gap-2 border-l border-slate-200 pl-4">
                            <span class="text-sm text-slate-500 font-medium">Warna Baju:</span>
                            <div class="flex gap-2">
                                <button @click="baseColor = '#ffffff'; canvasBackgroundChange('#ffffff')" class="w-6 h-6 rounded-full bg-white border border-slate-300 shadow-sm hover:ring-2 hover:ring-indigo-400 hover:ring-offset-1 transition"></button>
                                <button @click="baseColor = '#1e293b'; canvasBackgroundChange('#1e293b')" class="w-6 h-6 rounded-full bg-slate-800 border border-transparent shadow-sm hover:ring-2 hover:ring-indigo-400 hover:ring-offset-1 transition"></button>
                                <button @click="baseColor = '#ef4444'; canvasBackgroundChange('#ef4444')" class="w-6 h-6 rounded-full bg-red-500 border border-transparent shadow-sm hover:ring-2 hover:ring-indigo-400 hover:ring-offset-1 transition"></button>
                                <button @click="baseColor = '#3b82f6'; canvasBackgroundChange('#3b82f6')" class="w-6 h-6 rounded-full bg-blue-500 border border-transparent shadow-sm hover:ring-2 hover:ring-indigo-400 hover:ring-offset-1 transition"></button>
                                <button @click="baseColor = '#f59e0b'; canvasBackgroundChange('#f59e0b')" class="w-6 h-6 rounded-full bg-amber-500 border border-transparent shadow-sm hover:ring-2 hover:ring-indigo-400 hover:ring-offset-1 transition"></button>
                            </div>
                        </div>
                    </div>
                    
                    <button id="saveDesignBtn" class="bg-indigo-600 text-white font-bold py-2 px-6 rounded-lg shadow-lg hover:bg-indigo-500 transition hover:-translate-y-0.5 shadow-indigo-200 flex items-center gap-2 text-sm">
                        <span>Lanjut Order</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>

                <!-- Canvas Workspace Container -->
                <div class="flex-1 overflow-auto flex justify-center items-center py-8 relative">
                    <!-- Layer Control Floating Tool (Muncul saat diklik) -->
                    <div id="editorControls" class="absolute top-12 left-1/2 transform -translate-x-1/2 bg-white px-4 py-2 border border-slate-200 shadow-xl rounded-xl items-center gap-3 z-50 hidden transition-all">
                        
                        <!-- Image Controls (Remove Background) -->
                        <div id="imageControls" class="hidden items-center gap-2 border-r border-slate-200 pr-3 mr-1">
                            <button id="removeBgBtn" title="Hapus Background Putih/Polos" class="bg-indigo-50 border border-indigo-200 text-indigo-600 text-xs font-bold px-3 py-1.5 rounded hover:bg-indigo-100 flex items-center gap-1 transition">
                                <span>✨ Hapus BG Putih</span>
                            </button>
                        </div>

                        <!-- SVG Vector Controls -->
                        <div id="svgControls" class="hidden items-center gap-2 border-r border-slate-200 pr-3 mr-1">
                            <span class="text-xs font-bold text-slate-500">Warna Vektor:</span>
                            <input type="color" id="svgColorControl" class="w-8 h-8 p-0.5 border-0 rounded cursor-pointer" value="#000000" title="Ubah Warna Vektor">
                        </div>

                        <!-- Text Controls -->
                        <div id="textControls" class="hidden items-center gap-2 border-r border-slate-200 pr-3 mr-1">
                            <select id="fontFamilyControl" class="text-sm border-slate-300 rounded py-1 pl-2 pr-8 focus:ring-indigo-500 focus:border-indigo-500" style="padding-top:0.25rem; padding-bottom:0.25rem;">
                                <option value="Arial">Arial</option>
                                <option value="'Times New Roman'">Times New Roman</option>
                                <option value="Courier New">Courier</option>
                                <option value="Impact">Impact</option>
                                <option value="'Comic Sans MS'">Comic Sans</option>
                            </select>
                            <input type="color" id="textColorControl" class="w-8 h-8 p-0.5 border-0 rounded cursor-pointer" value="#000000" title="Warna Teks">
                            <div class="h-6 w-px bg-slate-200 mx-1"></div>
                            
                            <!-- Advanced Text Features -->
                            <div class="flex items-center gap-1" title="Outline / Stroke Teks">
                                <span class="text-[10px] font-bold text-slate-400">Outline:</span>
                                <input type="color" id="textStrokeColor" class="w-6 h-6 p-0 border-0 rounded cursor-pointer" value="#ffffff">
                                <input type="range" id="textStrokeWidth" min="0" max="10" value="0" class="w-12 h-1 bg-slate-200 rounded-lg appearance-none cursor-pointer">
                            </div>
                            <div class="h-6 w-px bg-slate-200 mx-1"></div>
                            <label class="flex items-center gap-1 cursor-pointer text-xs font-bold text-slate-500" title="Efek Bayangan">
                                <input type="checkbox" id="textShadowToggle" class="rounded text-indigo-600 form-checkbox h-4 w-4">
                                Shadow
                            </label>
                        </div>

                        <!-- Layer Control -->
                        <button id="bringForwardBtn" title="Bawa ke Depan" class="w-8 h-8 rounded shrink-0 bg-slate-50 hover:bg-slate-100 flex items-center justify-center text-slate-600 transition border border-slate-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 12m4-4v12"></path></svg></button>
                        <button id="sendBackwardBtn" title="Pindah ke Belakang" class="w-8 h-8 rounded shrink-0 bg-slate-50 hover:bg-slate-100 flex items-center justify-center text-slate-600 transition border border-slate-200"><svg class="w-4 h-4 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 12m4-4v12"></path></svg></button>
                        <div class="h-6 w-px bg-slate-200 mx-1"></div>
                        <button id="deleteObjBtn" title="Hapus Objek" class="w-8 h-8 rounded shrink-0 bg-red-50 hover:bg-red-100 flex items-center justify-center text-red-500 transition border border-red-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>

                    <!-- Layout Canvas + Base -->
                    <!-- Container Absolute 480x600 untuk Mockup Static Background -->
                    <div class="relative shadow-2xl rounded-xl overflow-hidden pointer-events-auto flex items-center justify-center bg-slate-100" id="mockupContainer" style="width: 480px; height: 600px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);">
                        
                        <!-- Indikator Sisi Baju -->
                        <div class="absolute top-4 left-4 z-30 pointer-events-none">
                            <span class="bg-indigo-600/90 text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-lg border border-indigo-400/50 uppercase tracking-widest backdrop-blur-sm" x-text="activeSide === 'front' ? 'Bagian Depan' : 'Bagian Belakang'">Bagian Depan</span>
                        </div>
                        
                        <!-- Latar Baju Fotorealistis (Statik/Locked Background) -->
                        <div class="absolute inset-0 z-0 pointer-events-none" id="baseColorContainer" :class="activeSide === 'back' ? '-scale-x-100' : ''">
                             <div class="w-full h-full flex items-center justify-center relative overflow-hidden">
                                @if($produk->jenis_produk == 'kaos')
                                    <!-- Base Foto Kaos Nyata -->
                                    <img src="{{ asset('images/mockups/kaos.png?v='.time()) }}" class="absolute w-[85%] h-[85%] object-contain drop-shadow-2xl opacity-90">
                                    
                                    <!-- Layer Masking Warna Kaos (Tepat di atas foto, mix-blend multiply) -->
                                    <div class="absolute w-[85%] h-[85%] mix-blend-multiply"
                                         style="-webkit-mask-image: url('{{ asset('images/mockups/kaos.png?v='.time()) }}'); -webkit-mask-size: contain; -webkit-mask-position: center; -webkit-mask-repeat: no-repeat; mask-image: url('{{ asset('images/mockups/kaos.png?v='.time()) }}'); mask-size: contain; mask-position: center; mask-repeat: no-repeat;">
                                        <div class="w-full h-full transition-colors duration-300" :style="`background-color: ${baseColor};`"></div>
                                    </div>
                                @elseif($produk->jenis_produk == 'hoodie')
                                    <!-- Base Foto Hoodie Nyata -->
                                    <img src="{{ asset('images/mockups/hoodie.png?v='.time()) }}" class="absolute w-[85%] h-[85%] object-contain drop-shadow-2xl opacity-90">
                                    
                                    <!-- Layer Masking Warna Hoodie -->
                                    <div class="absolute w-[85%] h-[85%] mix-blend-multiply"
                                         style="-webkit-mask-image: url('{{ asset('images/mockups/hoodie.png?v='.time()) }}'); -webkit-mask-size: contain; -webkit-mask-position: center; -webkit-mask-repeat: no-repeat; mask-image: url('{{ asset('images/mockups/hoodie.png?v='.time()) }}'); mask-size: contain; mask-position: center; mask-repeat: no-repeat;">
                                        <div class="w-full h-full transition-colors duration-300" :style="`background-color: ${baseColor};`"></div>
                                    </div>
                                @endif
                             </div>
                        </div>
                        
                        <!-- Print Area Visualizer (Kotak Dashed) Transparan yang letaknya pas di dada -->
                        <!-- Z-index 10 di belakang canvas, Z-index 20 canvasnya -->
                        <div class="absolute z-10 border border-dashed border-slate-600/40 pointer-events-none rounded transition-colors group" id="printAreaBox" style="width: 220px; height: 320px; top: 120px; left: 130px;">
                            <span class="absolute -top-7 left-1/2 transform -translate-x-1/2 text-[10px] text-slate-700/60 font-bold uppercase tracking-wider bg-white/50 px-2 rounded backdrop-blur border border-white/50 ">Area Cetak</span>
                            <!-- Grip lines corners -->
                            <div class="absolute -top-1 -left-1 w-2 h-2 border-t border-l border-indigo-400"></div>
                            <div class="absolute -top-1 -right-1 w-2 h-2 border-t border-r border-indigo-400"></div>
                            <div class="absolute -bottom-1 -left-1 w-2 h-2 border-b border-l border-indigo-400"></div>
                            <div class="absolute -bottom-1 -right-1 w-2 h-2 border-b border-r border-indigo-400"></div>
                        </div>

                        <!-- Fabric.js Canvas hanya sebesar area cetak didada -->
                        <!-- Sehingga desain user TIDAK AKAN PERNAH bisa digeser keluar dari area dada kaos -->
                        <div class="absolute z-20" style="top: 120px; left: 130px; width: 220px; height: 320px;" x-show="activeSide === 'front'">
                            <canvas id="tshirt-canvas-front" width="220" height="320"></canvas>
                        </div>
                        <div class="absolute z-20" style="top: 120px; left: 130px; width: 220px; height: 320px;" x-show="activeSide === 'back'" style="display: none;">
                            <canvas id="tshirt-canvas-back" width="220" height="320"></canvas>
                        </div>
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
        // Alpine data binding bridge
        window.canvasBackgroundChange = function(color) {
            // Kita render warna base via html agar tidak mengotori DataURL save format png
            const printbox = document.getElementById('printAreaBox');
            if(color === '#1e293b') {
                printbox.classList.replace('border-slate-800/20', 'border-white/30');
            } else {
                printbox.classList.replace('border-white/30', 'border-slate-800/20');
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi Canvas Fabric (Ukuran baru: 480x600)
            const canvasFront = new fabric.Canvas('tshirt-canvas-front', {
                preserveObjectStacking: true,
                selection: true
            });
            const canvasBack = new fabric.Canvas('tshirt-canvas-back', {
                preserveObjectStacking: true,
                selection: true
            });
            
            let canvas = canvasFront; // pointer ke canvas yang aktif saat ini

            window.switchCanvasSide = function(side) {
                if(canvas) {
                    canvas.discardActiveObject();
                    canvas.renderAll();
                }
                if (typeof hideControls === 'function') hideControls();
                canvas = side === 'front' ? canvasFront : canvasBack;
            };

            // Batasan Area Cetak (Canvas sekarang HANYA seukuran Print Area)
            const printArea = { top: 0, left: 0, width: 220, height: 320 };

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
            fabric.Object.prototype.cornerStrokeColor = '#e2e8f0';
            fabric.Object.prototype.borderColor = '#6366f1';
            fabric.Object.prototype.cornerSize = 12;
            fabric.Object.prototype.padding = 8;
            fabric.Object.prototype.borderDashArray = [4, 4];

            // Setup Custom Icons SVG
            const deleteIconSvg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23ef4444' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'/%3E%3C/svg%3E";
            const scaleIconSvg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236366f1' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4'/%3E%3C/svg%3E";
            const rotateIconSvg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236366f1' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'/%3E%3C/svg%3E";

            const delImg = document.createElement('img'); delImg.src = deleteIconSvg;
            const sclImg = document.createElement('img'); sclImg.src = scaleIconSvg;
            const rotImg = document.createElement('img'); rotImg.src = rotateIconSvg;

            function renderCustomIcon(ctx, left, top, styleOverride, fabricObject) {
                const size = this.cornerSize;
                ctx.save();
                ctx.translate(left, top);
                // Background bulat shadow
                ctx.fillStyle = '#ffffff';
                ctx.shadowColor = 'rgba(0,0,0,0.15)';
                ctx.shadowBlur = 4;
                ctx.beginPath();
                ctx.arc(0, 0, size/2 + 4, 0, Math.PI * 2);
                ctx.fill();
                // Gambar ikon
                ctx.drawImage(this.iconImg, -size/2, -size/2, size, size);
                ctx.restore();
            }

            // Hapus action dasar corner yang tidak dipakai agar tidak mengganggu
            fabric.Object.prototype.controls.tr = new fabric.Control({
                x: 0.5, y: -0.5,
                offsetX: 12, offsetY: -12,
                cursorStyle: 'pointer',
                actionHandler: fabric.controlsUtils.deleteObject || function(eventData, transform) {
                    var target = transform.target;
                    target.canvas.remove(target); target.canvas.requestRenderAll();
                    return true;
                },
                render: renderCustomIcon,
                cornerSize: 18
            });
            fabric.Object.prototype.controls.tr.iconImg = delImg;

            fabric.Object.prototype.controls.br = new fabric.Control({
                x: 0.5, y: 0.5,
                offsetX: 12, offsetY: 12,
                cursorStyle: 'se-resize',
                actionHandler: fabric.controlsUtils.scalingEqually,
                render: renderCustomIcon,
                cornerSize: 18
            });
            fabric.Object.prototype.controls.br.iconImg = sclImg;

            fabric.Object.prototype.controls.bl = new fabric.Control({
                x: -0.5, y: 0.5,
                offsetX: -12, offsetY: 12,
                cursorStyle: 'alias',
                actionHandler: fabric.controlsUtils.rotationWithSnapping,
                render: renderCustomIcon,
                cornerSize: 18
            });
            fabric.Object.prototype.controls.bl.iconImg = rotImg;

            // Sembunyikan titik corner lainnya (mt, mb, ml, mr, tl) agar UI bersih
            fabric.Object.prototype.controls.mt.visible = false;
            fabric.Object.prototype.controls.mb.visible = false;
            fabric.Object.prototype.controls.ml.visible = false;
            fabric.Object.prototype.controls.mr.visible = false;
            fabric.Object.prototype.controls.tl.visible = false;

            // Fungsi tambah TEXT
            document.getElementById('addTextBtn').addEventListener('click', function() {
                const text = new fabric.IText('Teks Anda', {
                    left: 20,
                    top: 20,
                    fontFamily: 'Arial',
                    fill: '#000000',
                    fontSize: 40,
                    fontWeight: 'bold',
                });
                canvas.add(text);
                canvas.setActiveObject(text);
                activeMode('text');
            });

            // Ganti Font
            fontFamilyControl.addEventListener('change', function() {
                const activeObj = canvas.getActiveObject();
                if(activeObj && activeObj.type === 'i-text') {
                    activeObj.set('fontFamily', this.value);
                    canvas.renderAll();
                }
            });

            // Ganti Warna Font
            textColorControl.addEventListener('input', function() {
                const activeObj = canvas.getActiveObject();
                if(activeObj && activeObj.type === 'i-text') {
                    activeObj.set('fill', this.value);
                    canvas.renderAll();
                }
            });

            // Stroke (Outline) Teks
            textStrokeColor.addEventListener('input', function() {
                const activeObj = canvas.getActiveObject();
                if(activeObj && activeObj.type === 'i-text') {
                    activeObj.set({ stroke: this.value, strokeWidth: parseInt(textStrokeWidth.value) });
                    canvas.renderAll();
                }
            });
            textStrokeWidth.addEventListener('input', function() {
                const activeObj = canvas.getActiveObject();
                if(activeObj && activeObj.type === 'i-text') {
                    activeObj.set({ stroke: textStrokeColor.value, strokeWidth: parseInt(this.value) });
                    canvas.renderAll();
                }
            });

            // Shadow Teks
            textShadowToggle.addEventListener('change', function() {
                const activeObj = canvas.getActiveObject();
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
                    canvas.renderAll();
                }
            });

            // Setup Layer Management
            bringForwardBtn.addEventListener('click', function() {
                const activeObj = canvas.getActiveObject();
                if(activeObj) { canvas.bringForward(activeObj); }
            });

            sendBackwardBtn.addEventListener('click', function() {
                const activeObj = canvas.getActiveObject();
                if(activeObj) { canvas.sendBackwards(activeObj); }
            });

            // Fungsi tambah STICKER/IMAGE ke canvas
            function addImageToCanvas(url) {
                const imgEl = new Image();
                imgEl.onload = function() {
                    const img = new fabric.Image(imgEl);
                    if(img.width > printArea.width) img.scaleToWidth(printArea.width - 20);
                    else if(img.width < 40) img.scaleToWidth(80);
                    
                    img.set({ left: 20, top: 20 });
                    img.customType = 'custom-image'; // Penanda image statis
                    canvas.add(img);
                    canvas.setActiveObject(img);
                };
                imgEl.onerror = function() { alert('Gagal memuat elemen desain.'); };
                imgEl.src = url;
            }

            function addSVGToCanvas(url) {
                fabric.loadSVGFromURL(url, function(objects, options) {
                    if (!objects || objects.length === 0) return;
                    const group = fabric.util.groupSVGElements(objects, options);
                    
                    const targetSize = 80;
                    const maxDim = Math.max(group.width, group.height);
                    if (maxDim > 0) group.scale(targetSize / maxDim);
                    
                    group.set({ left: 20, top: 20 });
                    group.customType = 'custom-svg'; // Penanda object adalah SVG Vector
                    canvas.add(group);
                    canvas.setActiveObject(group);
                });
            }

            // --- TAB UPLOAD ---
            document.getElementById('imageLoader').addEventListener('change', function(e) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    var imgObj = new Image();
                    imgObj.src = event.target.result;
                    imgObj.onload = function() {
                        var img = new fabric.Image(imgObj);
                        if(img.width > printArea.width) img.scaleToWidth(printArea.width - 20);
                        img.set({ left: 10, top: 10 });
                        img.customType = 'custom-image';
                        canvas.add(img);
                        canvas.setActiveObject(img);
                    }
                }
                reader.readAsDataURL(e.target.files[0]);
                e.target.value = ''; // reset
            });

            // --- TAB TEMPLATE ---
            document.querySelectorAll('.template-item').forEach(item => {
                item.addEventListener('click', function() { addImageToCanvas(this.getAttribute('data-url')); });
            });

            // --- TAB STIKER ---
            const stickerSearchInput = document.getElementById('stickerSearchInput');
            const searchStickerBtn   = document.getElementById('searchStickerBtn');
            const stickersContainer  = document.getElementById('stickersContainer');

            function loadStickers(query = 'cool') {
                stickersContainer.innerHTML = '<div class="col-span-2 text-center py-5"><svg class="animate-spin h-5 w-5 text-indigo-500 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg></div>';
                
                fetch(`{{ route('customer.api.stickers') }}?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(json => {
                        stickersContainer.innerHTML = '';
                        if(json.success && json.data.length > 0) {
                            json.data.forEach(sticker => {
                                const btn = document.createElement('div');
                                btn.className = 'relative bg-slate-50 border border-slate-200 rounded-lg cursor-pointer hover:border-indigo-500 transition duration-200 flex items-center justify-center p-2 aspect-square group';
                                
                                const isSVG = sticker.source === 'Iconify';
                                btn.innerHTML = `<img src="${sticker.url}" loading="lazy" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-200 max-h-16">
                                                <span class="absolute bottom-1 right-1 bg-slate-800/50 text-white text-[9px] px-1 rounded shadow">${isSVG ? 'Vector' : 'Art'}</span>`;
                                btn.addEventListener('click', function() { isSVG ? addSVGToCanvas(sticker.url) : addImageToCanvas(sticker.url); });
                                stickersContainer.appendChild(btn);
                            });
                        } else {
                            stickersContainer.innerHTML = '<div class="col-span-2 text-center text-xs text-slate-500 py-4">Ikon tidak ditemukan.</div>';
                        }
                    }).catch(() => stickersContainer.innerHTML = '<div class="col-span-2 text-center text-xs text-red-400 py-4">Gagal menghubungkan.</div>');
            }
            // Auto search default
            loadStickers('cool');
            searchStickerBtn.addEventListener('click', () => { if(stickerSearchInput.value.trim()) loadStickers(stickerSearchInput.value.trim()); });
            stickerSearchInput.addEventListener('keypress', (e) => { if(e.key === 'Enter' && stickerSearchInput.value.trim()) loadStickers(stickerSearchInput.value.trim()); });

            // --- EDITOR CONTROLS LOGIC ---
            [canvasFront, canvasBack].forEach(c => {
                c.on('selection:created', showControls);
                c.on('selection:updated', showControls);
                c.on('selection:cleared', hideControls);
            });

            function showControls(e) {
                editorControls.classList.remove('hidden');
                editorControls.classList.add('flex');
                const activeObj = e.selected[0];
                
                // Hide All Contextual Controls first
                textControls.classList.add('hidden'); textControls.classList.remove('flex');
                imageControls.classList.add('hidden'); imageControls.classList.remove('flex');
                svgControls.classList.add('hidden'); svgControls.classList.remove('flex');

                if(activeObj && activeObj.type === 'i-text') {
                    textControls.classList.remove('hidden');
                    textControls.classList.add('flex');
                    fontFamilyControl.value = activeObj.fontFamily.replace(/["']/g, "");
                    textColorControl.value = activeObj.fill;
                    textStrokeColor.value = activeObj.stroke || '#ffffff';
                    textStrokeWidth.value = activeObj.strokeWidth || 0;
                    textShadowToggle.checked = !!activeObj.shadow;
                } 
                else if (activeObj && activeObj.type === 'image' && activeObj.customType === 'custom-image') {
                    imageControls.classList.remove('hidden');
                    imageControls.classList.add('flex');
                }
                else if (activeObj && activeObj.type === 'group' && activeObj.customType === 'custom-svg') {
                    svgControls.classList.remove('hidden');
                    svgControls.classList.add('flex');
                    // Get initial color from the first path of SVG
                    if(activeObj._objects && activeObj._objects.length > 0) {
                        // Some SVGs use 'fill', some 'stroke'. Prefer fill.
                        let initialColor = activeObj._objects[0].fill;
                        if(initialColor && typeof initialColor === 'string' && initialColor.startsWith('#')) {
                            svgColorControl.value = initialColor;
                        }
                    }
                }
            }

            // Logic Remove Background (Magic Eraser) for Images
            removeBgBtn.addEventListener('click', function() {
                const activeObj = canvas.getActiveObject();
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
                        distance: 0.15 
                    });

                    activeObj.filters.push(filter);
                    activeObj.applyFilters();
                    canvas.renderAll();

                    setTimeout(() => {
                        this.innerHTML = oldHtml;
                        this.disabled = false;
                    }, 500);
                }
            });

            // Logic Merubah Warna Dynamic pada SVG
            svgColorControl.addEventListener('input', function() {
                const activeObj = canvas.getActiveObject();
                if(activeObj && activeObj.type === 'group' && activeObj.customType === 'custom-svg') {
                    const newColor = this.value;
                    // Loop setiap elemen di dalam grup vektor SVG
                    activeObj._objects.forEach(pathObj => {
                        // Jangan warnai elemen yang tidak punya fill atau transparan
                        if(pathObj.fill && pathObj.fill !== 'none' && pathObj.fill !== 'transparent') {
                            pathObj.set('fill', newColor);
                        }
                        if(pathObj.stroke && pathObj.stroke !== 'none' && pathObj.stroke !== 'transparent') {
                             pathObj.set('stroke', newColor);
                        }
                    });
                    canvas.renderAll();
                }
            });

            function hideControls() {
                editorControls.classList.add('hidden');
                editorControls.classList.remove('flex');
            }

            deleteObjBtn.addEventListener('click', function() {
                const activeObj = canvas.getActiveObject();
                if(activeObj) { canvas.remove(activeObj); hideControls(); }
            });

            // --- SUBMIT SAVE TO SERVER ---
            document.getElementById('saveDesignBtn').addEventListener('click', function() {
                canvasFront.discardActiveObject(); 
                canvasFront.renderAll();
                canvasBack.discardActiveObject(); 
                canvasBack.renderAll();

                const activeBaseColor = document.getElementById('baseColorContainer').style.backgroundColor || '#ffffff';
                const lebarCm = 30; // Proporsi standar A3 sablon
                const tinggiCm = 45;

                let frontDataURL = '';
                let backDataURL = '';

                // Ambil data jika ada objek (atau jika canvas kosong, kita kirimkan blank untuk depan sebagai mandatory)
                frontDataURL = canvasFront.toDataURL({ format: 'png', quality: 1, multiplier: 4 });
                
                if (canvasBack.getObjects().length > 0) {
                    backDataURL = canvasBack.toDataURL({ format: 'png', quality: 1, multiplier: 4 });
                }

                const payload = {
                    _token: '{{ csrf_token() }}',
                    id_produk: '{{ $produk->id_produk }}',
                    file_desain: frontDataURL, 
                    lebar_cm: lebarCm,
                    tinggi_cm: tinggiCm,
                    warna_baju: activeBaseColor
                };

                if (backDataURL !== '') {
                    payload.file_desain_belakang = backDataURL;
                    payload.lebar_cm_belakang = lebarCm;
                    payload.tinggi_cm_belakang = tinggiCm;
                }

                const oldText = this.innerHTML;
                this.innerHTML = 'Memproses... ⏳';
                this.disabled = true;

                fetch('{{ route('customer.designs.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        const checkoutUrl = new URL('{{ route('customer.checkout.index') }}', window.location.origin);
                        checkoutUrl.searchParams.append('id_produk', '{{ $produk->id_produk }}');
                        checkoutUrl.searchParams.append('id_desain', data.id_desain);
                        // Optional pass base color
                        // checkoutUrl.searchParams.append('warna_baju', activeBaseColor);

                        window.location.href = checkoutUrl;
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
        });
    </script>
</x-app-layout>
