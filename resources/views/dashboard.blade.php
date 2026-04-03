<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-slate-100 overflow-hidden">
                <div class="p-8 text-slate-900">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
                        <div>
                            <h3 class="text-3xl font-black font-outfit text-slate-900 mb-2">Halo, {{ Auth::user()->name }}! 👋</h3>
                            <p class="text-slate-500 font-medium italic">Selamat datang kembali di <span class="text-sky-600 font-bold">DAILY.CO</span>. Mari mulai berkreasi hari ini.</p>
                        </div>
                        <a href="{{ url('/') }}#katalog" class="bg-sky-600 text-white font-black px-8 py-4 rounded-2xl shadow-xl shadow-sky-100 hover:bg-sky-500 transition hover:-translate-y-1">
                            BUAT DESAIN BARU
                        </a>
                    </div>
</x-app-layout>
