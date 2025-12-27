<x-app-layout>
    <div class="max-w-2xl mx-auto mt-10">
        <div class="card bg-white dark:bg-slate-800 shadow-xl border-t-4 border-green-500">
            <div class="card-header p-6 border-b border-slate-100">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">📅 Thêm Việc Cần Làm</h3>
            </div>
            
            <div class="card-body p-6">
                <form action="{{ route('plans.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nội dung công việc</label>
                        <input type="text" name="title" required placeholder="Nhập việc cần làm..." 
                               class="form-input w-full rounded-lg border-slate-300 p-3 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Thời gian thực hiện</label>
                        <input type="datetime-local" name="start_time" required 
                               class="form-input w-full rounded-lg border-slate-300 p-3 shadow-sm">
                    </div>

                    <div class="flex items-center p-4 bg-red-50 rounded-lg border border-red-100">
                        <input id="priority_check" name="is_priority" type="checkbox" value="1" 
                               class="h-5 w-5 text-red-600 focus:ring-red-500 border-gray-300 rounded cursor-pointer">
                        <label for="priority_check" class="ml-3 text-sm font-bold text-red-700 cursor-pointer select-none">
                            Đánh dấu là KHẨN CẤP / ƯU TIÊN (sẽ hiện lên Top List)
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-light">Quay lại</a>
                        <button type="submit" class="btn btn-dark min-w-[120px]">Thêm vào lịch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>