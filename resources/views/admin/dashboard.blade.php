<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 leading-tight font-outfit uppercase tracking-tight">
            {{ __('Admin Control Panel') }}
        </h2>
    </x-slot>
 
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Hero Section -->
            <div class="bg-white shadow-[0_20px_50px_rgba(220,_38,_38,_0.05)] sm:rounded-[2.5rem] border border-slate-100 p-10 relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-red-50 rounded-full blur-3xl opacity-50 group-hover:scale-150 transition-transform duration-1000"></div>
                
                <h3 class="text-3xl font-black text-slate-900 mb-2 font-outfit uppercase tracking-tight relative z-10">Selamat Datang, Admin <span class="text-red-600">DAILY.CO</span>!</h3>
                <p class="text-slate-500 mb-8 font-medium italic relative z-10">Kelola operasional, verifikasi desain, dan pantau pertumbuhan brand Anda dari sini.</p>
                
                <!-- Quick Navigation -->
                <div class="flex gap-4 relative z-10">
                    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg shadow-red-200 transition transform hover:-translate-y-1">
                        🛍️ Kelola Semua Pesanan
                    </a>
                    <a href="{{ route('admin.templates.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl shadow-lg shadow-slate-200 transition transform hover:-translate-y-1">
                        🎨 Master Template
                    </a>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Stat Card 1 -->
                <div class="bg-white rounded-3xl border border-slate-100 p-8 shadow-sm hover:shadow-xl hover:shadow-red-100 transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute -right-6 -bottom-6 text-8xl opacity-5 group-hover:scale-110 group-hover:opacity-10 transition-all duration-500 transform rotate-12">📦</div>
                    <div class="text-slate-500 font-bold mb-2 uppercase tracking-wider text-sm">Pesanan Baru</div>
                    <div class="text-5xl font-black text-slate-800 font-outfit">{{ $pesananBaru }}</div>
                </div>
                
                <!-- Stat Card 2 -->
                <div class="bg-white rounded-3xl border border-slate-100 p-8 shadow-sm hover:shadow-xl hover:shadow-amber-100 transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute -right-6 -bottom-6 text-8xl opacity-5 group-hover:scale-110 group-hover:opacity-10 transition-all duration-500 transform rotate-12">⚙️</div>
                    <div class="text-slate-500 font-bold mb-2 uppercase tracking-wider text-sm">Sedang Diproses</div>
                    <div class="text-5xl font-black text-slate-800 font-outfit">{{ $sedangDiproses }}</div>
                </div>

                <!-- Stat Card 3 -->
                <div class="bg-white rounded-3xl border border-slate-100 p-8 shadow-sm hover:shadow-xl hover:shadow-sky-100 transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute -right-6 -bottom-6 text-8xl opacity-5 group-hover:scale-110 group-hover:opacity-10 transition-all duration-500 transform rotate-12">👕</div>
                    <div class="text-slate-500 font-bold mb-2 uppercase tracking-wider text-sm">Total Produk</div>
                    <div class="text-5xl font-black text-slate-800 font-outfit">{{ $totalProduk }}</div>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-xl font-bold text-slate-800 font-outfit tracking-tight">Pesanan Perlu Tindakan</h3>
                    <a href="{{ route('admin.orders.index') }}" class="text-sm font-bold text-red-600 hover:text-red-800 hover:underline">Lihat Semua &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white">
                                <th class="py-4 px-8 font-bold text-slate-400 text-xs uppercase tracking-wider border-b border-slate-100">ID Pesanan</th>
                                <th class="py-4 px-8 font-bold text-slate-400 text-xs uppercase tracking-wider border-b border-slate-100">Pelanggan</th>
                                <th class="py-4 px-8 font-bold text-slate-400 text-xs uppercase tracking-wider border-b border-slate-100">Tanggal</th>
                                <th class="py-4 px-8 font-bold text-slate-400 text-xs uppercase tracking-wider border-b border-slate-100">Status</th>
                                <th class="py-4 px-8 font-bold text-slate-400 text-xs uppercase tracking-wider border-b border-slate-100 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($recentOrders as $order)
                                <tr class="hover:bg-slate-50 transition-colors group">
                                    <td class="py-4 px-8 text-sm font-bold text-slate-700">#{{ str_pad($order->id_order, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td class="py-4 px-8 text-sm font-semibold text-slate-800">{{ $order->customer->nama_customer }}</td>
                                    <td class="py-4 px-8 text-sm text-slate-500">{{ \Carbon\Carbon::parse($order->tanggal_order)->format('d M Y') }}</td>
                                    <td class="py-4 px-8">
                                        @if($order->status_order == 'pending')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                <span class="w-2 h-2 rounded-full bg-amber-500 mr-2"></span>Menunggu Pembayaran
                                            </span>
                                        @elseif($order->status_order == 'diproses')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-sky-100 text-sky-800 border border-sky-200">
                                                <span class="w-2 h-2 rounded-full bg-sky-500 mr-2"></span>Sedang Diproses
                                            </span>
                                        @elseif($order->status_order == 'selesai')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>Selesai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                                <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span>Dibatalkan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-8 text-right">
                                        <a href="{{ route('admin.orders.show', $order->id_order) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 hover:border-red-300 hover:bg-red-50 hover:text-red-600 rounded-lg text-sm font-bold text-slate-600 transition shadow-sm group-hover:shadow">
                                            Cek Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 px-8 text-center text-slate-500 font-medium">
                                        <div class="text-4xl mb-3 opacity-20">📭</div>
                                        Belum ada pesanan terbaru.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
