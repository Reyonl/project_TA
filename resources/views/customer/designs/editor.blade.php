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

    <div class="min-h-screen bg-slate-50" 
         x-data="{ 
             currentStep: 1,
             totalSteps: {{ $produk->jenis_produk == 'hoodie' ? 6 : 4 }},
             activeTab: 'templates', 
             baseColor: '#ffffff',
             activeSide: 'front', 
             sidebarOpen: true,
             colorMap: {
                 'Maroon': '#7f1d1d', 'Green': '#14532d', 'Grey': '#94a3b8', 
                 'Army': '#4B5320', 'Yellow': '#facc15', 'White': '#ffffff', 
                 'Navy': '#1e3a8a', 'Orange': '#f97316', 'Black': '#1e293b', 
                 'Red': '#dc2626', 'Blue': '#3b82f6', 'Mint': '#a7f3d0'
             },
             goToStep(step) {
                 this.currentStep = step;
                 if(step === 2) { this.activeSide = 'front'; if(window.switchCanvasSide) window.switchCanvasSide('front'); }
                 if(step === 3) { this.activeSide = 'back'; if(window.switchCanvasSide) window.switchCanvasSide('back'); }
                 if(this.totalSteps === 6) {
                     if(step === 4) { this.activeSide = 'left'; if(window.switchCanvasSide) window.switchCanvasSide('left'); }
                     if(step === 5) { this.activeSide = 'right'; if(window.switchCanvasSide) window.switchCanvasSide('right'); }
                 }
                 if(step === this.totalSteps && window.generatePreviews) { setTimeout(() => window.generatePreviews(), 300); }
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

        {{-- ===== PROGRESS BAR ===== --}}
        <div class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-sm">
            <div class="max-w-5xl mx-auto px-6 py-4">
                <div class="flex items-center justify-between mb-3">
                    <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-2 hover:opacity-80 transition">
                        <img src="{{ asset('images/logo-dailyco.png') }}" class="h-8 w-auto" alt="Logo">
                    </a>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Editor Desain — {{ $produk->nama_produk }}</span>
                </div>
                <div class="flex items-center gap-2">
                    @php 
                        $stepNames = $produk->jenis_produk == 'hoodie' 
                            ? ['Produk & Warna','Desain Depan','Desain Belakang','Samping Kiri','Samping Kanan','Review & Simpan']
                            : ['Produk & Warna','Desain Depan','Desain Belakang','Review & Simpan']; 
                        $stepsCount = count($stepNames);
                    @endphp
                    @for($i = 1; $i <= $stepsCount; $i++)
                    <div class="flex items-center {{ $i < $stepsCount ? 'flex-1' : 'flex-none' }}">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full text-xs font-black transition-all duration-300 shrink-0"
                             :class="currentStep >= {{ $i }} ? 'bg-red-600 text-white shadow-lg shadow-red-200' : 'bg-slate-200 text-slate-400'">{{ $i }}</div>
                        <span class="ml-2 text-xs font-bold hidden md:inline"
                              :class="currentStep >= {{ $i }} ? 'text-red-700' : 'text-slate-400'">{{ $stepNames[$i-1] }}</span>
                        @if($i < $stepsCount)
                        <div class="flex-1 h-1 mx-3 rounded-full transition-all duration-500"
                             :class="currentStep > {{ $i }} ? 'bg-red-500' : 'bg-slate-200'"></div>
                        @endif
                    </div>
                    @endfor
                </div>
            </div>
        </div>

        @if($desainRevisi)
        <div class="bg-red-50 border-b border-red-200 p-4 flex gap-3 shadow-inner">
            <svg class="w-6 h-6 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <h4 class="font-bold text-red-800">Revisi Permintaan Admin</h4>
                <p class="text-sm text-red-700">Silakan buat ulang desain Anda sesuai catatan: <strong class="italic">'{{ \App\Models\OrderDetail::where('id_desain', $desainRevisi->id_desain)->value('catatan_admin') }}'</strong></p>
            </div>
        </div>
        @endif

        {{-- ===== STEP 1: PRODUK & WARNA ===== --}}
        <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="max-w-3xl mx-auto px-6 py-12">
                <div class="text-center mb-10">
                    <h1 class="text-3xl font-black text-slate-800 mb-2">Pilih Warna Baju</h1>
                    <p class="text-slate-500">Tentukan warna dasar baju yang Anda inginkan sebelum mulai mendesain</p>
                </div>

                {{-- Product Info Card --}}
                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-8 mb-8">
                    <div class="flex items-center gap-6">
                        @if($produk->gambar_produk)
                        <img src="{{ Storage::url($produk->gambar_produk) }}" class="w-28 h-28 object-cover rounded-xl border border-slate-200" alt="{{ $produk->nama_produk }}">
                        @else
                        <div class="w-28 h-28 bg-slate-100 rounded-xl flex items-center justify-center">
                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        </div>
                        @endif
                        <div>
                            <h3 class="text-xl font-black text-slate-800">{{ $produk->nama_produk }}</h3>
                            <p class="text-sm text-slate-500 capitalize">{{ $produk->jenis_produk }} — Teknik Sablon</p>
                            <p class="text-lg font-black text-red-600 mt-1">Rp {{ number_format($produk->harga_dasar, 0, ',', '.') }}</p>
                            <p class="text-xs text-slate-400 mt-1">+ <span class="biaya-desain-val font-bold text-red-600">Rp 0</span> biaya sablon kustom</p>
                        </div>
                    </div>
                </div>

                {{-- Color Picker --}}
                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-8 mb-8">
                    <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest mb-5">Pilih Warna Baju</h3>
                    <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-8 gap-4">
                        <template x-for="(hex, name) in colorMap" :key="name">
                            <button @click="baseColor = hex; canvasBackgroundChange(hex)" class="group flex flex-col items-center gap-1.5" :title="name">
                                <div class="w-11 h-11 rounded-full border-2 transition-all duration-200 shadow-sm"
                                     :style="{ backgroundColor: hex }"
                                     :class="baseColor === hex ? 'ring-4 ring-red-400 ring-offset-2 border-red-500 scale-110' : 'border-slate-300 hover:scale-105'"></div>
                                <span class="text-[9px] font-bold text-slate-400 group-hover:text-slate-600 transition" x-text="name"></span>
                            </button>
                        </template>
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-sm">
                        <span class="font-bold text-slate-500">Dipilih:</span>
                        <div class="w-5 h-5 rounded-full border border-slate-300" :style="{backgroundColor: baseColor}"></div>
                        <span class="font-black text-slate-700" x-text="getColorName(baseColor)"></span>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button @click="goToStep(2)" class="flex items-center gap-2 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black py-3 px-8 rounded-xl shadow-lg shadow-red-200 hover:shadow-red-300 transition-all text-sm hover:-translate-y-0.5">
                        Lanjut ke Desain Depan
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== STEP 2 - 5: CANVAS EDITOR (SINGLE INSTANCE) ===== --}}
        <div x-show="currentStep > 1 && currentStep < totalSteps" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
            @include('customer.designs._canvas_editor')

            {{-- Navigation Buttons --}}
            <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4 pb-24 md:pb-4 flex flex-col-reverse sm:flex-row justify-between gap-4">
                <button @click="goToStep(currentStep - 1)" class="flex items-center justify-center gap-2 w-full sm:w-auto bg-white border border-slate-300 text-slate-600 font-bold py-3 px-6 rounded-xl hover:bg-slate-50 transition text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg> Kembali
                </button>
                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    {{-- Skip button for all steps except 1 and last step --}}
                    <button x-show="currentStep > 1 && currentStep < totalSteps - 1" @click="goToStep(currentStep + 1)" class="flex items-center justify-center gap-2 w-full sm:w-auto bg-slate-100 border border-slate-300 text-slate-600 font-bold py-3 px-6 rounded-xl hover:bg-slate-200 transition text-sm">
                        Skip Bagian Ini →
                    </button>
                    <button @click="goToStep(currentStep + 1)" class="flex items-center justify-center gap-2 w-full sm:w-auto bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black py-3 px-8 rounded-xl shadow-lg shadow-red-200 transition-all text-sm hover:-translate-y-0.5">
                        <span x-text="currentStep < totalSteps - 1 ? 'Lanjut ke Sisi Berikutnya' : 'Lanjut ke Review'"></span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== STEP 4/6: REVIEW & SIMPAN ===== --}}
        <div x-show="currentStep === totalSteps" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
            <div class="max-w-3xl mx-auto px-6 py-12">
                <div class="text-center mb-10">
                    <h1 class="text-3xl font-black text-slate-800 mb-2">Review Desain Anda</h1>
                    <p class="text-slate-500">Pastikan semua desain sudah benar sebelum menyimpan</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 {{ $produk->jenis_produk == 'hoodie' ? 'md:grid-cols-4' : '' }} gap-4 mb-8">
                    <div class="bg-white rounded-xl border border-slate-200 p-4 text-center shadow-sm">
                        <h4 class="text-xs font-black text-slate-500 uppercase mb-3">Depan</h4>
                        <div class="bg-slate-50 rounded-lg border border-slate-100 overflow-hidden mx-auto w-full max-w-[200px] h-[250px]">
                            <img id="preview-front" src="" alt="Preview Depan" class="w-full h-full object-contain" style="display:none;" onload="this.style.display='block'; this.nextElementSibling.style.display='none';">
                            <div class="flex items-center justify-center h-full text-slate-400 text-xs">
                                <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Memuat preview...
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 p-4 text-center shadow-sm">
                        <h4 class="text-xs font-black text-slate-500 uppercase mb-3">Belakang</h4>
                        <div class="bg-slate-50 rounded-lg border border-slate-100 overflow-hidden mx-auto w-full max-w-[200px] h-[250px]">
                            <img id="preview-back" src="" alt="Preview Belakang" class="w-full h-full object-contain" style="display:none;" onload="this.style.display='block'; this.nextElementSibling.style.display='none';">
                            <div class="flex items-center justify-center h-full text-slate-400 text-xs">
                                <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Memuat preview...
                            </div>
                        </div>
                    </div>
                    @if($produk->jenis_produk == 'hoodie')
                    <div class="bg-white rounded-xl border border-slate-200 p-4 text-center shadow-sm">
                        <h4 class="text-xs font-black text-slate-500 uppercase mb-3">Samping Kiri</h4>
                        <div class="bg-slate-50 rounded-lg border border-slate-100 overflow-hidden mx-auto w-full max-w-[200px] h-[250px]">
                            <img id="preview-left" src="" alt="Preview Kiri" class="w-full h-full object-contain" style="display:none;" onload="this.style.display='block'; this.nextElementSibling.style.display='none';">
                            <div class="flex items-center justify-center h-full text-slate-400 text-xs">
                                <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Memuat preview...
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 p-4 text-center shadow-sm">
                        <h4 class="text-xs font-black text-slate-500 uppercase mb-3">Samping Kanan</h4>
                        <div class="bg-slate-50 rounded-lg border border-slate-100 overflow-hidden mx-auto w-full max-w-[200px] h-[250px]">
                            <img id="preview-right" src="" alt="Preview Kanan" class="w-full h-full object-contain" style="display:none;" onload="this.style.display='block'; this.nextElementSibling.style.display='none';">
                            <div class="flex items-center justify-center h-full text-slate-400 text-xs">
                                <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Memuat preview...
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-8 mb-8">
                    <h3 class="font-black text-slate-800 mb-4">Ringkasan Pesanan</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-slate-500">Produk</span><span class="font-bold text-slate-800">{{ $produk->nama_produk }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Warna</span>
                            <span class="flex items-center gap-2 font-bold text-slate-800">
                                <span class="w-4 h-4 rounded-full border border-slate-300 inline-block" :style="{backgroundColor: baseColor}"></span>
                                <span x-text="getColorName(baseColor)"></span>
                            </span>
                        </div>
                        <div class="flex justify-between"><span class="text-slate-500">Teknik</span><span class="font-bold text-slate-800">Sablon</span></div>
                        <hr class="border-slate-100">
                        <div class="flex justify-between"><span class="text-slate-500">Harga Produk</span><span class="font-bold">Rp {{ number_format($produk->harga_dasar, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Biaya Sablon (Kustom)</span><span class="font-bold biaya-desain-val text-red-600">Rp 0</span></div>
                        
                        <!-- Rincian Biaya Sablon -->
                        <div class="mt-2 text-slate-700 bg-slate-50 border border-slate-200 rounded-xl p-3" id="priceBreakdownContainer">
                            <!-- Populated dynamically via JS -->
                        </div>

                        <hr class="border-slate-200">
                        <div class="flex justify-between text-base"><span class="font-black text-slate-800">Total</span><span class="font-black text-red-600 total-harga-val">Rp {{ number_format($produk->harga_dasar, 0, ',', '.') }}</span></div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-between gap-4 mt-6">
                    <button @click="goToStep(totalSteps - 1)" class="flex items-center justify-center gap-2 w-full sm:w-auto bg-white border border-slate-300 text-slate-600 font-bold py-3 px-6 rounded-xl hover:bg-slate-50 transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg> Kembali
                    </button>
                    <button id="saveDesignBtn" class="flex items-center justify-center gap-2 w-full sm:w-auto bg-gradient-to-r from-green-600 to-emerald-500 hover:from-green-700 hover:to-emerald-600 text-white font-black py-3 px-8 rounded-xl shadow-lg shadow-green-200 transition-all text-sm hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                        Simpan & Masukkan ke Keranjang
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('customer.designs._editor_styles')
    <script src="{{ asset('js/fabric.min.js') }}"></script>
    @include('customer.designs._editor_scripts')
</x-app-layout>
