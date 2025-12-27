<x-app-layout>
    <div class="max-w-2xl mx-auto mt-10">
        <div class="card bg-white dark:bg-slate-800 shadow-xl">
            <div class="card-header bg-slate-900 text-white p-6 rounded-t-lg">
                <h3 class="text-xl font-bold">🎯 Thiết Lập Mục Tiêu Mới</h3>
                <p class="text-slate-300 text-sm mt-1">Hãy đặt mục tiêu rõ ràng để dễ dàng chinh phục.</p>
            </div>
            
            <div class="card-body p-6">
                <form action="{{ route('goals.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tên Mục Tiêu</label>
                        <input type="text" name="title" required placeholder="Ví dụ: Mua nhà, Học IELTS 7.0..." 
                               class="form-input w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 p-3">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tiến độ ban đầu (%)</label>
                            <input type="number" name="progress" value="0" min="0" max="100" 
                                   class="form-input w-full rounded-lg border-slate-300 p-3">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Màu sắc đại diện</label>
                            <select name="color" class="form-select w-full rounded-lg border-slate-300 p-3">
                                <option value="primary">Xanh dương (Sự nghiệp)</option>
                                <option value="success">Xanh lá (Tài chính)</option>
                                <option value="danger">Đỏ (Sức khỏe)</option>
                                <option value="warning">Vàng (Học tập)</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-100 mt-6">
                        <a href="{{ route('dashboard') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white rounded-lg border border-slate-200 hover:bg-slate-50">
                            Hủy bỏ
                        </a>
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-500/30">
                            Lưu Mục Tiêu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>