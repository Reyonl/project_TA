<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Kelola Data Produk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl shadow-indigo-100/50 sm:rounded-2xl border border-slate-100">
                <div class="p-6 sm:p-8">
                    
                    <div class="flex items-center justify-between flex-wrap gap-4 mb-8">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">Daftar Produk</h3>
                            <p class="text-sm text-slate-500 mt-1">Kelola master data produk (kustom maupun produk jadi).</p>
                        </div>
                        <a href="{{ route('admin.products.create') }}" class="bg-indigo-600 text-white px-5 py-2 rounded-lg font-bold hover:bg-indigo-500 transition shadow flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Produk
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-50 text-green-700 font-semibold rounded-lg border border-green-100">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-xs tracking-wider">
                                    <th class="p-4 font-bold rounded-tl-lg">Info Produk</th>
                                    <th class="p-4 font-bold">Kategori</th>
                                    <th class="p-4 font-bold">Tipe Produk</th>
                                    <th class="p-4 font-bold">Harga Dasar</th>
                                    <th class="p-4 font-bold text-right rounded-tr-lg">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @forelse ($produks as $produk)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="p-4">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-lg border border-slate-200 overflow-hidden flex items-center justify-center bg-slate-50 shrink-0">
                                                    @if($produk->gambar_produk)
                                                        <img src="{{ Storage::url($produk->gambar_produk) }}" alt="Gambar" class="w-full h-full object-cover">
                                                    @else
                                                        <span class="text-xl">👕</span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-800">{{ $produk->nama_produk }}</p>
                                                    <p class="text-xs text-slate-500">{{ Str::limit($produk->deskripsi, 40) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded-md uppercase border border-slate-200">
                                                {{ $produk->jenis_produk }}
                                            </span>
                                            @if($produk->tersedia_bordir)
                                            <span class="ml-1 px-2.5 py-1 bg-sky-50 text-sky-700 text-[10px] font-bold rounded-md uppercase border border-sky-100">
                                                + Bordir
                                            </span>
                                            @endif
                                        </td>
                                        <td class="p-4">
                                            @if($produk->tipe_produk == 'jadi')
                                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-md uppercase border border-emerald-200">Produk Jadi</span>
                                            @else
                                                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-md uppercase border border-indigo-200">Kustom</span>
                                            @endif
                                        </td>
                                        <td class="p-4 font-bold text-slate-700">
                                            Rp {{ number_format($produk->harga_dasar, 0, ',', '.') }}
                                        </td>
                                        <td class="p-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('admin.products.edit', $produk->id_produk) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </a>
                                                <form action="{{ route('admin.products.destroy', $produk->id_produk) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-8 text-center text-slate-500 font-medium border-t border-slate-100">
                                            Belum ada produk yang ditambahkan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
