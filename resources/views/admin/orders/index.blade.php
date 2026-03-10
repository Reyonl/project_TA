<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Kelola Pesanan Pelanggan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl shadow-indigo-100/50 sm:rounded-2xl border border-slate-100 p-8">
                
                <h3 class="text-xl font-bold text-slate-800 mb-6">Daftar Semua Pesanan Masuk</h3>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-lg border border-green-100">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-500 border border-slate-200 rounded-lg">
                        <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-semibold">Tgl Order</th>
                                <th scope="col" class="px-6 py-3 font-semibold">Pelanggan</th>
                                <th scope="col" class="px-6 py-3 font-semibold">Total Item</th>
                                <th scope="col" class="px-6 py-3 font-semibold text-right">Total Harga</th>
                                <th scope="col" class="px-6 py-3 font-semibold text-center">Status</th>
                                <th scope="col" class="px-6 py-3 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr class="bg-white border-b hover:bg-slate-50 transition border-slate-200">
                                    <td class="px-6 py-4 font-medium text-slate-900 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($order->tanggal_order)->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-700">{{ $order->customer->nama_customer }}</div>
                                        <div class="text-xs text-slate-400">{{ $order->customer->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $order->orderDetails->sum('jumlah') }} pcs
                                    </td>
                                    <td class="px-6 py-4 font-bold text-indigo-600 text-right">
                                        Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statusColors = [
                                                'pending'   => 'bg-yellow-100 text-yellow-700',
                                                'diproses'  => 'bg-blue-100 text-blue-700',
                                                'dikirim'   => 'bg-purple-100 text-purple-700',
                                                'selesai'   => 'bg-green-100 text-green-700',
                                                'dibatalkan'=> 'bg-red-100 text-red-700',
                                            ];
                                            $color = $statusColors[$order->status_order] ?? 'bg-slate-100 text-slate-700';
                                        @endphp
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-md uppercase tracking-wider {{ $color }}">
                                            {{ $order->status_order }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1 font-semibold text-indigo-600 hover:text-indigo-900 border border-indigo-200 px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition">
                                            <span>🔍</span> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                        Belum ada pesanan masuk.
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
