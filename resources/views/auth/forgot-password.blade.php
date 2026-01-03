<x-guest-layout>
    <div class="auth-box h-full flex flex-col justify-center items-center px-4">

        {{-- Mobile Logo --}}
        <div class="lg:hidden mb-8 flex items-center gap-3">
            <x-application-logo />
            <span class="text-xl font-bold text-slate-900 dark:text-white">
                Self-MPlus
            </span>
        </div>

        <div class="w-full sm:w-[480px] bg-white dark:bg-slate-800
                    rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700
                    p-6 sm:p-8">

            {{-- Header --}}
            <div class="text-center mb-6">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center
                            rounded-full bg-blue-50 text-blue-600">
                    <!-- mail icon -->
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21.75 7.5v9a2.25 2.25 0 01-2.25 2.25h-15
                                 A2.25 2.25 0 012.25 16.5v-9
                                 m19.5 0A2.25 2.25 0 0019.5 5.25h-15
                                 A2.25 2.25 0 002.25 7.5
                                 m19.5 0l-9.75 6.75L2.25 7.5"/>
                    </svg>
                </div>

                <h4 class="text-lg font-semibold text-slate-900 dark:text-white">
                    {{ __('Forgot your password?') }}
                </h4>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    {{ __('Enter your email and we’ll send you a recovery link.') }}
                </p>
            </div>

            {{-- Session Status --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            {{-- Form --}}
            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block mb-1 text-sm font-medium text-slate-700 dark:text-slate-300">
                        {{ __('Email') }}
                    </label>

                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M21.75 7.5v9a2.25 2.25 0 01-2.25 2.25h-15
                                         A2.25 2.25 0 012.25 16.5v-9
                                         m19.5 0A2.25 2.25 0 0019.5 5.25h-15
                                         A2.25 2.25 0 002.25 7.5
                                         m19.5 0l-9.75 6.75L2.25 7.5"/>
                            </svg>
                        </span>

                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            required
                            placeholder="{{ __('Type your email') }}"
                            class="w-full rounded-xl bg-slate-50 dark:bg-slate-700
                                   pl-10 pr-4 py-2.5
                                   text-sm text-slate-700 dark:text-white
                                   border border-slate-200 dark:border-slate-600
                                   placeholder:text-slate-400
                                   focus:bg-white dark:focus:bg-slate-700
                                   focus:border-blue-500
                                   focus:ring-4 focus:ring-blue-500/20
                                   transition
                                   @error('email') border-red-500 focus:ring-red-500/20 @enderror">
                    </div>

                    <x-input-error :messages="$errors->get('email')" class="mt-1"/>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2
                               rounded-xl bg-blue-600 px-4 py-2.5
                               text-sm font-semibold text-white
                               hover:bg-blue-700
                               focus:ring-4 focus:ring-blue-500/30
                               transition">
                    {{-- icon --}}
                    <iconify-icon icon="heroicons:arrow-down-left"></iconify-icon>
                    {{ __('Send recovery email') }}
                </button>
            </form>

            {{-- Back to login --}}
            <p class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400">
                {{ __('Remember your password?') }}
                <a href="{{ route('login') }}"
                   class="font-medium text-blue-600 hover:underline">
                    {{ __('Sign In') }}
                </a>
            </p>
        </div>
    </div>
</x-guest-layout>
