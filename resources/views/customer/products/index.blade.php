<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Katalog Produk Sablon') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse ($produks as $produk)
                    <div class="bg-white rounded-2xl shadow-xl shadow-indigo-100/50 overflow-hidden border border-slate-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                        <div class="h-48 bg-slate-200 flex items-center justify-center p-6 relative">
                            <!-- Placeholder Image based on Product Type -->
                            @if($produk->jenis_produk == 'kaos')
                                <div class="text-6xl">👕</div>
                            @elseif($produk->jenis_produk == 'hoodie')
                                <div class="text-6xl">🧥</div>
                            @endif
                            <div class="absolute top-4 right-4 bg-indigo-100 text-indigo-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                                {{ $produk->jenis_produk }}
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-slate-900 mb-2">{{ $produk->nama_produk }}</h3>
                            <p class="text-slate-600 mb-4 h-12 overflow-hidden text-sm">{{ $produk->deskripsi }}</p>
                            
                            <div class="flex items-center justify-between mt-6">
                                <span class="text-xl font-extrabold text-indigo-600">Rp {{ number_format($produk->harga_dasar, 0, ',', '.') }}</span>
                                <a href="{{ route('customer.products.show', $produk->id_produk) }}" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white px-5 py-2.5 rounded-lg font-semibold transition-colors">
                                    Pilih & Desain
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-slate-400 mb-4 text-5xl">📦</div>
                        <h3 class="text-lg font-medium text-slate-900">Belum ada produk</h3>
                        <p class="text-slate-500 mt-1">Produk sablon akan segera ditambahkan oleh admin.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
