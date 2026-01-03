<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Self-Management') }}</title>
    <x-favicon/>
    {{-- Scripts --}}
    @vite(['resources/css/app.scss', 'resources/js/custom/store.js'])
</head>
<body>

    <div class="min-h-screen w-full flex bg-white dark:bg-slate-900 overflow-hidden">

        <div class="hidden lg:flex w-1/2 flex-col justify-between bg-slate-100 dark:bg-slate-950 relative z-[1] p-10 2xl:p-20">
            
            <div class="relative z-10">
                <div class="mb-6">
                    <x-application-logo />
                </div>               
            </div>

            <div class="absolute inset-0 z-0 flex items-center justify-center overflow-hidden">
                <img class="w-4/5 h-auto object-contain transition-transform duration-500 hover:scale-105" 
                     src="{{ getSettings('guest_background') }}" 
                     alt="Background Illustration">
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center bg-white dark:bg-slate-800 relative px-6 py-10 sm:px-10">
            
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>

            <div class="mt-8 text-center text-sm text-slate-500 dark:text-slate-400">
                {{ __('Copyright') }}
                <script>
                    document.write(new Date().getFullYear())
                </script>
                , <a href="#" class="hover:text-slate-800 dark:hover:text-white transition-colors">{{ __('Self-ManagementPlus') }}</a>
                {{ __('All Rights Reserved.') }}
            </div>
        </div>

    </div>

    @vite(['resources/js/app.js'])
</body>
</html>