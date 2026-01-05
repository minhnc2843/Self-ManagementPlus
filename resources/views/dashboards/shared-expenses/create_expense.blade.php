<x-app-layout>
    <div class="space-y-8 max-w-6xl mx-auto px-4 sm:px-6">

        {{-- Page Header --}}
        <div class="flex justify-between items-center">
            <h4 class="font-semibold lg:text-2xl text-xl text-slate-900 dark:text-white">
                Thêm khoản chi: {{ $group->name }}
            </h4>
            <a href="{{ route('expense-groups.show', $group->id) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                      border border-slate-300 dark:border-slate-700
                      bg-white dark:bg-slate-800
                      text-slate-700 dark:text-slate-300
                      hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                <iconify-icon icon="heroicons-outline:arrow-left"></iconify-icon>
                Quay lại
            </a>
        </div>

        {{-- Error --}}
        @if ($errors->any())
            <div class="p-4 rounded-xl border border-red-200 bg-red-50 text-red-600">
                @foreach ($errors->all() as $error)
                    <div class="text-sm">{{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700
                    shadow-lg shadow-slate-200/60 dark:shadow-none
                    overflow-hidden">

            <div class="p-8">
                <form action="{{ route('expense-groups.add-expense', $group->id) }}" method="POST" id="expenseForm">
                    @csrf

                    {{-- Basic Info --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                Nội dung chi tiêu <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" required
                                   value="{{ old('title') }}"
                                   placeholder="Vd: Ăn hải sản, Tiền taxi..."
                                   class="w-full px-4 py-3 rounded-xl
                                          bg-white dark:bg-slate-800
                                          border border-slate-300 dark:border-slate-600
                                          focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                Ngày chi <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="date" required
                                   value="{{ old('date', date('Y-m-d')) }}"
                                   class="w-full px-4 py-3 rounded-xl
                                          bg-white dark:bg-slate-800
                                          border border-slate-300 dark:border-slate-600
                                          focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20 transition">
                        </div>
                    </div>

                    {{-- Total Amount --}}
                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Tổng số tiền (VNĐ) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="total_amount" name="total_amount" required
                               value="{{ old('total_amount') }}"
                               placeholder="0"
                               class="w-full px-4 py-4 rounded-xl text-xl font-bold
                                      bg-white dark:bg-slate-800
                                      border border-slate-300 dark:border-slate-600
                                      text-green-600
                                      focus:border-green-500 focus:ring-4 focus:ring-green-500/20 transition">
                        <p class="text-xs text-slate-500 mt-1">
                            Nhập tổng số tiền hóa đơn.
                        </p>
                    </div>

                    <hr class="my-8 border-slate-200 dark:border-slate-700">

                    {{-- Payers --}}
                    <div class="mb-8">
                        <h5 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center">
                            <iconify-icon icon="heroicons-outline:currency-dollar" class="mr-2"></iconify-icon>
                            Ai thanh toán?
                        </h5>

                        <div class="space-y-3">
                            @foreach($group->members as $member)
                                <div class="flex items-center gap-4 p-4 rounded-xl
                                            bg-slate-50 dark:bg-slate-800
                                            border border-slate-200 dark:border-slate-700">
                                    <img src="{{ $member->avatar ?? asset('images/all-img/user.png') }}"
                                         class="w-10 h-10 rounded-full object-cover">

                                    <div class="flex-1">
                                        <div class="text-sm font-semibold text-slate-800 dark:text-slate-300">
                                            {{ $member->name }}
                                        </div>
                                        <div class="text-xs text-slate-500">
                                            {{ $member->email }}
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <input type="number"
                                               name="payers[{{ $member->id }}]"
                                               id="payer-{{ $member->id }}"
                                               value="{{ old('payers.'.$member->id, 0) }}"
                                               class="payer-input w-28 px-3 py-2 rounded-lg text-right
                                                      border border-slate-300 dark:border-slate-600
                                                      bg-white dark:bg-slate-800">

                                        <button type="button"
                                                onclick="setPayAll({{ $member->id }})"
                                                class="px-3 py-1.5 rounded-lg text-sm
                                                       border border-primary-500 text-primary-600
                                                       hover:bg-primary-50 transition">
                                            Trả hết
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div id="payer-warning" class="mt-3 hidden"></div>
                    </div>

                    <hr class="my-8 border-slate-200 dark:border-slate-700">

                    {{-- Share --}}
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h5 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center">
                                <iconify-icon icon="heroicons-outline:users" class="mr-2"></iconify-icon>
                                Chia cho ai?
                            </h5>
                            <div class="flex gap-2">
                                <button type="button" id="selectAllBtn"
                                        class="px-3 py-1.5 rounded-full text-sm
                                               border border-primary-500 text-primary-600">
                                    Chọn tất cả
                                </button>
                                <button type="button" id="deselectAllBtn"
                                        class="px-3 py-1.5 rounded-full text-sm
                                               border border-danger-500 text-danger-500">
                                    Bỏ chọn hết
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($group->members as $member)
                                <div class="relative">
                                    <input type="checkbox"
                                           name="shares[]"
                                           value="{{ $member->id }}"
                                           id="share-user-{{ $member->id }}"
                                           class="split-checkbox hidden"
                                           checked>

                                    <label for="share-user-{{ $member->id }}"
                                           class="share-label-container flex items-center gap-3 p-4
                                                  rounded-xl border-2 border-slate-200
                                                  bg-white dark:bg-slate-800 dark:border-slate-700
                                                  cursor-pointer transition-all duration-200">
                                        <img src="{{ $member->avatar ?? asset('images/all-img/user.png') }}"
                                             class="w-10 h-10 rounded-full object-cover">

                                        <div class="flex-1">
                                            <div class="user-name text-sm font-semibold text-slate-700 dark:text-slate-300">
                                                {{ $member->name }}
                                            </div>
                                            <div class="text-xs text-slate-500">Thành viên</div>
                                        </div>

                                        <div class="check-icon-large w-6 h-6 rounded-full border-2 border-slate-300
                                                    flex items-center justify-center opacity-50 transition">
                                            <iconify-icon icon="heroicons-outline:check" class="text-sm"></iconify-icon>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div id="count-selected" class="text-xs text-slate-500 mt-3 text-right">
                            Đã chọn: {{ $group->members->count() }} người
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="flex justify-end pt-6 border-t border-slate-200 dark:border-slate-700">
                        <button type="submit"
                                class="px-8 py-3 rounded-xl text-lg font-semibold
                                       bg-primary-600 text-white
                                       hover:bg-primary-700 hover:shadow-lg hover:shadow-primary-600/30 transition">
                            <iconify-icon icon="heroicons-outline:save" class="mr-2"></iconify-icon>
                            Lưu Khoản Chi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



   <script>
    // ==========================================
    // 1. Các hàm xử lý tiền nong (Giữ nguyên)
    // ==========================================
    function setPayAll(userId) {
        const totalAmount = parseFloat(document.getElementById('total_amount').value) || 0;
        
        if(totalAmount <= 0) {
            alert('Vui lòng nhập "Tổng số tiền" hóa đơn trước!');
            document.getElementById('total_amount').focus();
            return;
        }

        document.querySelectorAll('.payer-input').forEach(input => {
            input.value = 0;
        });

        const targetInput = document.getElementById('payer-' + userId);
        if(targetInput) {
            targetInput.value = totalAmount;
            targetInput.classList.add('bg-green-100');
            setTimeout(() => targetInput.classList.remove('bg-green-100'), 500);
        }
        
        checkTotalMatch();
    }

    function checkTotalMatch() {
        const totalBill = parseFloat(document.getElementById('total_amount').value) || 0;
        let totalPaid = 0;
        document.querySelectorAll('.payer-input').forEach(input => {
            totalPaid += parseFloat(input.value) || 0;
        });

        const warningMsg = document.getElementById('payer-warning');
        
        if (Math.abs(totalBill - totalPaid) > 100 && totalBill > 0) {
            warningMsg.classList.remove('hidden');
            warningMsg.innerHTML = `<div class="flex items-center text-red-500 bg-red-50 p-3 rounded-md border border-red-200">
                <iconify-icon icon="heroicons-outline:exclamation-circle" class="text-xl mr-2"></iconify-icon> 
                <span>Tổng tiền người trả: <b>${totalPaid.toLocaleString()}</b> (Lệch: ${(totalBill - totalPaid).toLocaleString()})</span>
            </div>`;
        } else {
            warningMsg.classList.add('hidden');
        }
    }

  
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.split-checkbox');
        const selectAllBtn = document.getElementById('selectAllBtn');
        const deselectAllBtn = document.getElementById('deselectAllBtn');
        const countLabel = document.getElementById('count-selected');

        // --- Hàm cập nhật giao diện dựa trên trạng thái checkbox ---
        function updateVisualState(checkbox) {
            // Tìm thẻ label ngay sau checkbox đó
            const labelContainer = checkbox.nextElementSibling; 
            if (!labelContainer) return;

            const smallIcon = labelContainer.querySelector('.check-icon-small');
            const largeIcon = labelContainer.querySelector('.check-icon-large');
            const userName = labelContainer.querySelector('.user-name');

            if (checkbox.checked) {
                // Trạng thái ĐÃ CHỌN (Active)
                labelContainer.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
                labelContainer.classList.remove('border-slate-200', 'bg-white', 'dark:bg-slate-800', 'dark:border-slate-700');
                
                if(smallIcon) smallIcon.classList.replace('scale-0', 'scale-100');
                if(userName) userName.classList.add('text-primary-700', 'dark:text-primary-400');
                
                if(largeIcon) {
                    largeIcon.classList.add('bg-primary-500', 'border-primary-500', 'opacity-100');
                    largeIcon.classList.remove('border-slate-300', 'opacity-50');
                }

            } else {
                // Trạng thái KHÔNG CHỌN (Inactive)
                labelContainer.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
                labelContainer.classList.add('border-slate-200', 'bg-white', 'dark:bg-slate-800', 'dark:border-slate-700');

                if(smallIcon) smallIcon.classList.replace('scale-100', 'scale-0');
                if(userName) userName.classList.remove('text-primary-700', 'dark:text-primary-400');

                if(largeIcon) {
                    largeIcon.classList.remove('bg-primary-500', 'border-primary-500', 'opacity-100');
                    largeIcon.classList.add('border-slate-300', 'opacity-50');
                }
            }
        }

        // Hàm cập nhật số lượng và đồng bộ giao diện tất cả item
        function updateAllStates() {
            let checkedCount = 0;
            checkboxes.forEach(cb => {
                updateVisualState(cb); // Cập nhật giao diện từng cái
                if(cb.checked) checkedCount++;
            });
            countLabel.innerText = `Đã chọn: ${checkedCount} người`;
        }

        // --- Sự kiện ---

        // 1. Khi click vào từng item
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateVisualState(this);
                // Cập nhật lại số lượng tổng
                const checkedCount = document.querySelectorAll('.split-checkbox:checked').length;
                countLabel.innerText = `Đã chọn: ${checkedCount} người`;
            });
        });

        // 2. Nút Chọn tất cả
        selectAllBtn.addEventListener('click', function() {
            checkboxes.forEach(cb => cb.checked = true);
            updateAllStates();
        });

        // 3. Nút Bỏ chọn hết
        deselectAllBtn.addEventListener('click', function() {
            checkboxes.forEach(cb => cb.checked = false);
            updateAllStates();
        });

        // --- Khởi chạy lần đầu ---
        // Các sự kiện tiền nong
        const totalInput = document.getElementById('total_amount');
        const payerInputs = document.querySelectorAll('.payer-input');
        totalInput.addEventListener('input', checkTotalMatch);
        payerInputs.forEach(input => input.addEventListener('input', checkTotalMatch));

        // Cập nhật giao diện lần đầu khi tải trang
        updateAllStates();
    });
</script>
</x-app-layout>