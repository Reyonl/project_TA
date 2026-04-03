<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 leading-tight font-outfit uppercase tracking-tight">
            {{ __('Admin Control Panel') }}
        </h2>
    </x-slot>
 
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-[0_20px_50px_rgba(220,_38,_38,_0.05)] sm:rounded-[2.5rem] border border-slate-100 p-10 relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-red-50 rounded-full blur-3xl opacity-50 group-hover:scale-150 transition-transform duration-1000"></div>
                
                <h3 class="text-3xl font-black text-slate-900 mb-2 font-outfit uppercase tracking-tight relative z-10">Selamat Datang, Admin <span class="text-red-600">DAILY.CO</span>!</h3>
                <p class="text-slate-500 mb-10 font-medium italic relative z-10">Kelola operasional, verifikasi desain, dan pantau pertumbuhan brand Anda dari sini.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                    <a href="{{ route('admin.orders.index') }}" class="block p-8 bg-red-50 rounded-3xl border border-red-100 hover:bg-red-600 hover:text-white transition-all duration-300 transform hover:-translate-y-2 group/card shadow-sm hover:shadow-2xl hover:shadow-red-200">
                        <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-sm group-hover/card:scale-110 transition-transform">🛍️</div>
                        <h4 class="text-xl font-black text-red-900 group-hover/card:text-white mb-2 font-outfit uppercase tracking-tight">Kelola Pesanan</h4>
                        <p class="text-red-700/70 group-hover/card:text-red-100 text-sm font-medium leading-relaxed">Verifikasi pembayaran, cek mockup desain pelanggan, dan perbarui status produksi secara realtime.</p>
                    </a>
 
                    <a href="{{ route('admin.templates.index') }}" class="block p-8 bg-slate-50 rounded-3xl border border-slate-200 hover:bg-slate-900 hover:text-white transition-all duration-300 transform hover:-translate-y-2 group/card shadow-sm hover:shadow-2xl hover:shadow-slate-200">
                        <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-sm group-hover/card:scale-110 transition-transform">🎨</div>
                        <h4 class="text-xl font-black text-slate-900 group-hover/card:text-white mb-2 font-outfit uppercase tracking-tight">Master Template</h4>
                        <p class="text-slate-600 group-hover/card:text-slate-400 text-sm font-medium leading-relaxed">Kelola aset library desain untuk memudahkan pelanggan berkreasi di editor mockup Interaktif.</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
