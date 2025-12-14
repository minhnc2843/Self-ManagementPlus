@php
    // Chuẩn bị dữ liệu cho form
    $start_date = $event->start_time ? $event->start_time->format('Y-m-d') : null;
    $start_time_hour = $event->start_time ? $event->start_time->format('H:i') : '00:00';
    
    $end_date = $event->end_time ? $event->end_time->format('Y-m-d') : null;
    $end_time_hour = $event->end_time ? $event->end_time->format('H:i') : '00:00';
    
    // Danh sách các tùy chọn ưu tiên và loại sự kiện
    $priorities = $priorities ?? ['low', 'normal', 'high'];
    $eventTypes = $eventTypes ?? App\Http\Controllers\EventController::EVENT_TYPES;
@endphp

<x-app-layout>
    <div class="space-y-8">
        <div>
            <x-breadcrumb :page-title="$pageTitle" :breadcrumb-items="$breadcrumbItems" />
        </div>

        <div class="card">
            <div class="card-body p-6">
                <form method="POST" action="{{ route('events.update', $event->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Cột 1: Tên, Mô tả, Loại --}}
                        <div class="space-y-4">
                            {{-- Tên sự kiện --}}
                            <div class="input-area">
                                <label for="title" class="form-label">Tên sự kiện <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror pr-10"
                                        value="{{ old('title', $event->title) }}" required>
                                    <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 clear-input-btn" data-target="title">
                                        <iconify-icon icon="heroicons:x-mark" class="w-4 h-4"></iconify-icon>Clear
                                    </button>
                                </div>
                                @error('title')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Mô tả --}}
                            <div class="input-area">
                                <label for="description" class="form-label">Mô tả</label>
                                <div class="relative">
                                    <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror pr-10 resize-none">{{ old('description', $event->description) }}</textarea>
                                    <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 clear-input-btn" data-target="description" style="top: 50%; transform: translateY(-50%);">
                                        <iconify-icon icon="heroicons:x-mark" class="w-4 h-4"></iconify-icon>
                                    </button>
                                </div>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Loại sự kiện --}}
                            <div class="input-area">
                                <label for="event_type" class="form-label">Loại sự kiện</label>
                                <div class="relative">
                                    <select name="event_type" id="event_type" class="form-control @error('event_type') is-invalid @enderror pr-10">
                                        <option value="">Chọn loại</option>
                                        @foreach ($eventTypes as $type)
                                            <option value="{{ $type }}"
                                                {{ old('event_type', $event->event_type) == $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 clear-input-btn" data-target="event_type">
                                        <iconify-icon icon="heroicons:x-mark" class="w-4 h-4"></iconify-icon>
                                    </button>
                                </div>
                                @error('event_type')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Cột 2: Địa điểm, Thời gian bắt đầu, Thời gian kết thúc --}}
                        <div class="space-y-4">
                            {{-- Địa điểm --}}
                            <div class="input-area">
                                <label for="location" class="form-label">Địa điểm</label>
                                <div class="relative">
                                    <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror pr-10"
                                        value="{{ old('location', $event->location) }}">
                                    <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 clear-input-btn" data-target="location">
                                        <iconify-icon icon="heroicons:x-mark" class="w-4 h-4"></iconify-icon>
                                    </button>
                                </div>
                                @error('location')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- THỜI GIAN BẮT ĐẦU --}}
                            <div class="input-area">
                                <label class="form-label">Thời gian bắt đầu <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <div class="relative">
                                        <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror pr-10"
                                            value="{{ old('start_date', $start_date) }}" required>
                                        <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 clear-date-btn" data-target="start_date">
                                            <iconify-icon icon="heroicons:x-mark" class="w-4 h-4"></iconify-icon>
                                        </button>
                                    </div>
                                    <div class="relative">
                                        <input type="time" name="start_time_hour" id="start_time_hour" class="form-control @error('start_time_hour') is-invalid @enderror pr-10"
                                            value="{{ old('start_time_hour', $start_time_hour) }}" step="3600">
                                        <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 clear-time-btn" data-target="start_time_hour">
                                            <iconify-icon icon="heroicons:x-mark" class="w-4 h-4"></iconify-icon>
                                        </button>
                                    </div>
                                </div>
                                @error('start_time')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- THỜI GIAN KẾT THÚC --}}
                            <div class="input-area">
                                <label class="form-label">Thời gian kết thúc (Tùy chọn - Mặc định 1 ngày nếu trống)</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <div class="relative">
                                        <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror pr-10"
                                            value="{{ old('end_date', $end_date) }}">
                                        <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 clear-date-btn" data-target="end_date">
                                            <iconify-icon icon="heroicons:x-mark" class="w-4 h-4"></iconify-icon>
                                        </button>
                                    </div>
                                    <div class="relative">
                                        <input type="time" name="end_time_hour" id="end_time_hour" class="form-control @error('end_time_hour') is-invalid @enderror pr-10"
                                            value="{{ old('end_time_hour', $end_time_hour) }}" step="3600">
                                        <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 clear-time-btn" data-target="end_time_hour">
                                            <iconify-icon icon="heroicons:x-mark" class="w-4 h-4"></iconify-icon>
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="remove_end_time" id="remove_end_time" value="0">
                                <small class="text-slate-500 block mt-1">Nếu để trống, sẽ tự động đặt là 00:00 ngày hôm sau ngày bắt đầu.</small>
                                @error('end_time')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Cột 3: Ưu tiên, Nhắc nhở, Lặp lại --}}
                        <div class="space-y-4">
                            {{-- Ưu tiên và Quan trọng --}}
                            <div class="input-area">
                                <label class="form-label">Độ ưu tiên & Quan trọng</label>
                                <div class="space-y-3">
                                    <div class="relative">
                                        <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror pr-10">
                                            <option value="">Chọn ưu tiên</option>
                                            @foreach ($priorities as $priority)
                                                <option value="{{ $priority }}"
                                                    {{ old('priority', $event->priority) == $priority ? 'selected' : '' }}>
                                                    {{ ucfirst($priority) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 clear-input-btn" data-target="priority">
                                            <iconify-icon icon="heroicons:x-mark" class="w-4 h-4"></iconify-icon>
                                        </button>
                                    </div>
                                    @error('priority')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_important" id="is_important" value="1"
                                            class="h-5 w-5 rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 @error('is_important') border-red-500 @enderror"
                                            {{ old('is_important', $event->is_important) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-slate-600 dark:text-slate-300">
                                            Đánh dấu Quan trọng
                                        </span>
                                    </label>
                                    @error('is_important')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- NHẮC NHỞ --}}
                            <div class="input-area">
                                <label class="form-label">Nhắc nhở</label>
                                <div id="reminders-container">
                                    @php $reminders = old('reminders', $event->reminders->pluck('remind_at')->toArray() ?? []); @endphp
                                    @forelse ($reminders as $index => $remind_at)
                                        @php
                                            try {
                                                $remind_value = \Carbon\Carbon::parse($remind_at)->format('Y-m-d\TH:i');
                                            } catch (\Exception $e) {
                                                $remind_value = '';
                                            }
                                        @endphp
                                        <div class="flex gap-2 mb-2 reminder-item">
                                            <div class="relative flex-1">
                                                <input type="datetime-local" name="reminders[]" class="form-control @error('reminders.' . $index) is-invalid @enderror pr-10"
                                                    value="{{ $remind_value }}">
                                                <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 clear-input-btn" data-target-reminder>
                                                    <iconify-icon icon="heroicons:x-mark" class="w-4 h-4"></iconify-icon>
                                                </button>
                                            </div>
                                            @error('reminders.' . $index)
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-reminder flex items-center justify-center px-2">
                                                <iconify-icon icon="heroicons:x-mark" class="w-4 h-4"></iconify-icon>
                                            </button>
                                        </div>
                                    @empty
                                        <div class="flex gap-2 mb-2 reminder-item">
                                            <div class="relative flex-1">
                                                <input type="datetime-local" name="reminders[]" class="form-control pr-10" value="">
                                                <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 clear-input-btn" data-target-reminder>
                                                    <iconify-icon icon="heroicons:x-mark" class="w-4 h-4"></iconify-icon>
                                                </button>
                                            </div>
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-reminder flex items-center justify-center px-2">
                                                <iconify-icon icon="heroicons:x-mark" class="w-4 h-4"></iconify-icon>
                                            </button>
                                        </div>
                                    @endforelse
                                </div>
                                <button type="button" id="add-reminder-btn" class="btn btn-outline-primary btn-sm mt-2">
                                    <iconify-icon icon="heroicons:plus" class="w-4 h-4 mr-1"></iconify-icon>
                                    Thêm nhắc nhở
                                </button>
                                @error('reminders')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- QUY TẮC LẶP LẠI --}}
                            <div class="input-area">
                                <label for="repeat_rule" class="form-label">Quy tắc lặp lại</label>
                                <div class="relative">
                                    <select name="repeat_rule" id="repeat_rule" class="form-control @error('repeat_rule') is-invalid @enderror pr-10" onchange="toggleRepeatMeta()">
                                        <option value="null" {{ old('repeat_rule', $event->repeat_rule ?? 'null') == 'null' ? 'selected' : '' }}>Không lặp</option>
                                        <option value="daily" {{ old('repeat_rule', $event->repeat_rule) == 'daily' ? 'selected' : '' }}>Hàng ngày</option>
                                        <option value="weekly" {{ old('repeat_rule', $event->repeat_rule) == 'weekly' ? 'selected' : '' }}>Hàng tuần</option>
                                        <option value="monthly" {{ old('repeat_rule', $event->repeat_rule) == 'monthly' ? 'selected' : '' }}>Hàng tháng</option>
                                        <option value="yearly" {{ old('repeat_rule', $event->repeat_rule) == 'yearly' ? 'selected' : '' }}>Hàng năm</option>
                                        <option value="custom" {{ old('repeat_rule', $event->repeat_rule) == 'custom' ? 'selected' : '' }}>Tùy chỉnh</option>
                                    </select>
                                    <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 clear-input-btn" data-target="repeat_rule">
                                        <iconify-icon icon="heroicons:x-mark" class="w-4 h-4"></iconify-icon>
                                    </button>
                                </div>
                                @error('repeat_rule')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror

                                <div id="repeat-meta-container" class="mt-3 space-y-2" style="display: none;">
                                    <label for="repeat_meta" class="form-label">Chi tiết lặp lại (JSON)</label>
                                    <div class="relative">
                                        <textarea name="repeat_meta" id="repeat_meta" rows="2" class="form-control @error('repeat_meta') is-invalid @enderror pr-10 resize-none" placeholder='{"interval": 2, "days": [1,3,5]}'></textarea>
                                        <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 clear-input-btn" data-target="repeat_meta" style="top: 50%; transform: translateY(-50%);">
                                            <iconify-icon icon="heroicons:x-mark" class="w-4 h-4"></iconify-icon>
                                        </button>
                                    </div>
                                    @error('repeat_meta')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="text-slate-500">Ví dụ: {"interval": 2} cho mỗi 2 tuần. Chỉ cần cho weekly/custom.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger mt-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-end">
                        <a href="{{ route('events.list') }}" class="btn btn-outline-secondary">
                            <iconify-icon icon="heroicons:arrow-left" class="w-4 h-4 mr-1"></iconify-icon>
                            Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <iconify-icon icon="heroicons:check-circle" class="w-4 h-4 mr-1"></iconify-icon>
                            Cập Nhật Sự Kiện
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        function toggleRepeatMeta() {
            const rule = document.getElementById('repeat_rule').value;
            const container = document.getElementById('repeat-meta-container');
            const meta = document.getElementById('repeat_meta');
            if (rule === 'weekly' || rule === 'custom') {
                container.style.display = 'block';
                if (!meta.value) {
                    meta.value = '{{ old('repeat_meta', $event->repeat_meta ?? '') }}';
                }
            } else {
                container.style.display = 'none';
                meta.value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            toggleRepeatMeta();

            // Xử lý clear buttons chung cho tất cả inputs/selects/textarea
            document.querySelectorAll('.clear-input-btn, .clear-date-btn, .clear-time-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.dataset.target;
                    const input = document.getElementById(targetId);
                    if (input) {
                        input.value = '';
                        if (targetId === 'end_date') {
                            document.getElementById('remove_end_time').value = '1';
                            document.getElementById('end_time_hour').value = '';
                        }
                        if (targetId === 'start_date') {
                            document.getElementById('start_time_hour').value = '00:00';
                            // Auto set end_date nếu empty
                            const endDate = document.getElementById('end_date');
                            if (!endDate.value) {
                                const startDate = new Date(input.value + 'T00:00:00');
                                startDate.setDate(startDate.getDate() + 1);
                                endDate.value = startDate.toISOString().split('T')[0];
                                document.getElementById('remove_end_time').value = '0';
                            }
                        }
                    }
                    // Đối với reminders, clear input cụ thể
                    if (this.dataset.targetReminder) {
                        const reminderInput = this.closest('.reminder-item').querySelector('input[name="reminders[]"]');
                        if (reminderInput) reminderInput.value = '';
                    }
                });
            });

            // Auto-set end_date nếu start_date thay đổi và end_date empty
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const removeEndTimeHidden = document.getElementById('remove_end_time');
            if (startDateInput && endDateInput) {
                startDateInput.addEventListener('change', function() {
                    if (!endDateInput.value) {
                        const startDate = new Date(this.value + 'T00:00:00');
                        startDate.setDate(startDate.getDate() + 1);
                        endDateInput.value = startDate.toISOString().split('T')[0];
                        removeEndTimeHidden.value = '0';
                    }
                });
            }

            // Reset remove_end_time nếu nhập end_date/time
            [endDateInput, document.getElementById('end_time_hour')].forEach(input => {
                if (input) {
                    input.addEventListener('input', function() {
                        removeEndTimeHidden.value = '0';
                    });
                }
            });

            // Default time to 00:00 nếu empty
            ['start_time_hour', 'end_time_hour'].forEach(id => {
                const timeInput = document.getElementById(id);
                if (timeInput && !timeInput.value) {
                    timeInput.value = '00:00';
                }
                timeInput?.addEventListener('blur', function() {
                    if (!this.value) this.value = '00:00';
                });
            });

            // Thêm Reminder
            const addReminderBtn = document.getElementById('add-reminder-btn');
            const remindersContainer = document.getElementById('reminders-container');
            if (addReminderBtn && remindersContainer) {
                addReminderBtn.addEventListener('click', function() {
                    const newReminderHtml = `
                        <div class="flex gap-2 mb-2 reminder-item">
                            <div class="relative flex-1">
                                <input type="datetime-local" name="reminders[]" class="form-control pr-10" value="">
                                <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 clear-input-btn" data-target-reminder>
                                    <iconify-icon icon="heroicons:x-mark" class="w-4 h-4"></iconify-icon>
                                </button>
                            </div>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-reminder flex items-center justify-center px-2">
                                <iconify-icon icon="heroicons:x-mark" class="w-4 h-4"></iconify-icon>
                            </button>
                        </div>
                    `;
                    remindersContainer.insertAdjacentHTML('beforeend', newReminderHtml);
                });
            }

            // Xóa Reminder (giữ ít nhất 1)
            if (remindersContainer) {
                remindersContainer.addEventListener('click', function(e) {
                    if (e.target.closest('.remove-reminder')) {
                        e.preventDefault();
                        const item = e.target.closest('.reminder-item');
                        if (item && remindersContainer.children.length > 1) {
                            item.remove();
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>