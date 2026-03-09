<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Laporan Penjualan (Owner)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl shadow-indigo-100/50 sm:rounded-2xl border border-slate-100 p-8">
                
                <h3 class="text-2xl font-bold text-slate-800 mb-6">Ringkasan Aktivitas Sistem SablonQu</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-indigo-50 p-6 rounded-2xl border border-indigo-100">
                        <p class="text-indigo-600 font-bold mb-2 uppercase text-xs tracking-wider">Total Pesanan</p>
                        <h4 class="text-4xl font-black text-indigo-900">{{ $totalOrders }} <span class="text-sm font-medium text-indigo-500">Order</span></h4>
                    </div>
                    
                    <div class="bg-green-50 p-6 rounded-2xl border border-green-100">
                        <p class="text-green-600 font-bold mb-2 uppercase text-xs tracking-wider">Pendapatan Bersih (Selesai)</p>
                        <h4 class="text-4xl font-black text-green-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                    </div>

                    <div class="bg-violet-50 p-6 rounded-2xl border border-violet-100">
                        <p class="text-violet-600 font-bold mb-2 uppercase text-xs tracking-wider">Total Desain Dibuat</p>
                        <h4 class="text-4xl font-black text-violet-900">{{ $totalDesains }} <span class="text-sm font-medium text-violet-500">Desain</span></h4>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
