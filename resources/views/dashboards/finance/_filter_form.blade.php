<form method="GET" action="{{ route('finance.index') }}" class="mb-6 p-4 border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-800">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
        
        <div>
            <label for="type" class="form-label">Loại</label>
            <select name="type" id="type" class="form-control">
                <option value="all">Tất cả</option>
                <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Thu Nhập</option>
                <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Chi Tiêu</option>
            </select>
        </div>
        
        <div class="col-span-1 md:col-span-2">
            <label for="search" class="form-label">Tìm kiếm (Hạng mục/Ghi chú)</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Ví dụ: Ăn uống, Tiền lương..." class="form-control">
        </div>

        <div>
            <label for="start_date" class="form-label">Từ ngày</label>
            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="form-control">
        </div>

        <div>
            <label for="end_date" class="form-label">Đến ngày</label>
            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="form-control">
        </div>

        <div class="col-span-full md:col-span-5 flex space-x-3 rtl:space-x-reverse justify-end">
            <button type="submit" class="btn inline-flex justify-center btn-primary">
                <iconify-icon icon="heroicons:funnel" class="text-lg ltr:mr-1 rtl:ml-1"></iconify-icon>
                Lọc Dữ Liệu
            </button>
            <a href="{{ route('finance.index') }}" class="btn inline-flex justify-center btn-outline-secondary">
                <iconify-icon icon="heroicons:x-mark" class="text-lg ltr:mr-1 rtl:ml-1"></iconify-icon>
                Reset
            </a>
        </div>
    </div>
</form>