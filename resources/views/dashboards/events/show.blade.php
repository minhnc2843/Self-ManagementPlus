<x-app-layout>
    <div class="space-y-8">

        {{-- Breadcrumb --}}
        <x-breadcrumb :page-title="$pageTitle" :breadcrumb-items="$breadcrumbItems" />

        {{-- Card --}}
        <div class="card">
            <div class="card-body p-6 space-y-8">

                {{-- Header --}}
                <div class="flex items-start justify-between gap-4">
                    <div>
                       <h2 class="flex items-center text-2xl font-semibold text-primary-700 dark:text-white">
                            <iconify-icon
                                icon="heroicons:rocket-launch"
                                class="mr-2 w-5 h-5"
                            ></iconify-icon>

                            {{ $event->title }}
                        </h2>


                        @if($event->description)
                            <p class="mt-1 text-sm text-slate-500">
                                {{ $event->description }}
                            </p>
                        @endif
                    </div>

                    @if($event->is_important)
                        <span class="inline-flex items-center gap-1 rounded-full bg-red-50 text-red-600 px-3 py-1 text-base font-medium font-semibold">
                            <iconify-icon icon="heroicons:exclamation-circle" class="w-4 h-4"></iconify-icon>
                            Quan trọng
                        </span>
                    @endif
                </div>

                {{-- Grid Info --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- CỘT 1 --}}
                    <div class="space-y-4">

                        {{-- Loại --}}
                        <div class="flex items-center gap-3">
                            <iconify-icon icon="heroicons:tag" class="w-5 h-5 text-slate-700 text-primary-600"></iconify-icon>
                            <div>
                                <p class="text-base font-medium text-slate-500">Type of event</p>
                                <p class="text-sm font-medium text-slate-700">
                                    {{ $event->event_type ?? '—' }}
                                </p>
                            </div>
                        </div>

                        {{-- Địa điểm --}}
                        <div class="flex items-center gap-3">
                            <iconify-icon icon="heroicons:map-pin" class="w-5 h-5 text-slate-700 text-primary-600"></iconify-icon>
                            <div>
                                <p class="text-base font-medium text-slate-500">Location</p>
                                <p class="text-sm font-medium text-slate-700">
                                    {{ $event->location ?? '—' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- CỘT 2 --}}
                    <div class="space-y-4">

                        {{-- Thời gian bắt đầu --}}
                        <div class="flex items-center gap-3">
                            <iconify-icon icon="heroicons:calendar-days" class="w-5 h-5 text-slate-700 text-primary-600"></iconify-icon>
                            <div>
                                <p class="text-base font-medium text-slate-500">Start time</p>
                                <p class="text-sm font-medium text-slate-700">
                                    {{ optional($event->start_time)->format('d/m/Y H:i') ?? '—' }}
                                </p>
                            </div>
                        </div>

                        {{-- Thời gian kết thúc --}}
                        <div class="flex items-center gap-3">
                            <iconify-icon icon="heroicons:calendar" class="w-5 h-5 text-slate-700 text-primary-600"></iconify-icon>
                            <div>
                                <p class="text-base font-medium text-slate-500">End time</p>
                                <p class="text-sm font-medium text-slate-700">
                                    {{ optional($event->end_time)->format('d/m/Y H:i') ?? '—' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- CỘT 3 --}}
                    <div class="space-y-4">

                        {{-- Ưu tiên --}}
                        <div class="flex items-center gap-3">
                            <iconify-icon icon="heroicons:flag" class="w-5 h-5 text-slate-700 text-primary-600"></iconify-icon>
                            <div>
                                <p class="text-base font-medium text-slate-500">Priority</p>
                                <p class="text-sm font-medium text-slate-700 capitalize">
                                    {{ $event->priority ?? '—' }}
                                </p>
                            </div>
                        </div>

                        {{-- Lặp lại --}}
                        <div class="flex items-center gap-3">
                            <iconify-icon icon="heroicons:arrow-path" class="w-5 h-5 text-slate-700 text-primary-600"></iconify-icon>
                            <div>
                                <p class="text-base font-medium text-slate-500">Repeat</p>
                                <p class="text-sm font-medium text-slate-700">
                                    {{ $event->repeat_rule ?? 'Không lặp' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Nhắc nhở --}}
                <div>
                    <h4 class="text-base text-slate-900">
                        <iconify-icon icon="heroicons:bell" class="w-4 h-4"></iconify-icon>
                       Remindful
                    </h4>

                    @if($event->reminders->count())
                        <ul class="space-y-1">
                            @foreach($event->reminders as $reminder)
                                <li class="text-base text-slate-600 flex items-center gap-2">
                                    <iconify-icon icon="heroicons:clock" class="w-4 h-4 text-slate-700 text-primary-600"></iconify-icon>
                                    {{ \Carbon\Carbon::parse($reminder->remind_at)->format('d/m/Y H:i') }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-base text-slate-500 italic">Empty message</p>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex flex-row sm:flex-row justify-end gap-3 pt-4 border-t">

                    <a href="{{ route('events.list') }}"
                    class="btn btn-outline-secondary
                            w-[45%] justify-center
                            sm:w-auto">
                        <iconify-icon icon="heroicons:arrow-left" class="w-4 h-4 mr-1"></iconify-icon>
                        Back
                    </a>

                    <a href="{{ route('events.edit', $event->id) }}"
                    class="btn btn-primary
                            w-[45%] justify-center
                            sm:w-auto">
                        <iconify-icon icon="heroicons:pencil-square" class="w-4 h-4 mr-1"></iconify-icon>
                        Edit Event
                    </a>

                </div>


            </div>
        </div>
    </div>
</x-app-layout>
