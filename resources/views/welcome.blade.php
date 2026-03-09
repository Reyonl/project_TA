<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistem Visualisasi Mockup Sablon</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 text-slate-900 font-sans antialiased selection:bg-indigo-500 selection:text-white">
        <!-- Navigation -->
        <nav class="w-full py-6 px-4 sm:px-6 lg:px-8 absolute top-0 z-50">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div class="flex items-center">
                    <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-violet-600">
                        SablonQu
                    </span>
                </div>
                <div class="flex gap-4 items-center">
                    @auth('customer')
                        <a href="{{ route('customer.dashboard') }}" class="font-semibold text-slate-600 hover:text-indigo-600 transition">Dashboard</a>
                    @else
                        @auth('admin')
                            <a href="{{ route('admin.dashboard') }}" class="font-semibold text-slate-600 hover:text-indigo-600 transition">Admin Panel</a>
                        @else
                            <a href="{{ route('login') }}" class="font-semibold text-slate-600 hover:text-indigo-600 transition">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="font-semibold bg-indigo-600 text-white px-5 py-2.5 rounded-lg hover:bg-indigo-700 transition shadow-md shadow-indigo-200">Daftar</a>
                            @endif
                        @endauth
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden flex items-center min-h-screen">
            <!-- Decorative background elements -->
            <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
                <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>

            <div class="mx-auto max-w-7xl px-6 lg:px-8 relative z-10 w-full">
                <div class="max-w-3xl mx-auto text-center">
                    <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 sm:text-6xl mb-6">
                        Desain Sablon Impianmu <br/> Pada <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-violet-600">Kaos & Hoodie</span>
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-slate-600 mb-10">
                        Tidak perlu jago desain grafis! Buat pesanan custom dengan editor mockup visual yang interaktif. Atur posisi, rotasi, warna, dan ukuran desain secara <i>realtime</i>!
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        @auth('customer')
                            <a href="{{ route('customer.dashboard') }}" class="rounded-xl bg-indigo-600 px-8 py-4 text-base font-semibold text-white shadow-xl shadow-indigo-200 hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all transform hover:-translate-y-1">
                                Lanjut Desain
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="rounded-xl bg-indigo-600 px-8 py-4 text-base font-semibold text-white shadow-xl shadow-indigo-200 hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all transform hover:-translate-y-1">
                                Mulai Buat Desain
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Feature Mockup preview image or decoration -->
                <div class="mt-20 sm:mt-24 w-full flex justify-center">
                    <div class="relative w-full max-w-4xl p-2 rounded-2xl bg-white border border-slate-200 shadow-2xl shadow-indigo-100 flex items-center justify-center bg-slate-50 min-h-[400px]">
                        <div class="text-slate-400 font-medium">✨ Realtime Interactive Canvas Editor (Segera Hadir) ✨</div>
                    </div>
                </div>
            </div>
            
            <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-40rem)]" aria-hidden="true">
                <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>
        </main>
    </body>
</html>
