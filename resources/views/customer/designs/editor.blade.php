<x-app-layout>
    <script>
        window.activeBaseColorLocal = '#ffffff'; 
        window.canvasBackgroundChange = function(color) {
            window.activeBaseColorLocal = color;
            const printbox = document.getElementById('printAreaBox');
            if(printbox) {
                if(color === '#1e293b') printbox.classList.replace('border-slate-800/20', 'border-white/30');
                else printbox.classList.replace('border-white/30', 'border-slate-800/20');
            }
        };
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Dancing+Script:wght@700&family=Lobster&family=Montserrat:wght@400;700&family=Pacifico&family=Playfair+Display:wght@700&family=Roboto:wght@400;700&family=Oswald:wght@500&family=Anton&display=swap" rel="stylesheet">

    <div id="editor-alpine" wire:ignore class="min-h-screen bg-slate-50" 
         x-data="{ 
             currentMode: 'editor',
             activeTab: 'upload', 
             baseColor: '{{ $desainRevisi && $desainRevisi->warna_baju ? $desainRevisi->warna_baju : '#ffffff' }}',
             activeSide: 'front', 
             sidebarOpen: true,
             hasFrontDesign: false,
             hasBackDesign: false,
             hasLeftDesign: false,
             hasRightDesign: false,
             colorMap: {
                 'Maroon': '#7f1d1d', 'Green': '#14532d', 'Grey': '#94a3b8', 
                 'Army': '#4B5320', 'Yellow': '#facc15', 'White': '#ffffff', 
                 'Navy': '#1e3a8a', 'Orange': '#f97316', 'Black': '#1e293b', 
                 'Red': '#dc2626', 'Blue': '#3b82f6', 'Mint': '#a7f3d0'
             },
             selectSide(side) {
                 this.activeSide = side;
                 if(window.switchCanvasSide) window.switchCanvasSide(side);
                 this.updateDesignStatus();
                 setTimeout(() => {
                     if(typeof window.setupMobileCanvasScaler === 'function') window.setupMobileCanvasScaler();
                     if(window.activeCanvas) window.activeCanvas.calcOffset();
                 }, 80);
                 if(typeof window.saveLocalDraft === 'function') window.saveLocalDraft();
             },
             changeColor(hex) {
                 this.baseColor = hex;
                 if(typeof window.canvasBackgroundChange === 'function') window.canvasBackgroundChange(hex);
                 if(typeof window.saveLocalDraft === 'function') window.saveLocalDraft();
             },
             updateDesignStatus() {
                 this.hasFrontDesign = window.canvasFront ? window.canvasFront.getObjects().length > 0 : false;
                 this.hasBackDesign = window.canvasBack ? window.canvasBack.getObjects().length > 0 : false;
                 this.hasLeftDesign = window.canvasLeft ? window.canvasLeft.getObjects().length > 0 : false;
                 this.hasRightDesign = window.canvasRight ? window.canvasRight.getObjects().length > 0 : false;
             },
             openPreview() {
                 this.updateDesignStatus();
                 this.currentMode = 'preview';
                 if(typeof window.generatePreviews === 'function') {
                     setTimeout(() => window.generatePreviews(), 100);
                 }
             },
             backToEditor() {
                 this.currentMode = 'editor';
                 setTimeout(() => {
                     if(typeof window.setupMobileCanvasScaler === 'function') window.setupMobileCanvasScaler();
                     if(window.activeCanvas) window.activeCanvas.calcOffset();
                 }, 80);
             },
             getColorName(hex) {
                 for(let [name, val] of Object.entries(this.colorMap)) { if(val === hex) return name; }
                 return hex;
             }
         }" 
         x-init="$nextTick(() => { 
             const urlColor = new URLSearchParams(window.location.search).get('color');
             if(urlColor && colorMap[urlColor]) { baseColor = colorMap[urlColor]; }
             if(typeof window.canvasBackgroundChange === 'function') window.canvasBackgroundChange(baseColor);
         })">

        {{-- ===== HEADER TOOLBAR ===== --}}
        <div class="sticky top-0 z-30 bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-2.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 sm:gap-6">
                        <a href="{{ route('customer.products.show', $produk->id_produk) }}" class="flex items-center gap-2 hover:opacity-80 transition group" title="Kembali ke Produk">
                            <img src="{{ asset('images/logo-dailyco.png') }}" class="h-6 sm:h-7 w-auto" alt="Logo">
                        </a>
                        <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500">
                            <span class="font-semibold text-slate-700">{{ $produk->nama_produk }}</span>
                            <span class="text-slate-300">•</span>
                            <span class="capitalize text-slate-500">{{ $produk->jenis_produk }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3">
                        <!-- Quick Color Picker Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.outside="open = false" type="button" class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 transition text-xs font-medium text-slate-700 shadow-xs" title="Ubah Warna Dasar Baju">
                                <span class="w-3.5 h-3.5 rounded-full border border-slate-300 shadow-xs" :style="{ backgroundColor: baseColor }"></span>
                                <span class="hidden sm:inline" x-text="getColorName(baseColor)"></span>
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" x-transition.origin.top.right class="absolute right-0 mt-2 p-3 bg-white border border-slate-200 rounded-xl shadow-lg z-50 w-60" style="display: none;">
                                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Warna Baju</p>
                                <div class="grid grid-cols-4 gap-2">
                                    <template x-for="(hex, name) in colorMap" :key="name">
                                        <button @click="changeColor(hex); open = false" class="group flex flex-col items-center gap-1 p-1 rounded-md hover:bg-slate-50 transition" :title="name">
                                            <div class="w-6 h-6 rounded-full border border-slate-300 transition-all shadow-xs"
                                                 :style="{ backgroundColor: hex }"
                                                 :class="baseColor === hex ? 'ring-2 ring-slate-900 ring-offset-1 scale-105' : 'group-hover:scale-105'"></div>
                                            <span class="text-[8px] font-medium text-slate-500 truncate w-full text-center" x-text="name"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Draft Autosave & Status Badge -->
                        <div id="draftStatusBadge" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-medium border transition-all duration-300 bg-slate-50 border-slate-200 text-slate-500">
                            <span id="draftStatusDot" class="w-1.5 h-1.5 rounded-full bg-slate-400 transition-colors duration-300"></span>
                            <span id="draftStatusText" class="hidden xs:inline">Draft Siap</span>
                        </div>

                        <!-- Undo / Redo Actions -->
                        <div class="flex items-center gap-0.5 border border-slate-200 rounded-lg p-0.5 bg-white">
                            <button id="btnUndoAction" onclick="if(typeof window.undoHistory === 'function') window.undoHistory()" disabled class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-50 rounded transition opacity-50 cursor-not-allowed" title="Undo (Ctrl+Z)">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                            </button>
                            <button id="btnRedoAction" onclick="if(typeof window.redoHistory === 'function') window.redoHistory()" disabled class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-50 rounded transition opacity-50 cursor-not-allowed" title="Redo (Ctrl+Y)">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($desainRevisi)
        <div class="bg-amber-50 border-b border-amber-200 px-4 py-3 flex items-center gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="text-xs text-amber-900 font-medium">Revisi Admin: <span class="italic font-semibold">'{{ \App\Models\OrderDetail::where('id_desain', $desainRevisi->id_desain)->value('catatan_admin') }}'</span></p>
            </div>
        </div>
        @endif

        {{-- ===== VIEW 1: CANVAS WORKSPACE ===== --}}
        <div x-show="currentMode === 'editor'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            @include('customer.designs._canvas_editor')
        </div>

        {{-- ===== VIEW 2: PREVIEW & ORDER SUMMARY ===== --}}
        <div x-show="currentMode === 'preview'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
            <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
                <div class="text-center mb-8">
                    <span class="px-2.5 py-0.5 bg-slate-100 text-slate-600 text-[11px] font-medium rounded-md uppercase tracking-wider mb-2 inline-block">Ringkasan Desain</span>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 mb-1">Review Desain Kaos</h1>
                    <p class="text-xs text-slate-500">Periksa kembali tampilan desain sebelum melanjutkan ke pemesanan</p>
                </div>

                {{-- Dynamic Previews Grid: HANYA menampilkan sisi yang memiliki objek desain --}}
                <div class="flex flex-wrap justify-center gap-4 sm:gap-6 mb-8">
                    {{-- Front Preview (Always show if front has design OR if all empty) --}}
                    <div class="bg-white rounded-xl border border-slate-200 p-4 text-center shadow-xs flex-1 min-w-[220px] max-w-[280px]" x-show="hasFrontDesign || (!hasBackDesign && !hasLeftDesign && !hasRightDesign)">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-xs font-semibold text-slate-700">Tampak Depan</h4>
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" title="Desain Siap"></span>
                        </div>
                        <div class="bg-slate-50 rounded-lg border border-slate-100 overflow-hidden mx-auto w-full h-[280px] flex items-center justify-center">
                            <img id="preview-front" src="" alt="Preview Depan" class="w-full h-full object-contain" style="display:none;" onload="this.style.display='block'; if(this.nextElementSibling) this.nextElementSibling.style.display='none';">
                            <div class="flex items-center justify-center h-full text-slate-400 text-xs font-medium">
                                <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Memuat preview...
                            </div>
                        </div>
                    </div>

                    {{-- Back Preview (Hanya tampil jika ada objek di belakang) --}}
                    <div class="bg-white rounded-xl border border-slate-200 p-4 text-center shadow-xs flex-1 min-w-[220px] max-w-[280px]" x-show="hasBackDesign" x-cloak>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-xs font-semibold text-slate-700">Tampak Belakang</h4>
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" title="Desain Siap"></span>
                        </div>
                        <div class="bg-slate-50 rounded-lg border border-slate-100 overflow-hidden mx-auto w-full h-[280px] flex items-center justify-center">
                            <img id="preview-back" src="" alt="Preview Belakang" class="w-full h-full object-contain" style="display:none;" onload="this.style.display='block'; if(this.nextElementSibling) this.nextElementSibling.style.display='none';">
                            <div class="flex items-center justify-center h-full text-slate-400 text-xs font-medium">
                                <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Memuat preview...
                            </div>
                        </div>
                    </div>

                    @if(in_array($produk->jenis_produk, ['hoodie', 'kaos']))
                    {{-- Left Preview (Hanya tampil jika ada objek di kiri) --}}
                    <div class="bg-white rounded-xl border border-slate-200 p-4 text-center shadow-xs flex-1 min-w-[220px] max-w-[280px]" x-show="hasLeftDesign" x-cloak>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-xs font-semibold text-slate-700">Lengan Kiri</h4>
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" title="Desain Siap"></span>
                        </div>
                        <div class="bg-slate-50 rounded-lg border border-slate-100 overflow-hidden mx-auto w-full h-[280px] flex items-center justify-center">
                            <img id="preview-left" src="" alt="Preview Kiri" class="w-full h-full object-contain" style="display:none;" onload="this.style.display='block'; if(this.nextElementSibling) this.nextElementSibling.style.display='none';">
                            <div class="flex items-center justify-center h-full text-slate-400 text-xs font-medium">
                                <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Memuat preview...
                            </div>
                        </div>
                    </div>

                    {{-- Right Preview (Hanya tampil jika ada objek di kanan) --}}
                    <div class="bg-white rounded-xl border border-slate-200 p-4 text-center shadow-xs flex-1 min-w-[220px] max-w-[280px]" x-show="hasRightDesign" x-cloak>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-xs font-semibold text-slate-700">Lengan Kanan</h4>
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" title="Desain Siap"></span>
                        </div>
                        <div class="bg-slate-50 rounded-lg border border-slate-100 overflow-hidden mx-auto w-full h-[280px] flex items-center justify-center">
                            <img id="preview-right" src="" alt="Preview Kanan" class="w-full h-full object-contain" style="display:none;" onload="this.style.display='block'; if(this.nextElementSibling) this.nextElementSibling.style.display='none';">
                            <div class="flex items-center justify-center h-full text-slate-400 text-xs font-medium">
                                <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Memuat preview...
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Order Summary Card --}}
                <div class="bg-white rounded-xl border border-slate-200 p-6 mb-6 shadow-xs">
                    <h3 class="font-bold text-slate-900 text-sm mb-4">Rincian Pesanan</h3>
                    <div class="space-y-2.5 text-xs sm:text-sm">
                        <div class="flex justify-between items-center"><span class="text-slate-500">Produk</span><span class="font-semibold text-slate-800">{{ $produk->nama_produk }}</span></div>
                        <div class="flex justify-between items-center"><span class="text-slate-500">Warna Baju</span>
                            <span class="flex items-center gap-1.5 font-semibold text-slate-800">
                                <span class="w-3.5 h-3.5 rounded-full border border-slate-300 inline-block shadow-xs" :style="{backgroundColor: baseColor}"></span>
                                <span x-text="getColorName(baseColor)"></span>
                            </span>
                        </div>
                        <div class="flex justify-between items-center"><span class="text-slate-500">Teknik Sablon</span><span class="font-semibold text-slate-800">Sablon Kustom</span></div>
                        <hr class="border-slate-100 my-2">
                        <div class="flex justify-between items-center"><span class="text-slate-500">Harga Dasar Baju</span><span class="font-semibold text-slate-800">Rp {{ number_format($produk->harga_dasar, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between items-center"><span class="text-slate-500">Biaya Sablon Tambahan</span><span class="font-semibold biaya-desain-val text-slate-900">Rp 0</span></div>
                        
                        <!-- Rincian Biaya Sablon -->
                        <div class="mt-2 text-slate-600 bg-slate-50 border border-slate-200/80 rounded-lg p-2.5 text-xs" id="priceBreakdownContainer">
                            <!-- Populated dynamically via JS -->
                        </div>

                        <hr class="border-slate-200 my-2">
                        <div class="flex justify-between items-center text-sm sm:text-base"><span class="font-bold text-slate-900">Estimasi Total</span><span class="font-bold text-red-600 text-base sm:text-lg total-harga-val">Rp {{ number_format($produk->harga_dasar, 0, ',', '.') }}</span></div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col-reverse sm:flex-row justify-between gap-3">
                    <button @click="backToEditor()" type="button" class="flex items-center justify-center gap-2 w-full sm:w-auto bg-white border border-slate-200 text-slate-700 font-semibold py-2.5 px-5 rounded-lg hover:bg-slate-50 transition text-xs sm:text-sm shadow-xs">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Edit Desain Kembali
                    </button>
                    <button id="saveDesignBtn" type="button" class="flex items-center justify-center gap-2 w-full sm:w-auto bg-slate-900 hover:bg-slate-800 text-white font-semibold py-2.5 px-6 rounded-lg transition-colors text-xs sm:text-sm shadow-xs">
                        <span>Simpan & Masukkan ke Keranjang</span>
                        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('customer.designs._editor_styles')
    <script src="{{ asset('js/fabric.min.js') }}"></script>
    <script src="{{ asset('js/fabric-smart-guides.js') }}"></script>
    @include('customer.designs._editor_scripts')
</x-app-layout>
