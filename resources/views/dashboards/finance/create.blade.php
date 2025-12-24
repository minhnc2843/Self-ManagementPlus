<x-app-layout>
    <div class="space-y-8">
        {{-- Breadcrumb --}}
        <div>
            <x-breadcrumb :page-title="'Thêm Giao Dịch'" :breadcrumb-items="[
                ['url' => route('finance.index'), 'name' => 'Tài Chính'],
                ['url' => '#', 'name' => 'Thêm giao dịch Mới']
            ]" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- CỘT 1: FORM NHẬP LIỆU (Chiếm 2 phần) --}}
            <div class="lg:col-span-2">
                <div class="card h-full">
                    <header class="card-header border-b border-slate-100 dark:border-slate-700">
                        <h4 class="card-title text-slate-800 dark:text-slate-100">Thông Tin Giao Dịch</h4>
                    </header>

                    <div class="card-body p-6">
                        {{-- Hiển thị lỗi validate --}}
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4 rounded-md p-4 bg-danger-500 bg-opacity-20 border border-danger-500 text-danger-500">
                                <ul class="list-disc list-inside text-sm">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('finance.store') }}" method="POST" id="transactionForm" class="space-y-5">
                            @csrf

                            {{-- Hàng 1: Cơ bản --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="form-label block text-sm font-medium mb-1 text-slate-600 dark:text-slate-300">Người Tạo</label>
                                    <input type="text" value="{{ Auth::user()->name }}" disabled
                                        class="form-control w-full bg-slate-100 dark:bg-slate-700 text-slate-500 cursor-not-allowed">
                                </div>

                                <div>
                                    <label for="transaction_date" class="form-label block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Ngày Giao Dịch <span class="text-danger-500">*</span></label>
                                    <input type="date" name="transaction_date" id="transaction_date"
                                           value="{{ old('transaction_date', date('Y-m-d')) }}"
                                           required class="form-control w-full">
                                </div>
                            </div>

                            {{-- Hàng 2: Loại & Hạng mục --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="type" class="form-label block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Loại Giao Dịch <span class="text-danger-500">*</span></label>
                                    <select name="type" id="type" required class="form-control w-full">
                                        <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>🔴 Chi Tiêu (Tiền ra)</option>
                                        <option value="income" {{ old('type') === 'income' ? 'selected' : '' }}>🟢 Thu Nhập (Tiền vào)</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="category" class="form-label block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Hạng Mục / Lý Do <span class="text-danger-500">*</span></label>
                                    <input type="text" name="category" id="category"
                                           value="{{ old('category') }}"
                                           required
                                           placeholder="VD: Ăn sáng, Tiền xăng, Lương..."
                                           class="form-control w-full">
                                </div>
                            </div>

                            <hr class="border-slate-100 dark:border-slate-700 my-4">

                            {{-- Hàng 3: Số tiền & Mô tả --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="form-label block text-sm font-bold mb-1 text-indigo-600 dark:text-indigo-400 uppercase">Số Tiền (VNĐ) <span class="text-danger-500">*</span></label>
                                    <div class="relative">
                                        <input type="text"
                                               id="amount_display"
                                               value="{{ old('amount') ? number_format(old('amount'), 0, ',', '.') : '' }}"
                                               placeholder="0"
                                               class="form-control w-full text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 h-14 pl-4 pr-4 transition-all focus:ring-2 focus:ring-indigo-500"
                                               autocomplete="off">
                                        <input type="hidden" name="amount" id="amount" value="{{ old('amount') }}">
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1 italic">Nhập số tiền hoặc dùng máy tính bên cạnh.</p>
                                </div>

                                <div>
                                    <label class="form-label block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Ghi chú thêm (Tùy chọn)</label>
                                    <textarea name="description" id="description" rows="3"
                                              class="form-control w-full" placeholder="Chi tiết giao dịch...">{{ old('description') }}</textarea>
                                </div>
                            </div>

                            {{-- Footer Buttons --}}
                            <div class="flex items-center justify-end space-x-3 pt-4">
                                <a href="{{ route('finance.index') }}" class="btn btn-outline-secondary btn-sm">
                                    Hủy bỏ
                                </a>
                                <button type="submit" class="btn btn-dark btn-sm px-6 py-2 shadow-lg">
                                    <span class="flex items-center">
                                        <iconify-icon icon="heroicons:check" class="mr-1 text-lg"></iconify-icon>
                                        Lưu Giao Dịch
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- CỘT 2: MÁY TÍNH (UI Improved) --}}
            <div class="lg:col-span-1">
                <div class="card sticky top-24 shadow-xl ring-1 ring-slate-200 dark:ring-slate-700">
                    <header class="card-header bg-indigo-600 dark:bg-indigo-800 text-white rounded-t-md py-3 px-4 flex justify-between items-center">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-white">Máy Tính</h4>
                        <iconify-icon icon="heroicons:calculator" class="text-xl opacity-80"></iconify-icon>
                    </header>

                    <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-b-md" id="calcApp">
                        {{-- Màn hình hiển thị --}}
                        <div class="mb-4 relative">
                            <input
                                id="calcDisplay"
                                type="text"
                                readonly
                                placeholder="0"
                                class="w-full text-right text-3xl py-4 px-4 font-mono font-bold bg-white dark:bg-slate-900 text-slate-800 dark:text-white rounded-lg shadow-inner border border-slate-200 dark:border-slate-600 tracking-widest focus:outline-none"
                            >
                            <span class="absolute top-2 left-3 text-xs text-slate-400">LCD</span>
                        </div>

                        {{-- Bàn phím --}}
                        <div class="grid grid-cols-4 gap-3 mb-4">
                            {{-- Row 1 --}}
                            <button class="calc-ac col-span-2 btn-calc bg-rose-100 text-rose-600 hover:bg-rose-200 dark:bg-rose-500/20 dark:text-rose-400">AC</button>
                            <button class="calc-backspace btn-calc bg-amber-100 text-amber-600 hover:bg-amber-200 dark:bg-amber-500/20 dark:text-amber-400">
                                <iconify-icon icon="heroicons:backspace"></iconify-icon>
                            </button>
                            <button class="calc-op btn-calc bg-indigo-100 text-indigo-600 hover:bg-indigo-200 dark:bg-indigo-500/20 dark:text-indigo-300" data-val="/">÷</button>

                            {{-- Row 2 --}}
                            <button class="calc-btn btn-calc btn-num" data-val="7">7</button>
                            <button class="calc-btn btn-calc btn-num" data-val="8">8</button>
                            <button class="calc-btn btn-calc btn-num" data-val="9">9</button>
                            <button class="calc-op btn-calc bg-indigo-100 text-indigo-600 hover:bg-indigo-200 dark:bg-indigo-500/20 dark:text-indigo-300" data-val="*">×</button>

                            {{-- Row 3 --}}
                            <button class="calc-btn btn-calc btn-num" data-val="4">4</button>
                            <button class="calc-btn btn-calc btn-num" data-val="5">5</button>
                            <button class="calc-btn btn-calc btn-num" data-val="6">6</button>
                            <button class="calc-op btn-calc bg-indigo-100 text-indigo-600 hover:bg-indigo-200 dark:bg-indigo-500/20 dark:text-indigo-300" data-val="-">-</button>

                            {{-- Row 4 --}}
                            <button class="calc-btn btn-calc btn-num" data-val="1">1</button>
                            <button class="calc-btn btn-calc btn-num" data-val="2">2</button>
                            <button class="calc-btn btn-calc btn-num" data-val="3">3</button>
                            <button class="calc-op btn-calc bg-indigo-100 text-indigo-600 hover:bg-indigo-200 dark:bg-indigo-500/20 dark:text-indigo-300" data-val="+">+</button>

                            {{-- Row 5 --}}
                            <button class="calc-btn col-span-2 btn-calc btn-num" data-val="0">0</button>
                            <button class="calc-btn btn-calc btn-num" data-val=".">.</button>
                            <button class="calc-eq btn-calc bg-emerald-500 text-white hover:bg-emerald-600 shadow-md shadow-emerald-500/30" data-val="=">=</button>
                        </div>

                        {{-- Nút chuyển kết quả --}}
                        <button
                            id="calcUseResult"
                            class="w-full py-3 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-lg transition-all active:scale-95 flex justify-center items-center gap-2"
                        >
                            <iconify-icon icon="heroicons:arrow-left-on-rectangle"></iconify-icon>
                            Điền vào ô Số Tiền
                        </button>

                        {{-- Lịch sử --}}
                        <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-semibold text-slate-500 uppercase">Lịch sử</span>
                                <button id="clearHistory" class="text-xs text-rose-500 hover:text-rose-700 hover:underline">Xóa</button>
                            </div>
                            <div class="bg-white dark:bg-slate-900 rounded border border-slate-200 dark:border-slate-700 h-24 overflow-y-auto p-2 scrollbar-hide">
                                <ul id="calcHistory" class="space-y-1">
                                    <li class="text-xs text-slate-400 text-center italic mt-2">Chưa có phép tính nào</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CSS RIÊNG CHO MÁY TÍNH --}}
    <style>
        .btn-calc {
            @apply h-12 rounded-lg text-lg font-bold transition-all duration-150 active:scale-90 flex items-center justify-center;
        }
        .btn-num {
            @apply bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-600 border border-slate-200 dark:border-slate-600 shadow-sm;
        }
    </style>

    {{-- SCRIPT --}}
    <script type="module">
        // -------------------------------------------------------------
        // 1. Format số tiền Input
        // -------------------------------------------------------------
        const amountInput = document.getElementById("amount_display");
        const amountHidden = document.getElementById("amount");

        amountInput.addEventListener("input", function () {
            let raw = this.value.replace(/\./g, "").replace(/,/g, "").replace(/\D/g, "");
            amountHidden.value = raw;
            this.value = raw ? new Intl.NumberFormat('vi-VN').format(raw) : "";
        });

        document.getElementById("transactionForm").addEventListener("submit", function () {
            // Đảm bảo lấy giá trị raw từ hidden field hoặc clean lại display
            // (Code backend Laravel của bạn nên dùng $request->amount đã clean)
        });

        // -------------------------------------------------------------
        // 2. Logic Máy Tính
        // -------------------------------------------------------------
        const calcDisplay = document.getElementById("calcDisplay");
        const calcHistoryList = document.getElementById("calcHistory");
        let history = [];

        // Thêm số & phép toán
        document.querySelectorAll(".calc-btn, .calc-op").forEach(btn => {
            btn.addEventListener("click", () => {
                const val = btn.dataset.val;
                const currentVal = calcDisplay.value;

                // Logic chặn 2 phép toán liên tiếp
                const operators = ['+', '-', '*', '/'];
                const lastChar = currentVal.slice(-1);

                if (operators.includes(val) && operators.includes(lastChar)) {
                    // Thay thế phép toán cũ bằng phép toán mới
                    calcDisplay.value = currentVal.slice(0, -1) + val;
                } else {
                    calcDisplay.value += val;
                }
            });
        });

        // Xóa tất cả (AC)
        document.querySelector(".calc-ac").addEventListener("click", () => {
            calcDisplay.value = "";
        });

        // Xóa 1 ký tự (Backspace)
        document.querySelector(".calc-backspace").addEventListener("click", () => {
            calcDisplay.value = calcDisplay.value.slice(0, -1);
        });

        // Xóa lịch sử
        document.getElementById("clearHistory").addEventListener("click", () => {
            history = [];
            renderHistory();
        });

        // Tính toán (=)
        document.querySelector(".calc-eq").addEventListener("click", () => {
            let expression = calcDisplay.value;
            if (!expression) return;

            try {
                // Thay thế ký tự hiển thị thành ký tự toán học JS
                expression = expression.replace(/×/g, '*').replace(/÷/g, '/');

                // An toàn hơn: Chỉ cho phép số và các toán tử
                if (/[^0-9+\-*/.]/.test(expression)) {
                    throw new Error("Invalid characters");
                }

                // Tính toán
                // Lưu ý: eval() đơn giản nhất cho case này, nhưng cần try-catch
                let result = new Function('return ' + expression)();

                if (!isFinite(result) || isNaN(result)) {
                    calcDisplay.value = "Error";
                    return;
                }

                // Làm tròn nếu quá nhiều số thập phân
                if (!Number.isInteger(result)) {
                    result = parseFloat(result.toFixed(2));
                }

                // Lưu lịch sử
                const displayExpr = expression.replace(/\*/g, '×').replace(/\//g, '÷');
                const formattedResult = new Intl.NumberFormat('vi-VN').format(result);
                
                history.push(`${displayExpr} = ${formattedResult}`);
                renderHistory();

                calcDisplay.value = result; 

            } catch (e) {
                calcDisplay.value = "Error";
                setTimeout(() => calcDisplay.value = "", 1000);
            }
        });

        function renderHistory() {
            if (history.length === 0) {
                calcHistoryList.innerHTML = '<li class="text-xs text-slate-400 text-center italic mt-2">Chưa có phép tính nào</li>';
                return;
            }
            calcHistoryList.innerHTML = "";
            history.slice(-5).reverse().forEach(item => { // Chỉ lấy 5 cái gần nhất
                let li = document.createElement("li");
                li.className = "text-sm text-slate-600 dark:text-slate-300 font-mono border-b border-dashed border-slate-200 dark:border-slate-700 pb-1 last:border-0";
                li.textContent = item;
                calcHistoryList.appendChild(li);
            });
        }

        // Chuyển kết quả sang Form
        document.getElementById("calcUseResult").addEventListener("click", () => {
            let val = calcDisplay.value;
            
            if (val === "" || val === "Error") return;

            // Parse số thực
            const numVal = parseFloat(val);
            if (!isNaN(numVal)) {
                // Cập nhật Hidden Input (để submit)
                amountHidden.value = numVal; // Số nguyên/thực clean
                
                // Cập nhật Display Input (có format)
                amountInput.value = new Intl.NumberFormat('vi-VN').format(numVal);

                // Hiệu ứng Visual Feedback
                amountInput.focus();
                amountInput.parentElement.classList.add("ring-2", "ring-indigo-500");
                setTimeout(() => {
                    amountInput.parentElement.classList.remove("ring-2", "ring-indigo-500");
                }, 600);
            }
        });
    </script>
</x-app-layout>