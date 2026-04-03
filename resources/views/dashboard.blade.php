<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-[0_20px_50px_rgba(220,_38,_38,_0.05)] sm:rounded-[2.5rem] border border-slate-100 p-10 relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-red-50 rounded-full blur-3xl opacity-50 group-hover:scale-150 transition-transform duration-1000"></div>
                
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                    <div>
                        <h3 class="text-3xl font-black text-slate-900 mb-2 font-outfit uppercase tracking-tight">Selamat Datang, <span class="text-red-600">{{ Auth::guard('customer')->user()->nama_customer }}</span>!</h3>
                        <p class="text-slate-500 font-medium italic">Pantau pesanan Anda dan mulai kreasi baru hari ini.</p>
                    </div>
                    <a href="{{ route('home') }}#produk" class="inline-flex items-center gap-3 px-8 py-4 bg-slate-900 text-white font-black rounded-2xl hover:bg-red-600 transition-all duration-300 shadow-xl shadow-slate-200 uppercase tracking-widest text-sm transform hover:-translate-y-1">
                        Buat Desain Baru
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
