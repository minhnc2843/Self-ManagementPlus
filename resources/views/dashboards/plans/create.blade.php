<x-app-layout>
    <div class="max-w-2xl mx-auto mt-12 px-4 sm:px-6">
        {{-- Card Container --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl
                    shadow-lg shadow-slate-200/60 dark:shadow-none
                    border border-slate-200 dark:border-slate-700
                    overflow-hidden">

            {{-- Header --}}
            <div class="px-8 py-6 border-b border-slate-200 dark:border-slate-700
                        bg-red-50 dark:bg-slate-800/60">
                <div class="flex items-center gap-4">
                    <div class="h-11 w-11 rounded-xl
                                bg-red-100 text-red-600
                                dark:bg-red-900/30 dark:text-red-400
                                flex items-center justify-center
                                border border-red-200 dark:border-red-800">
                        <iconify-icon icon="heroicons:calendar-days" class="text-xl"></iconify-icon>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-red-600 dark:text-red-400 leading-tight">
                            Lên Lịch Công Việc Mới
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Tạo lịch làm việc và ưu tiên nhiệm vụ
                        </p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form action="{{ route('plans.store') }}" method="POST" class="p-8">
                @csrf

                <div class="space-y-7">

                    {{-- Title --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Nội dung công việc <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="title"
                            required
                            placeholder="Ví dụ: Họp team marketing..."
                            class="w-full px-4 py-3 rounded-xl
                                   bg-white dark:bg-slate-800
                                   border border-slate-300 dark:border-slate-600
                                   text-slate-800 dark:text-slate-100
                                   placeholder-slate-400
                                   focus:border-red-500 focus:ring-4 focus:ring-red-500/20
                                   transition-all duration-200"
                        >
                    </div>

                    {{-- Time --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Thời gian bắt đầu <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="datetime-local"
                            name="start_time"
                            required
                            class="w-full px-4 py-3 rounded-xl
                                   bg-white dark:bg-slate-800
                                   border border-slate-300 dark:border-slate-600
                                   text-slate-800 dark:text-slate-100
                                   focus:border-red-500 focus:ring-4 focus:ring-red-500/20
                                   transition-all duration-200"
                        >
                    </div>

                    {{-- Priority --}}
                    <div class="flex items-start gap-4 p-5
                                bg-slate-50 dark:bg-slate-700/30
                                rounded-xl border border-slate-200 dark:border-slate-600">
                        <div class="flex h-6 items-center pt-0.5">
                            <input
                                id="is_priority"
                                name="is_priority"
                                type="checkbox"
                                class="h-5 w-5 rounded
                                       border-slate-300 dark:border-slate-500
                                       text-red-600 focus:ring-red-600"
                            >
                        </div>

                        <div class="text-sm">
                            <label for="is_priority" class="font-semibold text-slate-800 dark:text-white">
                                Đánh dấu là Khẩn cấp / Ưu tiên cao
                            </label>
                            <p class="mt-1 text-slate-500 dark:text-slate-400 text-xs leading-relaxed">
                                Công việc này sẽ được ghim lên đầu danh sách để bạn xử lý sớm.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-8 mt-8
                            border-t border-slate-200 dark:border-slate-700">
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2
                               px-6 py-2.5 rounded-xl text-sm font-semibold
                               text-white bg-red-600
                               hover:bg-red-700 hover:shadow-lg hover:shadow-red-600/25
                               focus:ring-4 focus:ring-red-600/30
                               transition-all">
                         <iconify-icon icon="heroicons:check" class="text-lg"></iconify-icon>
                        Thêm công việc
                    </button>

                    <a href="{{ route('dashboard') }}"
                       class="px-5 py-2.5 rounded-xl text-sm font-medium
                              text-slate-600 dark:text-slate-300
                              bg-white dark:bg-slate-800
                              border border-slate-300 dark:border-slate-600
                              hover:bg-slate-50 dark:hover:bg-slate-700
                              transition-all focus:ring-4 focus:ring-slate-300/30">
                       <iconify-icon icon="heroicons:arrow-down-left"></iconify-icon>  Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
