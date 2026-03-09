<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Kelola Template Desain') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl shadow-indigo-100/50 sm:rounded-2xl border border-slate-100 p-8">
                
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-slate-800">Daftar Template</h3>
                    <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-indigo-500 transition">
                        + Tambah Template
                    </button>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    @forelse($templates as $template)
                        <div class="border border-slate-200 rounded-xl p-4 text-center">
                            <img src="{{ Storage::url($template->file_template) }}" alt="Template" class="w-full h-32 object-contain bg-slate-100 rounded-lg mb-3">
                            <p class="font-semibold text-slate-800 truncate">{{ $template->nama_template }}</p>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8 text-slate-500">
                            Belum ada template yang diunggah.
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
