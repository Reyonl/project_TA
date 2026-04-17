<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-8">
        <h2 class="text-2xl font-black font-outfit text-slate-900 mb-1">Selamat Datang Kembali</h2>
        <p class="text-slate-500 text-sm font-medium">Masuk untuk melanjutkan proses desain baju Anda.</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email" class="font-bold text-slate-700" />
            <x-text-input id="email" class="block mt-1 w-full border-slate-200 focus:border-red-500 focus:ring-red-500 rounded-xl" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600" />
        </div>

        <!-- Password -->
        <div class="mt-5">
            <x-input-label for="password" value="Kata Sandi" class="font-bold text-slate-700" />

            <x-text-input id="password" class="block mt-1 w-full border-slate-200 focus:border-red-500 focus:ring-red-500 rounded-xl"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-5">
            <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-red-600 shadow-sm focus:ring-red-500 group-hover:border-red-400 transition" name="remember">
                <span class="ms-2 text-sm font-medium text-slate-600 group-hover:text-slate-900 transition">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between flex-wrap gap-4 mt-8">
            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-slate-500 hover:text-red-600 rounded-md focus:outline-none transition-colors" href="{{ route('password.request') }}">
                    {{ __('Lupa kata sandi?') }}
                </a>
            @endif

            <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-red-600 text-white font-black text-sm uppercase tracking-widest rounded-xl hover:bg-red-500 hover:-translate-y-0.5 active:bg-red-700 active:translate-y-0 transition-all shadow-lg shadow-red-200">
                {{ __('Masuk Sekarang') }}
            </button>
        </div>
        
        <div class="text-center mt-8 pt-6 border-t border-slate-100">
            <p class="text-sm text-slate-500 font-medium">Belum punya akun? 
                <a href="{{ route('register') }}" class="font-bold text-red-600 hover:text-red-700 transition">Daftar di sini</a>
            </p>
        </div>
    </form>
</x-guest-layout>
