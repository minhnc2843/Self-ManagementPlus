<x-app-layout>
    <div class="space-y-8 max-w-5xl mx-auto px-4 sm:px-6">

        {{-- Page Header --}}
        <div class="flex justify-between items-center">
            <h4 class="font-semibold lg:text-2xl text-xl text-slate-900 dark:text-white">
                Tạo nhóm chi tiêu mới
            </h4>
            <a href="{{ route('expense-groups.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                      border border-slate-300 dark:border-slate-700
                      bg-white dark:bg-slate-800
                      text-slate-700 dark:text-slate-300
                      hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                <iconify-icon icon="heroicons-outline:arrow-left" class="text-lg"></iconify-icon>
                Quay lại
            </a>
        </div>

        {{-- Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700
                    shadow-lg shadow-slate-200/60 dark:shadow-none
                    overflow-hidden">

            {{-- Card Header --}}
            <header class="px-8 py-6 border-b border-slate-200 dark:border-slate-700
                           bg-slate-50 dark:bg-slate-800/60">
                <div class="text-lg font-bold text-slate-900 dark:text-white">
                  <iconify-icon icon="heroicons:user-plus" class="mr-2"></iconify-icon>  Thông tin nhóm
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Điền thông tin và chọn thành viên để bắt đầu theo dõi chi tiêu chung.
                </p>
            </header>

            {{-- Card Body --}}
            <div class="p-8">
                <form class="space-y-6" action="{{ route('expense-groups.store') }}" method="POST">
                    @csrf

                    {{-- Group Name --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Tên nhóm <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               placeholder="Ví dụ: Ăn trưa văn phòng, Du lịch Đà Nẵng..."
                               class="w-full px-4 py-3 rounded-xl
                                      bg-white dark:bg-slate-800
                                      border border-slate-300 dark:border-slate-600
                                      text-slate-800 dark:text-slate-100
                                      focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20
                                      transition">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Mô tả (Tùy chọn)
                        </label>
                        <textarea name="description"
                                  rows="3"
                                  placeholder="Ghi chú về mục đích của nhóm này..."
                                  class="w-full px-4 py-3 rounded-xl
                                         bg-white dark:bg-slate-800
                                         border border-slate-300 dark:border-slate-600
                                         text-slate-800 dark:text-slate-100
                                         focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20
                                         transition">{{ old('description') }}</textarea>
                    </div>

                    {{-- Members --}}
                    <div class="pt-4">
                        <div class="flex justify-between items-center mb-4">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Thêm thành viên
                            </label>

                            <div class="flex gap-2">
                                <button type="button" id="selectAllBtn"
                                        class="px-4 py-1.5 rounded-full text-sm
                                               border border-primary-500 text-primary-600
                                               hover:bg-primary-50 transition flex items-center gap-1">
                                    <iconify-icon icon="heroicons-outline:check-circle"></iconify-icon>
                                    Chọn tất cả
                                </button>

                                <button type="button" id="deselectAllBtn"
                                        class="px-4 py-1.5 rounded-full text-sm
                                               border border-slate-300 dark:border-slate-700
                                               text-slate-500
                                               hover:border-danger-500 hover:text-danger-500
                                               hover:bg-danger-50 transition flex items-center gap-1">
                                    <iconify-icon icon="heroicons-outline:x-circle"></iconify-icon>
                                    Bỏ chọn
                                </button>
                            </div>
                        </div>

                        {{-- Member Grid --}}
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
                                           class="member-label flex items-center gap-3 p-4
                                                  rounded-xl border-2 border-slate-200
                                                  bg-white dark:bg-slate-800 dark:border-slate-700
                                                  cursor-pointer transition-all duration-200">

                                        <img src="{{ $user->avatar ? asset($user->avatar) : asset('images/all-img/user.png') }}"
                                             class="w-10 h-10 rounded-full object-cover">

                                        <div class="flex-1 min-w-0">
                                            <div class="user-name text-sm font-semibold text-slate-700 dark:text-slate-300 truncate">
                                                {{ $user->name }}
                                            </div>
                                            <div class="text-xs text-slate-500 truncate">
                                                {{ $user->email }}
                                            </div>
                                        </div>

                                        <div class="check-icon-large w-6 h-6 rounded-full
                                                    border-2 border-slate-300
                                                    flex items-center justify-center
                                                    text-white opacity-50 transition">
                                            <iconify-icon icon="heroicons-outline:check" class="text-sm"></iconify-icon>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="text-xs text-slate-500 mt-3 text-right" id="count-selected">
                            Đã chọn: 0 người
                        </div>

                        <p class="text-xs text-slate-400 mt-1">
                            * Bạn sẽ tự động được thêm vào nhóm với tư cách quản trị viên.
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
                        <a href="{{ route('expense-groups.index') }}"
                           class="px-5 py-2.5 rounded-xl text-sm
                                  border border-slate-300 dark:border-slate-700
                                  text-slate-600 dark:text-slate-300
                                  hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                <iconify-icon icon="heroicons:arrow-down-left"></iconify-icon>  Hủy
                        </a>
                        <button type="submit"
                                class="px-6 py-2.5 rounded-xl text-sm font-semibold
                                       bg-primary-600 text-white
                                       hover:bg-primary-700 hover:shadow-lg hover:shadow-primary-600/30
                                       transition">
                                <iconify-icon icon="heroicons:check" class="text-lg"></iconify-icon> Tạo nhóm
                        </button>
                    </div>
                </form>
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