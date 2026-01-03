<x-app-layout>
    <div class="max-w-2xl mx-auto mt-10">
        <div class="card bg-white dark:bg-slate-800 shadow-xl rounded-xl overflow-hidden">
            <div class="card-header p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <iconify-icon icon="heroicons:trophy" class="text-blue-600"></iconify-icon>
                    Thêm Mục Tiêu Mới
                </h3>
            </div>
            
            <form action="{{ route('goals.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                {{-- Title --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tên mục tiêu <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="Ví dụ: Đọc 20 cuốn sách..." class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- Color & Progress --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Màu sắc đại diện</label>
                        <select name="color" class="form-select w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                            <option value="primary">Xanh dương (Primary)</option>
                            <option value="success">Xanh lá (Success)</option>
                            <option value="danger">Đỏ (Danger)</option>
                            <option value="warning">Vàng (Warning)</option>
                            <option value="info">Xanh nhạt (Info)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tiến độ khởi đầu (%)</label>
                        <input type="number" name="progress" value="0" min="0" max="100" class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    </div>
                </div>

                {{-- Deadline --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Hạn chót (Tùy chọn)</label>
                    <input type="date" name="deadline" class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <button type="submit" class="btn bg-slate-900 text-white hover:bg-slate-800 px-6 py-2.5 rounded-lg shadow-lg shadow-slate-500/30 transition-all">
                        <iconify-icon icon="heroicons:check" class="mr-2"></iconify-icon> Lưu mục tiêu
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 px-6 py-2.5 rounded-lg transition-all">
                        Hủy bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>