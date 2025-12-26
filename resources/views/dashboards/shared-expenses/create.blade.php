<x-app-layout>
    <div class="space-y-8">
        <div class="flex justify-between items-center">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Tạo nhóm chi tiêu mới
            </h4>
            <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center rtl:space-x-reverse">
                <a href="{{ route('expense-groups.index') }}" class="btn inline-flex justify-center btn-outline-dark">
                  <i class="iconify-icon text-lg" data-icon="heroicons-outline:arrow-left" data-inline="false"></i>  Quay lại
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body flex flex-col p-6">
                <header class="flex mb-5 items-center border-b border-slate-100 dark:border-slate-700 pb-5 -mx-6 px-6">
                    <div class="flex-1">
                        <div class="card-title text-slate-900 dark:text-white">Thông tin nhóm</div>
                        <div class="text-xs font-normal text-slate-500 dark:text-slate-400 mt-1">
                            Điền thông tin và chọn thành viên để bắt đầu theo dõi chi tiêu chung.
                        </div>
                    </div>
                </header>

                <div class="card-text h-full">
                    <form class="space-y-4" action="{{ route('expense-groups.store') }}" method="POST">
                        @csrf
                        
                        <div class="input-area relative">
                            <label for="name" class="form-label">Tên nhóm <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Ví dụ: Ăn trưa văn phòng, Du lịch Đà Nẵng..." value="{{ old('name') }}" required>
                            @error('name')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="input-area relative">
                            <label for="description" class="form-label">Mô tả (Tùy chọn)</label>
                            <textarea name="description" id="description" class="form-control" rows="3" placeholder="Ghi chú về mục đích của nhóm này...">{{ old('description') }}</textarea>
                        </div>

                        <div class="input-area mt-6">
                            <div class="flex justify-between items-center mb-3">
                                <label class="form-label block">Thêm thành viên</label>
                               <div class="flex flex-wrap justify-between items-end mb-4 gap-4">
                                    <div class="flex gap-2">
                                        <button type="button" id="selectAllBtn" 
                                                class="btn btn-sm btn-outline-primary rounded-full px-4 flex items-center gap-2 transition-all duration-200 hover:shadow-md hover:scale-105 active:scale-95">
                                            <iconify-icon icon="heroicons-outline:check-circle" class="text-lg"></iconify-icon>
                                            <span>Chọn tất cả</span>
                                        </button>

                                        <button type="button" id="deselectAllBtn" 
                                                class="btn btn-sm border border-slate-200 text-slate-500 bg-white rounded-full px-4 flex items-center gap-2 transition-all duration-200 
                                                    hover:border-danger-500 hover:text-danger-500 hover:bg-danger-50 hover:shadow-md active:scale-95
                                                    dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-slate-700">
                                            <iconify-icon icon="heroicons-outline:x-circle" class="text-lg"></iconify-icon>
                                            <span>Bỏ chọn</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach($users as $user)
                                <div class="relative">
                                    <input type="checkbox" 
                                           name="members[]" 
                                           value="{{ $user->id }}" 
                                           id="user-{{ $user->id }}"
                                           class="member-checkbox hidden"
                                           {{ (is_array(old('members')) && in_array($user->id, old('members'))) ? 'checked' : '' }}>
                                    
                                    <label for="user-{{ $user->id }}" 
                                           class="member-label flex items-center p-3 border-2 border-slate-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-slate-300 bg-white dark:bg-slate-800 dark:border-slate-700 select-none">
                                        
                                        <div class="relative mr-3">
                                            <img src="{{ $user->avatar ? asset($user->avatar) : asset('images/all-img/user.png') }}" class="w-10 h-10 rounded-full object-cover">
                                            
                                           
                                        </div>
                                        
                                        <div class="flex-1">
                                            <div class="user-name text-sm font-bold text-slate-700 dark:text-slate-300 transition-colors">
                                                {{ $user->name }}
                                            </div>
                                            <div class="text-xs text-slate-500">{{ $user->email }}</div>
                                        </div>

                                        <div class="check-icon-large w-6 h-6 rounded-full border-2 border-slate-300 flex items-center justify-center text-white transition-colors opacity-50">
                                            <iconify-icon icon="heroicons-outline:check" class="text-sm"></iconify-icon>
                                        </div>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <div class="text-xs text-slate-500 mt-3 text-right" id="count-selected">
                                Đã chọn: 0 người
                            </div>
                            <div class="text-xs text-slate-500 mt-2">* Bạn sẽ tự động được thêm vào nhóm với tư cách quản trị viên.</div>
                        </div>

                        <div class="flex justify-end space-x-3 rtl:space-x-reverse mt-6">
                            <a href="{{ route('expense-groups.index') }}" class="btn btn-dark">Hủy</a>
                            <button type="submit" class="btn btn-primary px-6">Tạo nhóm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.member-checkbox');
            const selectAllBtn = document.getElementById('selectAllBtn');
            const deselectAllBtn = document.getElementById('deselectAllBtn');
            const countLabel = document.getElementById('count-selected');

            // Hàm cập nhật giao diện 1 item
            function updateVisualState(checkbox) {
                const label = checkbox.nextElementSibling; // Tìm thẻ label ngay sau input
                if (!label) return;

                const smallIcon = label.querySelector('.check-icon-small');
                const largeIcon = label.querySelector('.check-icon-large');
                const userName = label.querySelector('.user-name');

                if (checkbox.checked) {
                    // Trạng thái: ĐÃ CHỌN
                    label.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
                    label.classList.remove('border-slate-200', 'bg-white', 'dark:border-slate-700');
                    
                    if(smallIcon) smallIcon.classList.replace('scale-0', 'scale-100');
                    if(userName) userName.classList.add('text-primary-700', 'dark:text-primary-400');
                    
                    if(largeIcon) {
                        largeIcon.classList.add('bg-primary-500', 'border-primary-500', 'opacity-100');
                        largeIcon.classList.remove('border-slate-300', 'opacity-50');
                    }
                } else {
                    // Trạng thái: CHƯA CHỌN
                    label.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
                    label.classList.add('border-slate-200', 'bg-white', 'dark:border-slate-700');

                    if(smallIcon) smallIcon.classList.replace('scale-100', 'scale-0');
                    if(userName) userName.classList.remove('text-primary-700', 'dark:text-primary-400');

                    if(largeIcon) {
                        largeIcon.classList.remove('bg-primary-500', 'border-primary-500', 'opacity-100');
                        largeIcon.classList.add('border-slate-300', 'opacity-50');
                    }
                }
            }

            // Hàm cập nhật tất cả và đếm số lượng
            function updateAllStates() {
                let count = 0;
                checkboxes.forEach(cb => {
                    updateVisualState(cb);
                    if(cb.checked) count++;
                });
                countLabel.innerText = `Đã chọn: ${count} người`;
            }

            // Gán sự kiện click cho từng checkbox
            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    updateVisualState(this);
                    // Đếm lại
                    const count = document.querySelectorAll('.member-checkbox:checked').length;
                    countLabel.innerText = `Đã chọn: ${count} người`;
                });
            });

            // Sự kiện Chọn tất cả
            selectAllBtn.addEventListener('click', () => {
                checkboxes.forEach(cb => cb.checked = true);
                updateAllStates();
            });

            // Sự kiện Bỏ chọn hết
            deselectAllBtn.addEventListener('click', () => {
                checkboxes.forEach(cb => cb.checked = false);
                updateAllStates();
            });

            // Chạy lần đầu (để hiển thị đúng nếu validation fail và load lại trang cũ)
            updateAllStates();
        });
    </script>
</x-app-layout>