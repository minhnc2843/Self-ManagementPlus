<x-app-layout>
    <div class="space-y-8">
        <div>
            @php
                $pageTitle = 'Thêm Giao Dịch';
                $breadcrumbItems = [
                    ['url' => route('finance.index'), 'name' => 'Tài Chính Cá Nhân'],
                    ['url' => '#', 'name' => 'Thêm Giao Dịch']
                ];
            @endphp
            <x-breadcrumb :page-title="$pageTitle" :breadcrumb-items="$breadcrumbItems" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- CỘT FORM --}}
            <div class="lg:col-span-2">
                <div class="card">
                    <header class="card-header">
                        <h4 class="card-title">Form Thêm Giao Dịch Mới</h4>
                    </header>

                    <div class="card-body px-6 pb-6">

                        @if ($errors->any())
                            <div class="alert-danger mb-4">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('finance.store') }}" method="POST" id="transactionForm" class="space-y-6">
                            @csrf

                            {{-- THÔNG TIN CƠ BẢN --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <div>
                                    <label class="form-label">Người Tạo</label>
                                    <input type="text" value="{{ Auth::user()->name }}" disabled
                                        class="form-control bg-slate-100 dark:bg-slate-700">
                                </div>

                                <div>
                                    <label for="transaction_date" class="form-label">Ngày GD (*)</label>
                                    <input type="date" name="transaction_date" id="transaction_date"
                                           value="{{ old('transaction_date', date('Y-m-d')) }}"
                                           required class="form-control">
                                </div>

                                <div>
                                    <label for="type" class="form-label">Loại GD (*)</label>
                                    <select name="type" id="type" required class="form-control">
                                        <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>Chi Tiêu</option>
                                        <option value="income" {{ old('type') === 'income' ? 'selected' : '' }}>Thu Nhập</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="form-label">Hạng Mục (*)</label>
                                    <input type="text" name="category" id="category"
                                           value="{{ old('category') }}"
                                           required
                                           placeholder="Ví dụ: Lương, Ăn uống..."
                                           class="form-control">
                                </div>
                            </div>

                            {{-- SỐ TIỀN --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t pt-6">

                                <div>
                                    <label class="form-label font-bold">Số Tiền (*)</label>
                                    <input type="text"
                                           id="amount_display"
                                           value="{{ old('amount') ? number_format(old('amount'), 0, ',', '.') : '' }}"
                                           placeholder="Ví dụ: 100000"
                                           class="form-control text-xl font-extrabold text-indigo-600 dark:text-indigo-400">

                                    <input type="hidden" name="amount" id="amount" value="{{ old('amount') }}">
                                </div>

                                <div>
                                    <label class="form-label">Ghi chú / Mô tả</label>
                                    <textarea name="description" id="description" rows="5"
                                              class="form-control">{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <div class="text-right pt-4 border-t mt-6 flex items-center justify-end space-x-3">
                                <a href="{{ route('finance.index') }}" class="btn btn-secondary h-11 flex items-center">
                                    <iconify-icon icon="heroicons:arrow-left-circle" class="text-xl mr-1"></iconify-icon>
                                    Back
                                </a>
                                <button type="submit" class="btn btn-dark h-11 flex items-center">
                                    <iconify-icon icon="heroicons:check-circle" class="text-xl mr-1"></iconify-icon>
                                    Lưu Giao Dịch
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

            {{-- CỘT MÁY TÍNH --}}
          {{-- CỘT MÁY TÍNH ĐÃ CẬP NHẬT --}}
            <div class="lg:col-span-1">
                <div class="card sticky top-24 h-fit shadow-xl border-2 border-indigo-200 dark:border-indigo-600">
                    <header class="card-header !py-3 bg-indigo-500 dark:bg-indigo-700 rounded-t-xl">
                        <h4 class="card-title text-base font-semibold text-white">
                            Máy Tính Nhanh 🔢
                        </h4>
                    </header>

                    <div class="p-4 space-y-4 bg-gray-50 dark:bg-slate-800 rounded-b-xl" id="calcApp">

                        <div>
                            <input 
                                id="calcDisplay"
                                type="text"
                                readonly
                                placeholder="0"
                                class="form-input w-full text-right text-3xl py-4 px-3 font-extrabold bg-white dark:bg-slate-900 text-indigo-700 dark:text-indigo-400 rounded-lg shadow-inner border border-indigo-300 dark:border-slate-700 focus:ring-0 focus:border-indigo-500"
                            >
                        </div>

                        <div class="grid grid-cols-4 gap-2">

                            <button class="calc-ac col-span-2 bg-red-500 hover:bg-red-600 text-white shadow-md text-xl font-bold py-3 rounded-xl transition duration-150">C</button>
                            <button class="calc-backspace bg-yellow-500 hover:bg-yellow-600 text-white shadow-md text-xl font-bold py-3 rounded-xl transition duration-150">
                                <iconify-icon icon="heroicons:backspace" class="text-xl"></iconify-icon>
                            </button>
                            <button class="calc-op bg-indigo-500 hover:bg-indigo-600 text-white shadow-md text-xl font-bold py-3 rounded-xl transition duration-150" data-val="/">÷</button>

                            <button class="calc-btn bg-gray-200 hover:bg-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-white shadow-sm text-xl font-semibold py-3 rounded-xl transition duration-150" data-val="7">7</button>
                            <button class="calc-btn bg-gray-200 hover:bg-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-white shadow-sm text-xl font-semibold py-3 rounded-xl transition duration-150" data-val="8">8</button>
                            <button class="calc-btn bg-gray-200 hover:bg-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-white shadow-sm text-xl font-semibold py-3 rounded-xl transition duration-150" data-val="9">9</button>
                            <button class="calc-op bg-indigo-500 hover:bg-indigo-600 text-white shadow-md text-xl font-bold py-3 rounded-xl transition duration-150" data-val="*">×</button>

                            <button class="calc-btn bg-gray-200 hover:bg-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-white shadow-sm text-xl font-semibold py-3 rounded-xl transition duration-150" data-val="4">4</button>
                            <button class="calc-btn bg-gray-200 hover:bg-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-white shadow-sm text-xl font-semibold py-3 rounded-xl transition duration-150" data-val="5">5</button>
                            <button class="calc-btn bg-gray-200 hover:bg-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-white shadow-sm text-xl font-semibold py-3 rounded-xl transition duration-150" data-val="6">6</button>
                            <button class="calc-op bg-indigo-500 hover:bg-indigo-600 text-white shadow-md text-xl font-bold py-3 rounded-xl transition duration-150" data-val="-">−</button>

                            <button class="calc-btn bg-gray-200 hover:bg-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-white shadow-sm text-xl font-semibold py-3 rounded-xl transition duration-150" data-val="1">1</button>
                            <button class="calc-btn bg-gray-200 hover:bg-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-white shadow-sm text-xl font-semibold py-3 rounded-xl transition duration-150" data-val="2">2</button>
                            <button class="calc-btn bg-gray-200 hover:bg-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-white shadow-sm text-xl font-semibold py-3 rounded-xl transition duration-150" data-val="3">3</button>
                            <button class="calc-op bg-indigo-500 hover:bg-indigo-600 text-white shadow-md text-xl font-bold py-3 rounded-xl transition duration-150" data-val="+">+</button>

                            <button class="calc-btn col-span-2 bg-gray-200 hover:bg-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-white shadow-sm text-xl font-semibold py-3 rounded-xl transition duration-150" data-val="0">0</button>
                            <button class="calc-btn bg-gray-200 hover:bg-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-white shadow-sm text-xl font-bold py-3 rounded-xl transition duration-150" data-val=".">.</button>
                            <button class="calc-eq bg-emerald-500 hover:bg-emerald-600 text-white shadow-md text-xl font-bold py-3 rounded-xl transition duration-150" data-val="=">=</button>
                        </div>
                        
                        <button 
                            id="calcUseResult"
                            class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold mt-2 shadow-lg transition duration-150"
                        >
                            Dùng kết quả cho số tiền
                        </button>
                        
                        <div class="border rounded-lg p-3 bg-white dark:bg-slate-700 h-32 overflow-y-auto shadow-inner" id="calcHistoryBox">
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold mb-1">Lịch sử:</p>
                            <ul id="calcHistory" class="text-sm leading-5 space-y-1 text-slate-700 dark:text-slate-300"></ul>
                        </div>

                        <button 
                            id="clearHistory"
                            class="w-full py-2 rounded-xl bg-slate-300 hover:bg-slate-400 dark:bg-slate-600 dark:hover:bg-slate-500 text-slate-800 dark:text-white text-sm font-semibold transition duration-150"
                        >
                            Xóa Lịch Sử
                        </button>
                    </div>
                </div>
            </div>
              {{-- end máy tinh   --}}
        </div>
    </div>

    {{-- SCRIPT --}}
   <script>
    // -------------------------------------------------------------
    // 1. Format số tiền theo thời gian thực
    // -------------------------------------------------------------
    const amountInput = document.getElementById("amount_display");
    const amountHidden = document.getElementById("amount");

    amountInput.addEventListener("input", function () {
        let raw = this.value.replace(/\./g, "").replace(/\D/g, "");
        amountHidden.value = raw;
        this.value = raw ? Number(raw).toLocaleString("vi-VN") : "";
    });

    document.getElementById("transactionForm").addEventListener("submit", function () {
        // Đảm bảo số tiền gửi đi là số nguyên (không có dấu chấm phân cách)
        amountHidden.value = amountHidden.value.replace(/\./g, ""); 
    });


    // -------------------------------------------------------------
    // 2. Logic Máy Tính
    // -------------------------------------------------------------
    const calcDisplay = document.getElementById("calcDisplay");
    const calcHistoryList = document.getElementById("calcHistory");
    let history = [];

    // Ấn số + phép toán
    document.querySelectorAll(".calc-btn, .calc-op").forEach(btn => {
        btn.addEventListener("click", () => {
            calcDisplay.value += btn.dataset.val;
        });
    });

    // Nút "C" (Clear All)
    document.querySelector(".calc-ac").addEventListener("click", () => {
        calcDisplay.value = "";
    });

    // Backspace (Xóa 1 ký tự) - dùng class mới
    document.querySelector(".calc-backspace").addEventListener("click", () => {
        calcDisplay.value = calcDisplay.value.slice(0, -1);
    });

    // Clear history
    document.getElementById("clearHistory").addEventListener("click", () => {
        history = [];
        renderHistory();
    });

    // Nút "="
    document.querySelector(".calc-eq").addEventListener("click", () => {
        try {
            let expr = calcDisplay.value.replace(/×/g, '*').replace(/÷/g, '/'); // Đảm bảo sử dụng toán tử JS
            let result = eval(expr);

            // Xử lý lỗi hoặc kết quả không phải là số (Infinity, NaN,...)
            if (!isFinite(result) || isNaN(result)) {
                calcDisplay.value = "Lỗi";
                return;
            }

            // Chỉ lưu vào lịch sử nếu phép tính hợp lệ
            history.push(expr.replace(/\*/g, '×').replace(/\//g, '÷') + " = " + result.toLocaleString("vi-VN")); // Format kết quả hiển thị lịch sử
            renderHistory();

            calcDisplay.value = result; // Hiển thị kết quả dưới dạng số không format
        } catch {
            calcDisplay.value = "Lỗi";
        }
    });

    // Render lịch sử
    function renderHistory() {
        calcHistoryList.innerHTML = "";
        // Giới hạn lịch sử (ví dụ: 10 mục)
        history.slice(-10).reverse().forEach(item => {
            let li = document.createElement("li");
            li.textContent = item;
            calcHistoryList.appendChild(li);
        });
    }

    // Dùng kết quả → Đổ vào form
    document.getElementById("calcUseResult").addEventListener("click", () => {
        // Lấy giá trị *số* từ màn hình, không phải string
        const result = parseFloat(calcDisplay.value);

        if (isNaN(result) || !isFinite(result)) return;

        const amountDisplay = document.getElementById("amount_display");
        const amountHidden = document.getElementById("amount");

        amountDisplay.value = result.toLocaleString("vi-VN"); // Format để hiển thị
        amountHidden.value = result; // Giá trị số thực (dạng không format)

        // Hiệu ứng nháy khi đổ số tiền thành công
        amountDisplay.classList.add("ring-2", "ring-indigo-400", "ring-opacity-75");

        setTimeout(() => {
            amountDisplay.classList.remove("ring-2", "ring-indigo-400", "ring-opacity-75");
        }, 800);
    });
   </script>

</x-app-layout>
