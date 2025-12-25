<x-app-layout>
    <div class="space-y-8">
        <div class="flex justify-between items-center">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Thêm khoản chi: {{ $group->name }}
            </h4>
            <a href="{{ route('expense-groups.show', $group->id) }}" class="btn btn-outline-dark btn-sm">
                Quay lại
            </a>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger light-mode">
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <iconify-icon class="text-2xl" icon="system-uicons:warning-circle"></iconify-icon>
                <div class="flex-1">
                    @foreach ($errors->all() as $error)
                        <div class="text-sm">{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-body p-6">
                <form action="{{ route('expense-groups.add-expense', $group->id) }}" method="POST" id="expenseForm">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div class="input-area">
                            <label class="form-label">Nội dung chi tiêu <span class="text-red-500">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="Vd: Ăn hải sản, Tiền taxi..." value="{{ old('title') }}" required>
                        </div>
                        <div class="input-area">
                            <label class="form-label">Ngày chi <span class="text-red-500">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                    
                    <div class="input-area mb-8">
                        <label class="form-label">Tổng số tiền (VNĐ) <span class="text-red-500">*</span></label>
                        <input type="number" id="total_amount" name="total_amount" class="form-control text-xl font-bold text-green-600" placeholder="0" value="{{ old('total_amount') }}" required>
                        <span class="text-xs text-slate-500">Nhập tổng số tiền hóa đơn.</span>
                    </div>

                    <hr class="border-t border-slate-200 dark:border-slate-700 my-6">

                    <div class="mb-8">
                        <h5 class="text-slate-900 font-medium text-lg mb-3 flex items-center">
                            <iconify-icon icon="heroicons-outline:currency-dollar" class="mr-2"></iconify-icon> 
                            Ai thanh toán?
                        </h5>
                        
                        <div class="grid grid-cols-1 gap-4">
                            @foreach($group->members as $member)
                            <div class="flex items-center p-3 border border-slate-200 rounded-md bg-slate-50 dark:bg-slate-800 dark:border-slate-700 transition-all hover:border-primary-500">
                                <img src="{{ $member->avatar ?? asset('images/all-img/user.png') }}" class="w-10 h-10 rounded-full mr-3 object-cover">
                                
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-slate-900 dark:text-slate-300">{{ $member->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $member->email }}</div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <input type="number" 
                                           name="payers[{{ $member->id }}]" 
                                           class="form-control py-2 px-2 text-right w-32 payer-input font-medium" 
                                           id="payer-{{ $member->id }}"
                                           placeholder="0"
                                           min="0"
                                           value="{{ old('payers.'.$member->id, 0) }}">
                                           
                                    <button type="button" 
                                            onclick="setPayAll({{ $member->id }})"
                                            class="btn btn-sm btn-outline-primary whitespace-nowrap"
                                            title="Người này trả hết">
                                        Trả hết
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div id="payer-warning" class="text-orange-500 text-sm mt-2 hidden">
                            <iconify-icon icon="heroicons-outline:exclamation"></iconify-icon>
                            Tổng tiền người trả đang lệch so với hóa đơn!
                        </div>
                    </div>

                    <hr class="border-t border-slate-200 dark:border-slate-700 my-6">

                   <div class="mb-8">
    <div class="flex justify-between items-center mb-4">
        <h5 class="text-slate-900 font-medium text-lg flex items-center">
            <iconify-icon icon="heroicons-outline:users" class="mr-2"></iconify-icon> 
            Chia cho ai?
        </h5>
        <div class="flex gap-2">
            <button type="button" id="selectAllBtn" class="btn btn-sm btn-outline-primary rounded-full px-3">
                Chọn tất cả
            </button>
            <button type="button" id="deselectAllBtn" class="btn btn-sm btn-outline-danger rounded-full px-3">
                Bỏ chọn hết
            </button>
        </div>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="share-list-container">
        @foreach($group->members as $member)
        <div class="relative share-item-wrapper">
            <input type="checkbox" 
                   name="shares[]" 
                   value="{{ $member->id }}" 
                   id="share-user-{{ $member->id }}"
                   class="split-checkbox" 
                   style="display: none;"
                   checked>

            <label for="share-user-{{ $member->id }}" 
                   class="share-label-container flex items-center p-3 rounded-xl border-2 cursor-pointer transition-all duration-200 select-none bg-white border-slate-200 dark:bg-slate-800 dark:border-slate-700">
                
                <div class="relative mr-3">
                    <img src="{{ $member->avatar ?? asset('images/all-img/user.png') }}" class="w-10 h-10 rounded-full object-cover">
                    
                </div>
                
                <div class="flex-1">
                    <div class="user-name text-sm font-bold text-slate-700 dark:text-slate-300">
                        {{ $member->name }}
                    </div>
                    <div class="text-xs text-slate-500">Thành viên</div>
                </div>

                <div class="check-icon-large w-6 h-6 rounded-full border-2 border-slate-300 flex items-center justify-center text-white transition-colors opacity-50">
                    <iconify-icon icon="heroicons-outline:check" class="text-sm"></iconify-icon>
                </div>
            </label>
        </div>
        @endforeach
    </div>
    <div class="text-xs text-slate-500 mt-3 text-right" id="count-selected">
        Đã chọn: {{ $group->members->count() }} người
    </div>
</div>

                    <div class="flex justify-end mt-6 pb-10">
                        <button type="submit" class="btn btn-primary px-8 py-2 text-lg shadow-lg hover:shadow-xl transition-all">
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