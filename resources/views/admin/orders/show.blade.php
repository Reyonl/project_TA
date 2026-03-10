<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Detail Pesanan & Verifikasi Desain') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Tindakan Admin -->
            <div class="bg-white overflow-hidden shadow-xl shadow-indigo-100/50 sm:rounded-2xl border border-slate-100 p-8">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Update Status Pengerjaan</h3>
                        <p class="text-sm text-slate-500 mt-1">Status pesanan saat ini: 
                            <span class="font-bold text-indigo-600 uppercase">{{ $order->status_order }}</span>
                        </p>
                    </div>

                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="flex gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="status_order" class="rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm font-semibold text-slate-700">
                            <option value="pending" {{ $order->status_order == 'pending' ? 'selected' : '' }}>⏳ Menunggu Konfirmasi</option>
                            <option value="diproses" {{ $order->status_order == 'diproses' ? 'selected' : '' }}>🏭 Sedang Diproses/Dicetak</option>
                            <option value="dikirim" {{ $order->status_order == 'dikirim' ? 'selected' : '' }}>🚚 Dalam Pengiriman</option>
                            <option value="selesai" {{ $order->status_order == 'selesai' ? 'selected' : '' }}>✅ Selesai</option>
                            <option value="dibatalkan" {{ $order->status_order == 'dibatalkan' ? 'selected' : '' }}>❌ Dibatalkan</option>
                        </select>
                        <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg font-bold hover:bg-indigo-500 transition shadow">
                            Simpan Status
                        </button>
                    </form>
                </div>
                
                @if(session('success'))
                    <div class="mt-4 p-4 bg-green-50 text-green-700 font-semibold rounded-lg border border-green-100">
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            <!-- Detail Pesanan & Pelanggan -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Data Pemesan -->
                <div class="col-span-1 bg-white shadow-xl shadow-slate-100/50 sm:rounded-2xl border border-slate-100 p-6">
                    <h4 class="font-bold text-slate-800 border-b pb-2 mb-4">Informasi Pemesan</h4>
                    <ul class="space-y-3 text-sm text-slate-600">
                        <li><span class="font-semibold text-slate-800 block">Nama</span> {{ $order->customer->nama_customer }}</li>
                        <li><span class="font-semibold text-slate-800 block">Email</span> {{ $order->customer->email }}</li>
                        <li><span class="font-semibold text-slate-800 block">No. HP</span> {{ $order->customer->no_hp ?? '-' }}</li>
                        <li><span class="font-semibold text-slate-800 block">Alamat Pengiriman</span> 
                            {{ $order->customer->alamat ?? 'Alamat belum dilengkapi' }}
                        </li>
                    </ul>
                </div>

                <!-- Info Order -->
                <div class="col-span-2 bg-white shadow-xl shadow-slate-100/50 sm:rounded-2xl border border-slate-100 p-6">
                    <h4 class="font-bold text-slate-800 border-b pb-2 mb-4">Item Pesanan & Preview Desain</h4>
                    
                    <div class="space-y-6">
                        @foreach($order->orderDetails as $detail)
                            <div class="flex flex-col sm:flex-row gap-6 bg-slate-50 p-4 rounded-xl border border-slate-100">
                                
                                <!-- Canvas Preview (Jika ada desain) -->
                                <div class="w-full sm:w-1/3 flex-shrink-0 bg-white border border-slate-200 rounded-lg p-2 flex items-center justify-center min-h-[160px]">
                                    @if($detail->desain && $detail->desain->gambar_desain)
                                        <img src="{{ Str::startsWith($detail->desain->gambar_desain, 'data:image') ? $detail->desain->gambar_desain : Storage::url($detail->desain->gambar_desain) }}" 
                                             alt="Preview Desain" class="max-w-full max-h-40 object-contain">
                                    @else
                                        <span class="text-slate-400 text-sm">Tidak ada desain kustom</span>
                                    @endif
                                </div>

                                <!-- Detail Item -->
                                <div class="w-full sm:w-2/3 flex flex-col justify-center">
                                    <h5 class="font-black text-lg text-slate-800">{{ $detail->produk->nama_produk ?? 'Produk Custom' }}</h5>
                                    <p class="text-sm text-slate-500 mb-2">Desain: {{ $detail->desain->nama_desain ?? 'Tanpa Nama' }}</p>
                                    
                                    <div class="grid grid-cols-2 gap-4 mt-2 mb-3">
                                        <div class="bg-white p-2 rounded border border-slate-100">
                                            <p class="text-xs text-slate-400 font-bold uppercase">Harga Satuan</p>
                                            <p class="font-semibold text-slate-800">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="bg-white p-2 rounded border border-slate-100">
                                            <p class="text-xs text-slate-400 font-bold uppercase">Jumlah Qty</p>
                                            <p class="font-semibold text-slate-800">{{ $detail->jumlah }} pcs</p>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center border-t border-slate-200 pt-3 mt-auto">
                                        <span class="font-bold text-slate-600">Subtotal Item:</span>
                                        <span class="font-black text-indigo-600 text-lg">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Total Keseluruhan -->
                    <div class="mt-8 flex justify-between items-center p-4 bg-indigo-50 border border-indigo-100 rounded-xl">
                        <span class="text-lg font-bold text-indigo-900">Total Tagihan Pesanan</span>
                        <span class="text-2xl font-black text-indigo-700">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                    </div>

                </div>
            </div>

            <div class="text-center pt-4">
                <a href="{{ route('admin.orders.index') }}" class="text-slate-500 hover:text-slate-800 font-semibold underline decoration-2 underline-offset-4">
                    &larr; Kembali ke Daftar Pesanan
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
