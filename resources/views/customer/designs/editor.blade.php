<x-app-layout>
    <!-- Tambahkan library Fabric.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Editor Desain Sablon') }} - {{ $produk->nama_produk }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl shadow-indigo-100/50 sm:rounded-2xl border border-slate-100 flex flex-col md:flex-row min-h-[600px]">
                
                <!-- Sidebar Tools -->
                <div class="w-full md:w-1/3 border-r border-slate-200 p-6 bg-slate-50 flex flex-col gap-6">
                    <div>
                        <h3 class="font-bold text-slate-800 mb-2">1. Upload Desain Sendiri</h3>
                        <label class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border-2 border-dashed border-slate-300 rounded-xl p-4 text-center transition-colors hover:border-indigo-400 bg-white">
                            <input type="file" id="imageLoader" accept="image/png, image/jpeg, image/svg+xml" class="hidden"/>
                            <span>Pilih Gambar (PNG/JPG/SVG)</span>
                        </label>
                    </div>

                    <div class="relative flex items-center py-2">
                        <div class="flex-grow border-t border-slate-300"></div>
                        <span class="flex-shrink-0 mx-4 text-slate-400 text-sm">ATAU</span>
                        <div class="flex-grow border-t border-slate-300"></div>
                    </div>

                    <div class="flex-1">
                        <h3 class="font-bold text-slate-800 mb-2">2. Pilih dari Template Admin</h3>
                        <div class="grid grid-cols-2 gap-3 max-h-[200px] overflow-y-auto pr-2">
                            @forelse($templates as $template)
                                <div class="bg-white p-2 border border-slate-200 rounded-lg cursor-pointer hover:border-indigo-500 hover:shadow-md transition template-item" data-url="{{ Storage::url($template->file_template) }}">
                                    <!-- Jika template kosong atau error, fallback ke placeholder -->
                                    <div class="aspect-square bg-slate-100 rounded-md mb-2 flex items-center justify-center overflow-hidden">
                                        <img src="{{ Storage::url($template->file_template) }}" alt="{{ $template->nama_template }}" class="w-full h-full object-cover">
                                    </div>
                                    <p class="text-xs text-center font-medium truncate">{{ $template->nama_template }}</p>
                                </div>
                            @empty
                                <div class="col-span-2 text-center text-sm text-slate-500 py-4">Belum ada template.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="relative flex items-center py-1">
                        <div class="flex-grow border-t border-slate-300"></div>
                        <span class="flex-shrink-0 mx-4 text-slate-400 text-sm">ATAU</span>
                        <div class="flex-grow border-t border-slate-300"></div>
                    </div>

                    <!-- Tempat Menyimpan Stiker -->
                    <div class="flex-1">
                        <h3 class="font-bold text-slate-800 mb-2 flex justify-between items-center text-sm">
                            3. Tambahkan Stiker
                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded text-[10px] uppercase font-bold tracking-wider">NEW</span>
                        </h3>
                        
                        <!-- Search Box Stiker -->
                        <div class="flex mb-3">
                            <input type="text" id="stickerSearchInput" placeholder="Cari ikon (cth: cat, fire, star)..." class="flex-1 border border-slate-300 rounded-l-lg px-3 py-1.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <button id="searchStickerBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-r-lg text-sm font-medium transition-colors">Cari</button>
                        </div>

                        <div id="stickersContainer" class="grid grid-cols-4 gap-2 max-h-[150px] overflow-y-auto pr-2">
                            <div class="col-span-4 text-center text-xs text-slate-400 py-4">Memuat stiker...</div>
                        </div>
                    </div>

                    <!-- Editor Controls (Muncul saat objek dipilih) -->
                    <div id="editorControls" class="bg-white p-4 rounded-xl border border-indigo-100 shadow-sm hidden">
                        <h4 class="text-sm font-bold text-indigo-900 mb-3 flex items-center justify-between">
                            Alat Edit
                            <button id="deleteObjBtn" class="text-red-500 hover:text-red-700 text-xs px-2 py-1 bg-red-50 rounded">Hapus</button>
                        </h4>
                        <div class="flex flex-col gap-3">
                            <label class="text-xs font-semibold text-slate-600 block">
                                Ukuran (%)
                                <input type="range" id="scaleControl" min="10" max="200" value="100" class="w-full mt-1 accent-indigo-600">
                            </label>
                        </div>
                    </div>

                    <div>
                        <button id="saveDesignBtn" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-500 transition hover:-translate-y-1">
                            Simpan & Lanjut Order
                        </button>
                    </div>
                </div>

                <!-- Canvas Area -->
                <div class="w-full md:w-2/3 p-8 flex flex-col items-center justify-center bg-slate-100 relative">
                    <p class="text-sm text-slate-500 font-medium mb-4">Area Mockup - {{ ucfirst($produk->jenis_produk) }}</p>
                    
                    <div class="relative shadow-2xl rounded-2xl overflow-hidden border border-slate-200 bg-white" id="mockupContainer" style="width: 400px; height: 500px;">
                        <!-- Background Mockup Product -->
                        <div class="absolute inset-0 flex items-center justify-center text-[15rem] leading-none opacity-20 pointer-events-none select-none">
                            @if($produk->jenis_produk == 'kaos')
                                👕
                            @elseif($produk->jenis_produk == 'hoodie')
                                🧥
                            @endif
                        </div>
                        
                        <!-- The Fabric Canvas -->
                        <canvas id="tshirt-canvas" width="400" height="500"></canvas>
                        
                        <!-- Print Area Visualizer (Garis Putus-Putus) -->
                        <div class="absolute border-2 border-dashed border-indigo-400/50 pointer-events-none rounded" style="width: 200px; height: 300px; top: 100px; left: 100px;">
                            <span class="absolute -top-6 left-0 text-xs text-indigo-400 font-semibold px-2">Area Cetak</span>
                        </div>
                    </div>

                    <p class="text-xs text-slate-400 mt-6 max-w-sm text-center">Gunakan kursor untuk menggeser, memutar, dan mengubah ukuran desain Anda di dalam area kotak putus-putus.</p>
                </div>

            </div>
        </div>
    </div>

    <!-- Script Logika Fabric.js -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi Canvas Fabric
            const canvas = new fabric.Canvas('tshirt-canvas', {
                preserveObjectStacking: true
            });

            // Referensi Elemen DOM
            const imageLoader = document.getElementById('imageLoader');
            const editorControls = document.getElementById('editorControls');
            const scaleControl = document.getElementById('scaleControl');
            const deleteObjBtn = document.getElementById('deleteObjBtn');
            const templateItems = document.querySelectorAll('.template-item');
            
            // Batasan Area Cetak (opsional: jika ingin melarang objek keluar kotak)
            const printArea = { top: 100, left: 100, width: 200, height: 300 };

            // Fungsi tambah gambar ke canvas
            function addImageToCanvas(url) {
                // Gunakan element <img> native untuk memuat gambar terlebih dahulu lalu baru ke Fabric
                const imgEl = new Image();
                imgEl.onload = function() {
                    const img = new fabric.Image(imgEl);
                    
                    // Auto-scale agar tidak terlalu besar
                    if(img.width > printArea.width) {
                        img.scaleToWidth(printArea.width - 20);
                    } else if(img.width < 40) {
                        // Jika terlalu kecil (stiker icon), scale up sedikit
                        img.scaleToWidth(80);
                    }
                    
                    img.set({
                        left: printArea.left + 10,
                        top: printArea.top + 10,
                        cornerColor: '#4f46e5',
                        cornerStrokeColor: '#4f46e5',
                        borderColor: '#4f46e5',
                        cornerSize: 12,
                        padding: 10,
                        transparentCorners: false
                    });
                    
                    canvas.add(img);
                    canvas.setActiveObject(img);
                    canvas.renderAll();
                };
                imgEl.onerror = function() {
                    // Jika gambar gagal dimuat, tampilkan pesan
                    alert('Gagal memuat stiker. Coba lagi beberapa saat.');
                };
                // Tidak perlu crossOrigin karena gambar difetch via proxy lokal
                imgEl.src = url;
            }

            // Event Listener: Upload dari komputer
            imageLoader.addEventListener('change', function(e) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    var imgObj = new Image();
                    imgObj.src = event.target.result;
                    imgObj.onload = function() {
                        var img = new fabric.Image(imgObj);
                        if(img.width > printArea.width) {
                            img.scaleToWidth(printArea.width - 20);
                        }
                        img.set({
                            left: printArea.left + 10,
                            top: printArea.top + 10,
                            cornerColor: '#4f46e5',
                            borderColor: '#4f46e5',
                            transparentCorners: false
                        });
                        canvas.add(img);
                        canvas.setActiveObject(img);
                        canvas.renderAll();
                    }
                }
                reader.readAsDataURL(e.target.files[0]);
                // Reset input file agar gambar sama bisa diupload ulang jika sudah dihapus
                e.target.value = '';
            });

            // Event Listener: Klik template dari Admin
            templateItems.forEach(item => {
                item.addEventListener('click', function() {
                    const url = this.getAttribute('data-url');
                    addImageToCanvas(url);
                });
            });

            // Fetch Online Stickers via internal API Route dengan Query
            const stickerSearchInput = document.getElementById('stickerSearchInput');
            const searchStickerBtn   = document.getElementById('searchStickerBtn');
            const stickersContainer  = document.getElementById('stickersContainer');

            // Fungsi khusus untuk menambahkan SVG (Iconify) ke canvas
            function addSVGToCanvas(url) {
                fabric.loadSVGFromURL(url, function(objects, options) {
                    if (!objects || objects.length === 0) {
                        alert('Gagal memuat stiker SVG.');
                        return;
                    }
                    const group = fabric.util.groupSVGElements(objects, options);
                    
                    // Auto-scale ke ukuran yang cocok
                    const targetSize = 80;
                    const maxDim = Math.max(group.width, group.height);
                    if (maxDim > 0) {
                        group.scale(targetSize / maxDim);
                    }
                    
                    group.set({
                        left: printArea.left + 10,
                        top:  printArea.top + 10,
                        cornerColor: '#4f46e5',
                        cornerStrokeColor: '#4f46e5',
                        borderColor: '#4f46e5',
                        cornerSize: 12,
                        padding: 10,
                        transparentCorners: false
                    });
                    
                    canvas.add(group);
                    canvas.setActiveObject(group);
                    canvas.renderAll();
                });
            }

            function loadStickers(query = 'smile') {
                stickersContainer.innerHTML = '<div class="col-span-4 text-center py-5"><svg class="animate-spin h-5 w-5 text-indigo-500 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><span class="text-xs text-slate-400">Mencari ikon...</span></div>';
                
                const url = `{{ route('customer.api.stickers') }}?q=${encodeURIComponent(query)}`;

                fetch(url)
                    .then(res => res.json())
                    .then(json => {
                        if(json.success && json.data.length > 0) {
                            stickersContainer.innerHTML = '';

                            json.data.forEach(sticker => {
                                const btn = document.createElement('div');
                                btn.className = 'relative bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-indigo-500 hover:shadow-md transition duration-200 flex flex-col items-center justify-center aspect-square overflow-hidden group';
                                btn.title = sticker.name;

                                // Badge kecil sumber stiker (Iconify / DiceBear)
                                const isIconify = sticker.source === 'Iconify';
                                const badgeColor = isIconify ? 'bg-blue-500' : 'bg-indigo-400';
                                const badgeLabel = isIconify ? 'Icon' : 'Art';

                                btn.innerHTML = `
                                    <img src="${sticker.url}" alt="${sticker.name}" loading="lazy"
                                        class="w-[70%] h-[70%] object-contain group-hover:scale-110 transition-transform duration-200">
                                    <span class="absolute bottom-1 right-1 ${badgeColor} text-white text-[8px] px-1 py-0.5 rounded font-bold leading-none">${badgeLabel}</span>
                                `;
                                
                                btn.addEventListener('click', function() {
                                    if (isIconify) {
                                        // SVG dari Iconify: gunakan loadSVGFromURL
                                        addSVGToCanvas(sticker.url);
                                    } else {
                                        // PNG dari DiceBear: gunakan addImageToCanvas biasa
                                        addImageToCanvas(sticker.url);
                                    }
                                });
                                stickersContainer.appendChild(btn);
                            });
                        } else {
                            stickersContainer.innerHTML = '<div class="col-span-4 text-center text-xs text-slate-500 py-4">Ikon tidak ditemukan. Coba kata kunci lain.</div>';
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching stickers:', err);
                        stickersContainer.innerHTML = '<div class="col-span-4 text-center text-xs text-red-400 py-4">Gagal memuat. Periksa koneksi.</div>';
                    });
            }

            // Load default awal
            loadStickers('Smile');

            // Event saat tombol diklik
            searchStickerBtn.addEventListener('click', function() {
                const q = stickerSearchInput.value.trim();
                if(q) loadStickers(q);
            });

            // Event saat tombol Enter ditekan pada form input
            stickerSearchInput.addEventListener('keypress', function(e) {
                if(e.key === 'Enter') {
                    const q = stickerSearchInput.value.trim();
                    if(q) loadStickers(q);
                }
            });

            // Mendeteksi ketika objek dipilih untuk memunculkan Tool Control
            canvas.on('selection:created', showControls);
            canvas.on('selection:updated', showControls);
            canvas.on('selection:cleared', hideControls);

            function showControls(e) {
                editorControls.classList.remove('hidden');
                // Set nilai scale awal ke slider
                const activeObj = e.selected[0];
                if(activeObj) {
                    scaleControl.value = Math.round(activeObj.scaleX * 100);
                }
            }

            function hideControls() {
                editorControls.classList.add('hidden');
            }

            // Slider Scale Event
            scaleControl.addEventListener('input', function() {
                const activeObj = canvas.getActiveObject();
                if(activeObj) {
                    const val = parseInt(this.value, 10) / 100;
                    activeObj.scaleX = val;
                    activeObj.scaleY = val;
                    canvas.renderAll();
                }
            });

            // Hapus Objek Event
            deleteObjBtn.addEventListener('click', function() {
                const activeObj = canvas.getActiveObject();
                if(activeObj) {
                    canvas.remove(activeObj);
                    hideControls();
                }
            });

            // --- FUNGSI SAVE ---
            document.getElementById('saveDesignBtn').addEventListener('click', function() {
                // Di sini Anda bisa mengirim data canvas JSON atau Blob gambar ke server via AJAX/Fetch
                const canvasDataURL = canvas.toDataURL({
                    format: 'png',
                    quality: 1
                });
                
                // Ambil ukuran canvas logic untuk disimpan ke database
                const lebarCm = Math.round((printArea.width / 400) * 30); // Estimasi 30cm untuk lebar ukuran nyata
                const tinggiCm = Math.round((printArea.height / 500) * 45);

                const payload = {
                    _token: '{{ csrf_token() }}',
                    id_produk: '{{ $produk->id_produk }}',
                    file_desain: canvasDataURL, 
                    lebar_cm: lebarCm,
                    tinggi_cm: tinggiCm
                };

                // Request POST ke Backend
                fetch('{{ route('customer.designs.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // Redirect ke checkout dengan ID desain yg baru
                        const checkoutUrl = new URL('{{ route('customer.checkout.index') }}');
                        checkoutUrl.searchParams.append('id_produk', '{{ $produk->id_produk }}');
                        checkoutUrl.searchParams.append('id_desain', data.id_desain);
                        window.location.href = checkoutUrl;
                    } else {
                        alert('Gagal menyimpan desain!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan jaringan.');
                });
            });
        });
    </script>
</x-app-layout>
