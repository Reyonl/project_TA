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
        <div class="mt-5 relative" x-data="{ show: false }">
            <x-input-label for="password" value="Kata Sandi" class="font-bold text-slate-700" />

            <div class="relative">
                <input id="password" class="block mt-1 w-full border-slate-200 focus:border-red-500 focus:ring-red-500 rounded-xl pr-10 shadow-sm"
                                x-bind:type="show ? 'text' : 'password'"
                                name="password"
                                required autocomplete="current-password" />
                                
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.978 9.978 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>

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