<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr" class="light nav-floating">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-favicon />
    <title>{{ config('app.name', 'dashcode') }}</title>

    {{-- Scripts --}}
    @vite(['resources/css/app.scss', 'resources/js/custom/store.js'])
</head>

<body class="font-inter dashcode-app" id="body_class">
    <div class="app-wrapper">

        <!-- BEGIN: Sidebar Navigation -->
        <x-sidebar-menu />
        <!-- End: Sidebar -->

        <!-- BEGIN: Settings -->
        <x-dashboard-settings />
        <!-- End: Settings -->

        <div class="flex flex-col justify-between min-h-screen">
            <div>
                <!-- BEGIN: header -->
                <x-dashboard-header />
                <!-- BEGIN: header -->

                <div class="content-wrapper transition-all duration-150 ltr:ml-0 xl:ltr:ml-[248px] rtl:mr-0 xl:rtl:mr-[248px]" id="content_wrapper">
                    <div class="page-content">
                        <div class="transition-all duration-150 container-fluid" id="page_layout">
                            <main id="content_layout">
                                <!-- Page Content -->
                                {{ $slot }}
                            </main>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BEGIN: footer -->
            <x-dashboard-footer />
            <!-- BEGIN: footer -->

        </div>
    </div>

    @vite(['resources/js/app.js', 'resources/js/main.js'])



    @stack('scripts')
    <script>
document.addEventListener('DOMContentLoaded', () => {
    const userId = {{ auth()->id() }};

    window.Echo.private(`App.Models.User.${userId}`)
        .notification((notification) => {

            // 1️⃣ Badge tăng số
            const badge = document.getElementById('notification-count');
            if (badge) {
                badge.innerText = parseInt(badge.innerText || 0) + 1;
                badge.classList.remove('hidden');
            }

            // 2️⃣ Append vào dropdown
            const list = document.getElementById('notification-list');
            if (list) {
                list.insertAdjacentHTML('afterbegin', `
                    <a href="${notification.url}"
                       class="block px-4 py-2 hover:bg-slate-100">
                        <strong>${notification.title}</strong>
                        <div class="text-sm">${notification.message}</div>
                    </a>
                `);
            }

            // 3️⃣ Toast (optional)
            alert(notification.message);
        });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const userId = {{ auth()->id() }};
    const list = document.getElementById('notification-list');
    const badge = document.getElementById('notification-count');

    if (!window.Echo || !list) return;

    window.Echo.private(`App.Models.User.${userId}`)
        .notification((notification) => {

            // Badge +1
            if (badge) {
                const count = parseInt(badge.innerText || 0) + 1;
                badge.innerText = count;
                badge.classList.remove('hidden');
            }

            // Append notification (GIỮ NGUYÊN CSS)
            const html = `
                <div class="text-slate-600 dark:text-slate-300 block w-full px-4 py-2 text-sm bg-slate-50 dark:bg-slate-700">
                    <div class="flex ltr:text-left rtl:text-right space-x-3 rtl:space-x-reverse relative">
                        <div class="flex-none">
                            <div class="h-8 w-8 bg-white dark:bg-slate-700 rounded-full relative">
                                <span class="bg-danger-500 w-[10px] h-[10px] rounded-full border border-white
                                    dark:border-slate-700 inline-block absolute right-0 top-0"></span>
                                <img src="/images/all-img/user.png"
                                     class="block w-full h-full object-cover rounded-full border">
                            </div>
                        </div>
                        <div class="flex-1">
                            <a href="${notification.url}"
                               class="text-slate-800 dark:text-slate-300 text-sm font-medium mb-1">
                                ${notification.title}
                            </a>
                            <div class="text-xs text-slate-600 dark:text-slate-300 mb-1">
                                ${notification.message}
                            </div>
                            <div class="text-slate-400 dark:text-slate-400 text-xs">
                                just now
                            </div>
                        </div>
                        <div class="flex-0">
                            <span class="h-4 w-4 bg-danger-500 border border-white rounded-full text-[10px]
                                flex items-center justify-center text-white">1</span>
                        </div>
                    </div>
                </div>
            `;

            list.insertAdjacentHTML('afterbegin', html);
        });
});
</script>

</body>

</html>