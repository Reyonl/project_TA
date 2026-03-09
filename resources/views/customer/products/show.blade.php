<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Detail Produk') }} - {{ $produk->nama_produk }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl shadow-indigo-100/50 sm:rounded-2xl border border-slate-100">
                <div class="p-8 md:p-12 lg:flex lg:items-center lg:justify-between gap-12">
                    
                    <div class="lg:w-1/2 flex justify-center mb-8 lg:mb-0">
                        <div class="w-full max-w-md aspect-square bg-slate-100 rounded-3xl flex items-center justify-center p-8 border-4 border-dashed border-slate-200">
                             @if($produk->jenis_produk == 'kaos')
                                <div class="text-9xl">👕</div>
                            @elseif($produk->jenis_produk == 'hoodie')
                                <div class="text-9xl">🧥</div>
                            @endif
                        </div>
                    </div>

                    <div class="lg:w-1/2">
                        <div class="inline-block bg-indigo-100 text-indigo-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide mb-4">
                            {{ $produk->jenis_produk }}
                        </div>
                        <h1 class="text-3xl font-extrabold text-slate-900 mb-4">{{ $produk->nama_produk }}</h1>
                        <p class="text-lg text-slate-600 mb-8">{{ $produk->deskripsi }}</p>

                        <div class="bg-slate-50 p-6 rounded-xl border border-slate-200 mb-8">
                            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-2">Harga Dasar Polosan</h3>
                            <div class="text-4xl font-black text-indigo-600">Rp {{ number_format($produk->harga_dasar, 0, ',', '.') }}</div>
                            <p class="text-sm text-slate-500 mt-2">*Harga sablon (+Rp20.000) akan ditambahkan setelah Anda mengupload desain.</p>
                        </div>

                        <div class="flex gap-4">
                            <a href="{{ route('customer.dashboard') }}" class="px-6 py-4 rounded-xl border-2 border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition">
                                Kembali
                            </a>
                            <a href="{{ route('customer.designs.editor', $produk->id_produk) }}" class="flex-1 px-6 py-4 rounded-xl bg-indigo-600 text-white font-bold text-center shadow-lg shadow-indigo-200 hover:bg-indigo-500 hover:-translate-y-1 transition transform">
                                Mulai Desain Sekarang &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
