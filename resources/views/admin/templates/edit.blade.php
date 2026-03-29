<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Ubah Template Desain') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl shadow-indigo-100/50 sm:rounded-2xl border border-slate-100 p-8">
                
                <div class="mb-6 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-slate-800">Edit Data Template</h3>
                    <a href="{{ route('admin.templates.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-800">
                        &larr; Kembali
                    </a>
                </div>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 rounded-lg border border-red-100">
                        <ul class="list-disc list-inside text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.templates.update', $template) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4 text-center">
                        <p class="text-xs text-slate-500 font-bold uppercase mb-2">Gambar Saat Ini:</p>
                        <img src="{{ Storage::url($template->file_template) }}" alt="Template" class="w-full h-40 object-contain bg-slate-50 border border-slate-200 rounded-lg mx-auto">
                    </div>

                    <div class="mb-4">
                        <label for="nama_template" class="block text-sm font-medium text-slate-700 mb-1">Nama Template</label>
                        <input type="text" name="nama_template" id="nama_template" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" value="{{ old('nama_template', $template->nama_template) }}" required>
                    </div>

                    <div class="mb-4">
                        <label for="kategori" class="block text-sm font-medium text-slate-700 mb-1">Kategori (Baju, Hoodie, Topi, dsb)</label>
                        <input type="text" name="kategori" id="kategori" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" value="{{ old('kategori', $template->kategori) }}" required>
                    </div>

                    <div class="mb-6">
                        <label for="file_template" class="block text-sm font-medium text-slate-700 mb-1">Ganti File Gambar (Biarkan kosong jika tidak diubah)</label>
                        <input type="file" name="file_template" id="file_template" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-200 rounded-lg cursor-pointer" accept="image/*">
                        <p class="mt-1 text-xs text-slate-500">Gunakan format gambar dengan latar belakang transparan (PNG/SVG) untuk hasil terbaik.</p>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-500 transition shadow hover:shadow-lg">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
