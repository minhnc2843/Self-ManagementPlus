<form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf

    {{-- Email --}}
    <div>
        <label for="email" class="block mb-1 text-sm font-medium text-slate-700">
            {{ __('Email') }}
        </label>

        <div class="relative">
            <!-- icon -->
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21.75 7.5v9a2.25 2.25 0 01-2.25 2.25h-15A2.25 2.25 0 012.25 16.5v-9m19.5 0A2.25 2.25 0 0019.5 5.25h-15A2.25 2.25 0 002.25 7.5m19.5 0l-9.75 6.75L2.25 7.5"/>
                </svg>
            </span>

            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email') }}"
                autofocus
                placeholder="{{ __('Type your email') }}"
                class="w-full rounded-xl bg-slate-50 pl-10 pr-4 py-2.5
                       text-sm text-slate-700
                       border border-slate-200
                       placeholder:text-slate-400
                       focus:bg-white focus:border-blue-500
                       focus:ring-4 focus:ring-blue-500/20
                       transition
                       @error('email') border-red-500 focus:ring-red-500/20 @enderror"
            >
        </div>

        <x-input-error :messages="$errors->get('email')" class="mt-1"/>
    </div>

    {{-- Password --}}
    <div>
        <label for="password" class="block mb-1 text-sm font-medium text-slate-700">
            {{ __('Password') }}
        </label>

        <div class="relative">
            <!-- icon -->
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16.5 10.5V7.875a4.125 4.125 0 10-8.25 0V10.5m-1.875 0h12.75
                             A1.875 1.875 0 0121 12.375v6.75
                             A1.875 1.875 0 0119.125 21H4.875
                             A1.875 1.875 0 013 19.125v-6.75
                             A1.875 1.875 0 014.875 10.5z"/>
                </svg>
            </span>

            <input
                type="password"
                name="password"
                id="password"
                autocomplete="current-password"
                placeholder="{{ __('Password') }}"
                class="w-full rounded-xl bg-slate-50 pl-10 pr-4 py-2.5
                       text-sm text-slate-700
                       border border-slate-200
                       placeholder:text-slate-400
                       focus:bg-white focus:border-blue-500
                       focus:ring-4 focus:ring-blue-500/20
                       transition
                       @error('password') border-red-500 focus:ring-red-500/20 @enderror"
            >
        </div>

        <x-input-error :messages="$errors->get('password')" class="mt-1"/>
    </div>

    {{-- Remember & Forgot --}}
    <div class="flex items-center justify-between">
        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm text-slate-600">{{ __('Keep me signed in') }}</span>
        </label>

        <a href="{{ route('password.request') }}"
           class="text-sm font-medium text-blue-600 hover:underline">
            {{ __('Forgot your password?') }}
        </a>
    </div>

    {{-- Submit --}}
    <button type="submit"
            class="w-full flex items-center justify-center gap-2
                   rounded-xl bg-blue-600 px-4 py-2.5
                   text-sm font-semibold text-white
                   hover:bg-blue-700
                   focus:ring-4 focus:ring-blue-500/30
                   transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-9
                     A2.25 2.25 0 002.25 5.25v13.5
                     A2.25 2.25 0 004.5 21h9
                     A2.25 2.25 0 002.25 18.75V15m12 0l4.5-4.5m0 0L12 6m4.5 4.5H9"/>
        </svg>
        {{ __('Sign In') }}
    </button>
</form>
