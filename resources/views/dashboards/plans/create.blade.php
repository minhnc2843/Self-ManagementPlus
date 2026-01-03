<x-app-layout>
    <div class="max-w-2xl mx-auto mt-10">
        <div class="card bg-white dark:bg-slate-800 shadow-xl rounded-xl overflow-hidden border-t-4 border-red-500">
            <div class="card-header p-6 border-b border-slate-100 dark:border-slate-700 bg-red-50 dark:bg-slate-900/50">
                <h3 class="text-xl font-bold text-red-600 flex items-center gap-2">
                    <iconify-icon icon="heroicons:calendar-days" class="text-red-500"></iconify-icon>
                    Lên Lịch Công Việc Mới
                </h3>
            </div>
            
            <form action="{{ route('plans.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                {{-- Title --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nội dung công việc <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="Ví dụ: Họp team marketing..." class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-red-500 focus:border-red-500">
                </div>

                {{-- Time --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Thời gian bắt đầu <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="start_time" required class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                </div>

                {{-- Priority Checkbox --}}
                <div class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-700/30 rounded-lg border border-slate-200 dark:border-slate-600">
                    <div class="flex h-6 items-center">
                        <input id="is_priority" name="is_priority" type="checkbox" class="h-5 w-5 rounded border-gray-300 text-red-600 focus:ring-red-600">
                    </div>
                    <div class="text-sm leading-6">
                        <label for="is_priority" class="font-medium text-slate-900 dark:text-white">Đánh dấu là Khẩn cấp / Ưu tiên cao</label>
                        <p class="text-slate-500 dark:text-slate-400">Việc này sẽ được ghim lên đầu danh sách.</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <button type="submit" class="btn bg-red-600 text-white hover:bg-red-700 px-6 py-2.5 rounded-lg shadow-lg shadow-red-500/30 transition-all">
                        <iconify-icon icon="heroicons:plus" class="mr-2"></iconify-icon> Thêm công việc
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 px-6 py-2.5 rounded-lg transition-all">
                        Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>