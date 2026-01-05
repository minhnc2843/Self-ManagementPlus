<div class="col-span-12 lg:col-span-7 xl:col-span-8">
    <style>
        /* Custom Range Slider Thumb with Icon */
        .range-rocket::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 28px;
            height: 28px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%234F46E5'%3E%3Cpath d='M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94.63 1.5 1.98 2.63 3.61 2.96V19H7v2h10v-2h-4v-3.1c1.63-.33 2.98-1.46 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z'/%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            cursor: pointer;
            border: none;
            margin-top: -10px; /* Căn giữa theo chiều dọc */
            transition: transform 0.2s;
            filter: drop-shadow(0px 2px 4px rgba(0,0,0,0.3));
        }
        .range-rocket::-webkit-slider-thumb:hover {
            transform: scale(1.2) rotate(45deg);
        }
    </style>
    <div class="card h-full bg-white dark:bg-slate-800 shadow-md border border-slate-200 dark:border-slate-700">

        {{-- Header --}}
        <div class="card-header p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <div>
                <h4 class="text-lg font-bold text-slate-800 dark:text-white uppercase tracking-wider">
                    🎯 Mục Tiêu Dài Hạn
                </h4>
                <p class="text-xs text-slate-500 mt-1">
                    Theo dõi tiến độ các mục tiêu lớn của bạn
                </p>
            </div>

            <a href="{{ route('goals.create') }}"
               class="btn btn-sm bg-slate-900 text-white hover:bg-slate-800 shadow-lg shadow-slate-500/30">
                <iconify-icon icon="heroicons:plus" class="mr-1"></iconify-icon>
                Thêm mới
            </a>
        </div>

        {{-- Body --}}
        <div class="card-body p-5 space-y-6">
            @forelse($yearlyGoals as $goal)

                @php
                    $progress = $goal->progress ?? 0;

                    $progressColor = match (true) {
                        $progress < 30  => 'bg-red-500',
                        $progress < 70  => 'bg-amber-500',
                        $progress < 100 => 'bg-blue-500',
                        default         => 'bg-green-500',
                    };

                    $gradientClass = match (true) {
                        $progress < 30  => 'from-red-500',
                        $progress < 70  => 'from-amber-500',
                        $progress < 100 => 'from-blue-500',
                        default         => 'from-green-500',
                    };

                    $ringColor = match (true) {
                        $progress < 30  => 'ring-red-200',
                        $progress < 70  => 'ring-amber-200',
                        $progress < 100 => 'ring-blue-200',
                        default         => 'ring-green-200',
                    };
                @endphp

                <div
                    class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl
                           border border-slate-100 dark:border-slate-700
                           hover:shadow-md transition-all">

                    {{-- Top --}}
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-11 w-11 rounded-full flex items-center justify-center
                                       {{ $progressColor }} text-white ring-4 {{ $ringColor }}">
                                <iconify-icon icon="heroicons:trophy" class="text-xl"></iconify-icon>
                            </div>

                            <div>
                                <h5 class="font-bold text-slate-700 dark:text-slate-200">
                                    {{ $goal->title }}
                                </h5>
                                <span class="text-xs text-slate-500">
                                    Deadline:
                                    {{ $goal->deadline
                                        ? \Carbon\Carbon::parse($goal->deadline)->format('d/m/Y')
                                        : 'Không thời hạn' }}
                                </span>
                            </div>
                        </div>

                        {{-- Status --}}
                        @if(!$goal->is_completed)
                            <form action="{{ route('goals.complete', $goal->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button
                                    class="text-xs px-3 py-1 rounded-md bg-green-100 text-green-700 hover:bg-green-200">
                                    ✓ Hoàn thành
                                </button>
                            </form>
                        @else
                            <span class="text-xs text-green-600 font-medium">
                                ✔ Đã hoàn thành
                            </span>
                        @endif
                    </div>

                    @if(!$goal->is_completed)
                        <form action="{{ route('goals.progress', $goal->id) }}" method="POST" class="mt-4">
                            @csrf
                            @method('PATCH')
                            <div class="flex items-center gap-3">
                                {{-- Cột 1: 2 thanh progress (Visual + Input) --}}
                                <div class="flex-1">
                                    <input type="range" name="progress" min="0" max="100" value="{{ $progress }}"
                                           class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer dark:bg-slate-700 block {{ $gradientClass }} range-rocket"
                                           style="background-image: linear-gradient(to right, var(--tw-gradient-from) 0%, var(--tw-gradient-from) {{ $progress }}%, transparent {{ $progress }}%, transparent 100%)">
                                </div>

                                {{-- Cột 2: % --}}
                                <div class="font-bold text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                    {{ $progress }}%
                                </div>

                                {{-- Cột 3: Actions --}}
                                <div class="flex items-center gap-2">
                                <a href="{{ route('goals.edit', $goal->id) }}"
                                       class="text-xs px-3 py-1.5 rounded-md border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 whitespace-nowrap">
                                    ✏️ Sửa
                                </a>
                                    <button type="submit"
                                            class="text-xs px-3 py-1.5 rounded-md bg-blue-600 text-white hover:bg-blue-500 whitespace-nowrap">
                                    🔄 Cập nhật
                                </button>
                            </div>
                            </div>
                        </form>
                    @else
                        <div class="mt-4 flex items-center gap-3">
                            <div class="flex-1 bg-slate-200 dark:bg-slate-700 rounded-full h-3 overflow-hidden">
                                <div class="bg-green-500 h-3 rounded-full" style="width: 100%"></div>
                            </div>
                            <span class="font-bold text-green-600">100%</span>
                        </div>
                    @endif

                </div>

            @empty
                <div class="text-center py-12">
                    <img src="{{ asset('images/svg/empty.svg') }}"
                         class="h-32 mx-auto opacity-50 mb-4"
                         alt="Empty">
                    <p class="text-slate-500">
                        Chưa có mục tiêu nào. Hãy đặt mục tiêu ngay!
                    </p>
                </div>
            @endforelse

            {{-- Pagination --}}
            @if($yearlyGoals->hasPages())
                <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                    {{ $yearlyGoals->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
