<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl shadow-indigo-100/50 sm:rounded-2xl border border-slate-100 p-8">
                <h3 class="text-2xl font-bold text-slate-800 mb-4">Selamat Datang, Admin SablonQu!</h3>
                <p class="text-slate-600 mb-8">Ini adalah pusat kendali untuk mengelola pesanan masuk dan template desain untuk pelanggan.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <a href="{{ route('admin.orders.index') }}" class="block p-6 bg-indigo-50 rounded-xl border border-indigo-100 hover:bg-indigo-600 hover:text-white transition group">
                        <h4 class="text-xl font-bold text-indigo-900 group-hover:text-white mb-2">Kelola Pesanan</h4>
                        <p class="text-indigo-700 group-hover:text-indigo-100 text-sm">Lihat order masuk, verifikasi desain mockup, dan update status pengerjaan.</p>
                    </a>

                    <a href="{{ route('admin.templates.index') }}" class="block p-6 bg-slate-50 rounded-xl border border-slate-200 hover:bg-slate-800 hover:text-white transition group">
                        <h4 class="text-xl font-bold text-slate-900 group-hover:text-white mb-2">Master Template</h4>
                        <p class="text-slate-600 group-hover:text-slate-300 text-sm">Upload gambar template desain opsional untuk dipilih pelanggan saat mendesain.</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
