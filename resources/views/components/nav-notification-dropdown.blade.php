<!-- Notifications Dropdown area -->
@php
    $user = auth()->user();
    $notifications = auth()->user()->notifications()->latest()->take(5)->get();
    $unreadCount = auth()->user()->unreadNotifications()->count();
    $unreadCount = $user?->unreadNotifications()->count() ?? 0;

    @endphp
<div class="relative md:block hidden">
    <button
      class="lg:h-[32px] lg:w-[32px] lg:bg-slate-50 lg:dark:bg-slate-900 dark:text-white text-slate-900 cursor-pointer
        rounded-full text-[20px] flex flex-col items-center justify-center"
      type="button"
      data-bs-toggle="dropdown"
      aria-expanded="false">
      <iconify-icon class="animate-tada text-slate-800 dark:text-white text-xl" icon="heroicons-outline:bell"></iconify-icon>
     <span
    id="notification-count"
    class="absolute -top-1 -right-1 bg-danger-500 text-white text-[10px] px-1.5 rounded-full
    {{ $unreadCount ? '' : 'hidden' }}">
    {{ $unreadCount }}
    </span>

    </button>
    <!-- Notifications Dropdown -->
    

<div
    class="dropdown-menu z-10 hidden bg-white divide-y divide-slate-100 dark:divide-slate-900 shadow w-[335px]
    dark:bg-slate-800 border dark:border-slate-900 !top-[18px] rounded-md overflow-hidden
    lrt:origin-top-right rtl:origin-top-left"
>
    {{-- Header --}}
    <div class="flex items-center justify-between py-4 px-4">
        <h3 class="text-sm font-Inter font-medium text-slate-700 dark:text-white">
            Messages
        </h3>
        <a class="text-xs font-Inter font-normal underline text-slate-500 dark:text-white"
           href="{{ route('notifications.index') }}">
            See All
        </a>
    </div>

    {{-- List --}}
    <div
        id="notification-list"
        class="divide-y divide-slate-100 dark:divide-slate-900"
        role="none"
    >
        @forelse ($notifications as $notification)
            <div
                class="text-slate-600 dark:text-slate-300 block w-full px-4 py-2 text-sm {{ $notification->read_at ? '' : 'bg-slate-50 dark:bg-slate-700' }}">
                <div class="flex ltr:text-left rtl:text-right space-x-3 rtl:space-x-reverse relative">

                    {{-- Avatar --}}
                    <div class="flex-none">
                        <div class="h-8 w-8 bg-white dark:bg-slate-700 rounded-full relative">
                            @if(is_null($notification->read_at))
                                <span
                                    class="bg-danger-500 w-[10px] h-[10px] rounded-full border border-white
                                    dark:border-slate-700 inline-block absolute right-0 top-0"></span>
                            @endif
                            <img
                                src="/images/all-img/user.png"
                                alt="user"
                                class="block w-full h-full object-cover rounded-full border hover:border-white border-transparent">
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1">
                        <a
                            href="{{ $notification->data['url'] ?? '#' }}"
                            class="text-slate-800 dark:text-slate-300 text-sm font-medium mb-1 before:w-full
                            before:h-full before:absolute before:top-0 before:left-0">
                            {{ $notification->data['title'] ?? 'Thông báo' }}
                        </a>
                        <div class="text-xs hover:text-[#68768A] text-slate-600 dark:text-slate-300 mb-1">
                            {{ $notification->data['message'] ?? '' }}
                        </div>
                        <div class="text-slate-400 dark:text-slate-400 text-xs">
                            {{ $notification->created_at->diffForHumans() }}
                        </div>
                    </div>

                    {{-- Unread badge --}}
                    @if(is_null($notification->read_at))
                        <div class="flex-0">
                            <span
                                class="h-4 w-4 bg-danger-500 border border-white rounded-full text-[10px]
                                flex items-center justify-center text-white">
                                1
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="px-4 py-6 text-sm text-slate-500 text-center">
                Không có thông báo mới
            </div>
        @endforelse
    </div>
</div>

</div>
