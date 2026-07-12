<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Tambah Data Produk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl shadow-indigo-100/50 sm:rounded-2xl border border-slate-100 p-8">
                
                <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4">
                    <h3 class="text-xl font-bold text-slate-800">Formulir Tambah Produk</h3>
                    <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-800 transition">
                        &larr; Kembali
                    </a>
                </div>

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 rounded-xl border border-red-100">
                        <ul class="list-disc list-inside text-sm text-red-600 font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nama_produk" class="block text-sm font-bold text-slate-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_produk" id="nama_produk" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition" value="{{ old('nama_produk') }}" required placeholder="Contoh: Kaos Polos Premium / Kaos Anime Sablon">
                        </div>

                        <div>
                            <label for="harga_dasar" class="block text-sm font-bold text-slate-700 mb-1">Harga Dasar (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="harga_dasar" id="harga_dasar" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition" value="{{ old('harga_dasar') }}" required min="0" step="1000">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <div>
                            <label for="jenis_produk" class="block text-sm font-bold text-slate-700 mb-1">Kategori Produk <span class="text-red-500">*</span></label>
                            <select name="jenis_produk" id="jenis_produk" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">
                                <option value="kaos" {{ old('jenis_produk') == 'kaos' ? 'selected' : '' }}>Kaos</option>
                                <option value="hoodie" {{ old('jenis_produk') == 'hoodie' ? 'selected' : '' }}>Hoodie</option>
                                <option value="polo" {{ old('jenis_produk') == 'polo' ? 'selected' : '' }}>Polo Shirt</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Tipe Penjualan</label>
                            <input type="hidden" name="tipe_produk" value="jadi">
                            <div class="w-full rounded-xl border border-emerald-200 bg-emerald-50 shadow-sm px-4 py-2.5 flex items-center gap-2">
                                <div class="w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center text-white text-xs font-bold">✓</div>
                                <span class="text-sm font-bold text-emerald-700">Produk Jadi (Beli Langsung)</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center gap-2 p-3 bg-sky-50 border border-sky-200 rounded-xl shadow-sm">
                            <div class="w-5 h-5 rounded bg-sky-500 flex items-center justify-center text-white text-xs">✓</div>
                            <div>
                                <span class="block text-sm font-bold text-sky-700">Teknik Cetak: Sablon Digital (DTG)</span>
                                <span class="block text-xs text-sky-600">Semua produk menggunakan teknik sablon.</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-sm font-bold text-slate-700 mb-1">Deskripsi Produk</label>
                        <textarea name="deskripsi" id="deskripsi" rows="3" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition" placeholder="Tuliskan detail bahan, ukuran, dll.">{{ old('deskripsi') }}</textarea>
                    </div>

                    {{-- Ukuran Baju --}}
                    <div class="bg-amber-50/50 p-5 rounded-xl border border-amber-200">
                        <label class="block text-sm font-bold text-slate-800 mb-1">Ukuran Baju Tersedia <span class="text-red-500">*</span></label>
                        <p class="text-xs text-slate-500 font-medium mb-4">Centang ukuran yang tersedia untuk produk ini.</p>
                        @error('ukuran_tersedia')<p class="text-red-500 text-xs mb-2">{{ $message }}</p>@enderror
                        <div class="grid grid-cols-4 sm:grid-cols-8 gap-2">
                            @foreach(['XS','S','M','L','XL','2XL','3XL','4XL'] as $size)
                                <label class="relative cursor-pointer group">
                                    <input type="checkbox" name="ukuran_tersedia[]" value="{{ $size }}" 
                                           class="peer sr-only"
                                           {{ is_array(old('ukuran_tersedia')) && in_array($size, old('ukuran_tersedia')) ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center h-11 rounded-xl border-2 border-slate-200 bg-white text-slate-600 text-sm font-bold transition-all
                                                peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 peer-checked:ring-2 peer-checked:ring-indigo-200
                                                group-hover:border-indigo-300 group-hover:shadow-sm">
                                        {{ $size }}
                                    </div>
                                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-indigo-500 rounded-full items-center justify-center text-white text-[10px] hidden peer-checked:flex shadow">
                                        ✓
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-indigo-50/50 p-5 rounded-xl border border-indigo-100 border-dashed">
                        <label for="gambar_produk" class="block text-sm font-bold text-slate-800 mb-2">Foto / Gambar Produk</label>
                        <input type="file" name="gambar_produk" id="gambar_produk" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer transition" accept="image/*">
                        <p class="mt-2 text-xs text-slate-500 font-medium">Opsional untuk Produk Kustom. <strong class="text-indigo-600">Wajib untuk Produk Jadi</strong> agar pelanggan bisa melihat desain jadinya. (Format: JPG, PNG, maksimal 2MB)</p>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-100">
                        <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-500 transition shadow-lg hover:shadow-indigo-200">
                            Simpan Data Produk
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
