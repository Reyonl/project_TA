<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Ubah Data Produk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl shadow-indigo-100/50 sm:rounded-2xl border border-slate-100 p-8">
                
                <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4">
                    <h3 class="text-xl font-bold text-slate-800">Formulir Ubah Produk</h3>
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

                <form action="{{ route('admin.products.update', $product->id_produk) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nama_produk" class="block text-sm font-bold text-slate-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_produk" id="nama_produk" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition" value="{{ old('nama_produk', $product->nama_produk) }}" required>
                        </div>

                        <div>
                            <label for="harga_dasar" class="block text-sm font-bold text-slate-700 mb-1">Harga Dasar (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="harga_dasar" id="harga_dasar" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition" value="{{ old('harga_dasar', intval($product->harga_dasar)) }}" required min="0" step="1000">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <div>
                            <label for="jenis_produk" class="block text-sm font-bold text-slate-700 mb-1">Kategori Produk <span class="text-red-500">*</span></label>
                            <select name="jenis_produk" id="jenis_produk" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">
                                <option value="kaos" {{ (old('jenis_produk', $product->jenis_produk) == 'kaos') ? 'selected' : '' }}>Kaos</option>
                                <option value="hoodie" {{ (old('jenis_produk', $product->jenis_produk) == 'hoodie') ? 'selected' : '' }}>Hoodie</option>
                                {{-- <option value="topi" {{ (old('jenis_produk', $product->jenis_produk) == 'topi') ? 'selected' : '' }}>Topi</option> --}}
                                <option value="polo" {{ (old('jenis_produk', $product->jenis_produk) == 'polo') ? 'selected' : '' }}>Polo Shirt</option>
                                <option value="seragam" {{ (old('jenis_produk', $product->jenis_produk) == 'seragam') ? 'selected' : '' }}>Kemeja / Seragam</option>
                            </select>
                        </div>

                        <div>
                            <label for="tipe_produk" class="block text-sm font-bold text-slate-700 mb-1">Tipe Penjualan <span class="text-red-500">*</span></label>
                            <select name="tipe_produk" id="tipe_produk" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">
                                <option value="kustom" {{ (old('tipe_produk', $product->tipe_produk) == 'kustom') ? 'selected' : '' }}>Produk Kustom (Pelanggan Buat Desain)</option>
                                <option value="jadi" {{ (old('tipe_produk', $product->tipe_produk) == 'jadi') ? 'selected' : '' }}>Produk Jadi (Beli Langsung)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center gap-2 p-3 bg-white border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition shadow-sm">
                            <input type="checkbox" name="tersedia_bordir" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-5 h-5" {{ old('tersedia_bordir', $product->tersedia_bordir) ? 'checked' : '' }}>
                            <div>
                                <span class="block text-sm font-bold text-slate-700">Tersedia Opsi Bordir?</span>
                            </div>
                        </label>
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-sm font-bold text-slate-700 mb-1">Deskripsi Produk</label>
                        <textarea name="deskripsi" id="deskripsi" rows="3" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">{{ old('deskripsi', $product->deskripsi) }}</textarea>
                    </div>

                    <div class="bg-indigo-50/50 p-5 rounded-xl border border-indigo-100 border-dashed">
                        <label for="gambar_produk" class="block text-sm font-bold text-slate-800 mb-2">Foto / Gambar Produk</label>
                        
                        @if($product->gambar_produk)
                            <div class="mb-4">
                                <p class="text-xs text-slate-500 mb-1">Gambar saat ini:</p>
                                <img src="{{ Storage::url($product->gambar_produk) }}" class="w-32 h-32 object-cover rounded-lg border border-slate-200 shadow-sm">
                            </div>
                        @endif

                        <input type="file" name="gambar_produk" id="gambar_produk" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer transition" accept="image/*">
                        <p class="mt-2 text-xs text-slate-500 font-medium">Biarkan kosong jika tidak ingin mengubah gambar.</p>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-100">
                        <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-500 transition shadow-lg hover:shadow-indigo-200">
                            Perbarui Data Produk
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
