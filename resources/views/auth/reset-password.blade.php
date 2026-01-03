<x-guest-layout>
    <div class="auth-box h-full flex flex-col justify-center items-center px-4">

        {{-- Mobile Logo --}}
        <div class="lg:hidden mb-8 flex items-center gap-3">
            <x-application-logo />
            <span class="text-xl font-bold text-slate-900 dark:text-white">
                Self-MPlus
            </span>
        </div>

        <div class="w-full sm:w-[450px] bg-white dark:bg-slate-800
                    rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700
                    p-6 sm:p-8">

            {{-- Header --}}
            <div class="text-center mb-6">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center
                            rounded-full bg-blue-50 text-blue-600">
                    <!-- lock reset icon -->
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16.5 10.5V7.875a4.125 4.125 0 10-8.25 0V10.5
                                 m-1.875 0h12.75
                                 A1.875 1.875 0 0121 12.375v6.75
                                 A1.875 1.875 0 0119.125 21H4.875
                                 A1.875 1.875 0 013 19.125v-6.75
                                 A1.875 1.875 0 014.875 10.5z"/>
                    </svg>
                </div>

                <h4 class="text-lg font-semibold text-slate-900 dark:text-white">
                    {{ __('Reset password') }}
                </h4>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    {{ __('Create a new secure password for your account.') }}
                </p>
            </div>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf

                {{-- Token --}}
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                {{-- Email (readonly) --}}
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
                            value="{{ $request->email }}"
                            readonly
                            class="w-full rounded-xl bg-slate-100 dark:bg-slate-700
                                   pl-10 pr-4 py-2.5
                                   text-sm text-slate-600 dark:text-slate-300
                                   border border-slate-200 dark:border-slate-600
                                   cursor-not-allowed">
                    </div>

                    <x-input-error :messages="$errors->get('email')" class="mt-1"/>
                </div>

                {{-- New Password --}}
                <div>
                    <label for="password" class="block mb-1 text-sm font-medium text-slate-700 dark:text-slate-300">
                        {{ __('New Password') }}
                    </label>

                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M16.5 10.5V7.875a4.125 4.125 0 10-8.25 0V10.5
                                         m-1.875 0h12.75"/>
                            </svg>
                        </span>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            autocomplete="new-password"
                            placeholder="{{ __('New Password') }}"
                            class="w-full rounded-xl bg-slate-50 dark:bg-slate-700
                                   pl-10 pr-4 py-2.5
                                   text-sm text-slate-700 dark:text-white
                                   border border-slate-200 dark:border-slate-600
                                   placeholder:text-slate-400
                                   focus:bg-white dark:focus:bg-slate-700
                                   focus:border-blue-500
                                   focus:ring-4 focus:ring-blue-500/20
                                   transition
                                   @error('password') border-red-500 focus:ring-red-500/20 @enderror">
                    </div>

                    <x-input-error :messages="$errors->get('password')" class="mt-1"/>
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block mb-1 text-sm font-medium text-slate-700 dark:text-slate-300">
                        {{ __('Confirm Password') }}
                    </label>

                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 12.75L11.25 15 15 9.75"/>
                            </svg>
                        </span>

                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="{{ __('Confirm Password') }}"
                            class="w-full rounded-xl bg-slate-50 dark:bg-slate-700
                                   pl-10 pr-4 py-2.5
                                   text-sm text-slate-700 dark:text-white
                                   border border-slate-200 dark:border-slate-600
                                   placeholder:text-slate-400
                                   focus:bg-white dark:focus:bg-slate-700
                                   focus:border-blue-500
                                   focus:ring-4 focus:ring-blue-500/20
                                   transition
                                   @error('password_confirmation') border-red-500 focus:ring-red-500/20 @enderror">
                    </div>

                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1"/>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2
                               rounded-xl bg-blue-600 px-4 py-2.5
                               text-sm font-semibold text-white
                               hover:bg-blue-700
                               focus:ring-4 focus:ring-blue-500/30
                               transition mt-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 6v6l4 2"/>
                    </svg>
                    {{ __('Reset password') }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
