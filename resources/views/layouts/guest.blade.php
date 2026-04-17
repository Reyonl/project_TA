<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DAILY.CO') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .font-outfit { font-family: 'Outfit', sans-serif; }
        </style>
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-50 relative overflow-hidden">
            <!-- Deco -->
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-red-100 rounded-full blur-3xl opacity-50"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-orange-50 rounded-full blur-3xl opacity-50"></div>

            <div class="relative z-10 flex flex-col items-center">
                <a href="/" class="flex items-center gap-3 mb-2 hover:scale-105 transition-transform duration-300">
                    <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center text-white font-black font-outfit text-2xl shadow-lg shadow-red-200">D</div>
                    <span class="font-outfit font-black text-3xl tracking-tighter text-slate-900">DAILY.CO</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white shadow-2xl shadow-slate-200/50 overflow-hidden sm:rounded-[2rem] border border-slate-100 relative z-10">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
