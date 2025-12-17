<x-app-layout>
    <div class="space-y-8">
        <div>
            <x-breadcrumb :page-title="$pageTitle" :breadcrumb-items="$breadcrumbItems" />
        </div>
        {{-- GLOBAL MESSAGES --}}
@if (session('success'))
    <div class="alert alert-success mb-6">
        <div class="flex items-center gap-2">
            <iconify-icon icon="heroicons:check-circle" class="text-xl"></iconify-icon>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger mb-6">
        <div class="flex items-center gap-2">
            <iconify-icon icon="heroicons:x-circle" class="text-xl"></iconify-icon>
            <span>{{ session('error') }}</span>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger mb-6">
        <div class="flex gap-3">
            <iconify-icon icon="heroicons:exclamation-triangle" class="text-xl mt-1"></iconify-icon>
            <div>
                <p class="font-semibold mb-1">Vui lòng kiểm tra lại thông tin:</p>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

        <div class="card">
            <div class="card-body px-6 pb-6 pt-2">

                <form action="{{ route('events.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                        {{-- Title (Bắt buộc) --}}
                        <div class="input-area">
                            <label for="title" class="form-label">Tiêu đề (Bắt buộc)</label>
                            <input id="title" name="title" type="text" class="form-control" 
                                value="{{ old('title') }}">
                            @error('title') 
                                <div class="font-medium text-danger-500 text-sm mt-1">{{ $message }}</div> 
                            @enderror
                        </div>

                       
                        {{-- Start Date --}}
                        <div class="input-area">
                            <label for="start_date" class="form-label">Ngày Bắt đầu</label>
                            <input id="start_date" name="start_date" type="date" class="form-control" required
                                value="{{ old('start_date', now()->format('Y-m-d')) }}">
                            @error('start_date') 
                                <div class="font-medium text-danger-500 text-sm mt-1">{{ $message }}</div> 
                            @enderror
                        </div>

                        {{-- Start Time --}}
                        <div class="input-area">
                            <label for="start_time_hour" class="form-label">Giờ Bắt đầu (Để trống: 00:00)</label>
                            <input id="start_time_hour" name="start_time_hour" type="time" class="form-control"
                                value="{{ old('start_time_hour') }}">
                        </div>
                         {{-- Event Type --}}
                        <div class="input-area">
                            <label for="event_type" class="form-label">Phân loại</label>
                            <select id="event_type" name="event_type" class="form-control">
                                <option value="">Chọn loại</option>
                                @foreach($eventTypes as $type)
                                    <option value="{{ strtolower($type) }}" 
                                        {{ old('event_type') == strtolower($type) ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                       

                        {{-- End Date --}}
                        <div class="input-area">
                            <label for="end_date" class="form-label">Ngày Kết thúc (Có thể trống)</label>
                            <input id="end_date" name="end_date" type="date" class="form-control"
                                value="{{ old('end_date') }}">
                        </div>

                        {{-- End Time --}}
                        <div class="input-area">
                            <label for="end_time_hour" class="form-label">Giờ Kết thúc (Nếu có ngày)</label>
                            <input id="end_time_hour" name="end_time_hour" type="time" class="form-control"
                                value="{{ old('end_time_hour') }}">
                        </div>
                         {{-- Location --}}
                        <div class="input-area">
                            <label for="location" class="form-label">Địa điểm</label>
                            <input id="location" name="location" type="text" class="form-control"
                                value="{{ old('location') }}">
                        </div>
                        {{-- Priority --}}
                        <div class="input-area">
                            <label for="priority" class="form-label">Mức ưu tiên</label>
                            <select id="priority" name="priority" class="form-control">
                                @foreach($priorities as $p)
                                    <option value="{{ $p }}" {{ old('priority', 'normal') == $p ? 'selected' : '' }}>
                                        {{ ucfirst($p) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Repeat Rule --}}
                        <div class="input-area">
                            <label for="repeat_rule" class="form-label">Lặp lại</label>
                            <select id="repeat_rule" name="repeat_rule" class="form-control">
                                <option value="">Không lặp</option>
                                <option value="daily">Hằng ngày</option>
                                <option value="weekly">Hằng tuần</option>
                                <option value="monthly">Hằng tháng</option>
                                <option value="yearly">Hằng năm</option>
                                <option value="custom">Tùy chỉnh...</option>
                            </select>
                        </div>

                        {{-- Important --}}
                        <div class="input-area flex items-center pt-8">
                            <label class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-150">
                                <input type="checkbox" name="is_important" value="1" class="sr-only peer" 
                                    {{ old('is_important') ? 'checked' : '' }}>
                                <div class="w-full h-full bg-slate-200 rounded-full peer-checked:bg-success-500 transition-colors duration-200"></div>
                            </label>
                            <span class="text-sm text-slate-500 dark:text-slate-400 ml-3">Đánh dấu Quan trọng</span>
                        </div>

                        {{-- Description --}}
                        <div class="input-area">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea id="description" name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                        </div>

                        {{-- Reminders --}}
                        <div class="input-area">
                            <label class="form-label">Thời điểm Nhắc nhở (Để trống dùng mặc định)</label>
                            <input name="reminders[]" type="datetime-local" class="form-control mb-2"
                                placeholder="Ví dụ: 2025-12-07T09:00">
                            <p class="text-sm text-slate-500 mt-1">
                                Lưu ý: Nếu không nhập, mặc định nhắc 1 ngày trước hạn. Bạn có thể thêm nhiều lần nhắc.
                            </p>
                        </div>

                    </div>

                    <div class="ltr:text-right rtl:text-left mt-6">
                        <button type="submit" class="btn btn-primary">Lưu Sự Kiện</button>
                        <a href="{{ route('events.list') }}" class="btn btn-secondary ml-2">Hủy</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
