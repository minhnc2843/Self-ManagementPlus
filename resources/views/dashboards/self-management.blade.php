<x-app-layout>
    {{-- Main Container with Alpine Data for Modals --}}
    <div class="space-y-6" x-data="{ 
        bannerModal: false,
        editGoalModal: false,
        currentGoal: { id: null, title: '', progress: 0, color: 'primary' },
        openEditGoal(goal) {
            this.currentGoal = goal;
            this.editGoalModal = true;
        }
    }">
        
        {{-- SECTION 1: BANNER & HEADER --}}
        <div class="relative rounded-2xl overflow-hidden shadow-xl bg-slate-900 min-h-[280px] flex items-center group">
            {{-- Background Image --}}
            @if($settings->banner_path)
                <img src="{{ asset('storage/' . $settings->banner_path) }}" class="absolute inset-0 w-full h-full object-cover opacity-60 transition-transform duration-700 group-hover:scale-105">
            @else
                <img src="{{ asset('images/all-img/widget-bg-2.png') }}" class="absolute inset-0 w-full h-full object-cover opacity-60 transition-transform duration-700 group-hover:scale-105">
            @endif
            
            {{-- Content --}}
            <div class="relative z-10 px-8 md:px-12 w-full flex flex-col md:flex-row justify-between items-end md:items-center">
                <div class="max-w-2xl">
                    <h2 class="text-3xl md:text-5xl font-black text-white tracking-tight uppercase drop-shadow-lg mb-2">
                        {{ $settings->banner_title }}
                    </h2>
                    <p class="text-white/90 text-lg font-light italic border-l-4 border-blue-500 pl-4">
                        "{{ $settings->banner_quote ?? 'Kỷ luật là cầu nối giữa mục tiêu và thành tựu.' }}"
                    </p>
                </div>
                
                {{-- Edit Banner Button --}}
                <button @click="bannerModal = true" class="mt-6 md:mt-0 flex items-center gap-2 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white px-4 py-2 rounded-full border border-white/20 transition-all shadow-lg">
                    <iconify-icon icon="heroicons:pencil-square" class="text-xl"></iconify-icon>
                    <span class="text-sm font-medium">Cập nhật giao diện</span>
                </button>
            </div>
        </div>

        {{-- SECTION 2: GOALS & PRIORITIES --}}
        <div class="grid grid-cols-12 gap-6">
            
            {{-- LEFT: YEARLY GOALS --}}
            <div class="col-span-12 lg:col-span-7 xl:col-span-8">
                <div class="card h-full bg-white dark:bg-slate-800 shadow-md border border-slate-200 dark:border-slate-700">
                    <div class="card-header p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                        <div>
                            <h4 class="text-lg font-bold text-slate-800 dark:text-white uppercase tracking-wider">🎯 Mục Tiêu Năm Nay</h4>
                            <p class="text-xs text-slate-500 mt-1">Theo dõi tiến độ các mục tiêu lớn của bạn</p>
                        </div>
                        <a href="{{ route('goals.create') }}" class="btn btn-sm bg-slate-900 text-white hover:bg-slate-800 shadow-lg shadow-slate-500/30">
                            <iconify-icon icon="heroicons:plus" class="mr-1"></iconify-icon> Thêm mới
                        </a>
                    </div>
                    
                    <div class="card-body p-5 space-y-5">
                        @forelse($yearlyGoals as $goal)
                            <div class="group bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700 hover:border-blue-200 transition-all">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full bg-{{ $goal->color }}-100 text-{{ $goal->color }}-600 flex items-center justify-center">
                                            <iconify-icon icon="heroicons:trophy" class="text-xl"></iconify-icon>
                                        </div>
                                        <div>
                                            <h5 class="font-bold text-slate-700 dark:text-slate-200">{{ $goal->title }}</h5>
                                            <span class="text-xs text-slate-500">Deadline: {{ $goal->deadline ? \Carbon\Carbon::parse($goal->deadline)->format('d/m/Y') : 'Không thời hạn' }}</span>
                                        </div>
                                    </div>
                                    
                                    {{-- Actions --}}
                                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="openEditGoal({{ json_encode($goal) }})" class="p-2 rounded-full hover:bg-slate-200 text-slate-500 transition">
                                            <iconify-icon icon="heroicons:pencil"></iconify-icon>
                                        </button>
                                        <form action="{{ route('goals.destroy', $goal->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa mục tiêu này?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 rounded-full hover:bg-red-100 text-red-500 transition">
                                                <iconify-icon icon="heroicons:trash"></iconify-icon>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Progress Bar --}}
                                <div class="relative pt-2">
                                    <div class="flex justify-between text-xs font-semibold mb-1 text-slate-500">
                                        <span>Tiến độ</span>
                                        <span class="text-{{ $goal->color }}-600">{{ $goal->progress }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-{{ $goal->color }}-500 h-2.5 rounded-full transition-all duration-1000" style="width: {{ $goal->progress }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <img src="{{ asset('images/svg/empty.svg') }}" class="h-32 mx-auto opacity-50 mb-4" alt="Empty">
                                <p class="text-slate-500">Chưa có mục tiêu nào. Hãy đặt mục tiêu ngay!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- RIGHT: URGENT TASKS --}}
            <div class="col-span-12 lg:col-span-5 xl:col-span-4">
                <div class="card h-full bg-white dark:bg-slate-800 shadow-md border-t-4 border-red-500">
                    <div class="card-header p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-red-50 dark:bg-slate-900/50">
                        <h4 class="text-lg font-bold text-red-600 uppercase"><iconify-icon icon="heroicons:fire" class="mr-1 inline"></iconify-icon> Việc Khẩn Cấp</h4>
                        <a href="{{ route('plans.create') }}" class="text-xs font-bold text-red-600 hover:underline">+ Thêm việc</a>
                    </div>
                    <div class="card-body p-0">
                        <ul class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($topPriorities as $plan)
                                <li class="p-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition flex items-start gap-3 group">
                                    <div class="pt-1">
                                        <input type="checkbox" 
                                               onchange="togglePlan({{ $plan->id }})" 
                                               {{ $plan->status == 'completed' ? 'checked' : '' }} 
                                               class="h-5 w-5 text-red-600 focus:ring-red-500 border-gray-300 rounded cursor-pointer">
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold {{ $plan->status == 'completed' ? 'line-through text-slate-400' : 'text-slate-700 dark:text-slate-200' }}">
                                            {{ $plan->title }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[10px] px-2 py-0.5 rounded bg-red-100 text-red-600 font-bold">Ưu tiên cao</span>
                                            <span class="text-xs text-slate-400">{{ $plan->start_time->format('H:i d/m') }}</span>
                                        </div>
                                    </div>
                                    <form action="{{ route('plans.destroy', $plan->id) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-red-500">
                                            <iconify-icon icon="heroicons:x-mark" class="text-lg"></iconify-icon>
                                        </button>
                                    </form>
                                </li>
                            @empty
                                <li class="p-8 text-center text-slate-400 italic">
                                    Tuyệt vời! Bạn không có việc gấp nào.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 3: WEEKLY SCHEDULE --}}
        <div class="card bg-white dark:bg-slate-800 shadow-md">
            <div class="card-header p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                <h4 class="font-bold text-slate-700 dark:text-white uppercase"><iconify-icon icon="heroicons:calendar-days" class="mr-2 inline"></iconify-icon> Lịch Trình Tuần Này</h4>
                <a href="{{ route('plans.create') }}" class="btn btn-sm btn-outline-dark">Lên lịch chi tiết</a>
            </div>
            <div class="card-body p-5">
                <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
                    @php 
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']; 
                        $dayLabels = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ Nhật'];
                    @endphp
                    @foreach($days as $index => $day)
                        <div class="flex flex-col h-full">
                            <div class="text-center py-2 bg-slate-100 dark:bg-slate-700 rounded-t-lg font-bold text-slate-600 dark:text-slate-300 text-sm uppercase">
                                {{ $dayLabels[$index] }}
                            </div>
                            <div class="flex-1 border border-t-0 border-slate-100 dark:border-slate-700 rounded-b-lg p-2 min-h-[100px] bg-slate-50/50 dark:bg-slate-900/20">
                                @if(isset($weeklyPlans[$day]))
                                    @foreach($weeklyPlans[$day] as $t)
                                        <div class="group relative bg-white dark:bg-slate-800 p-2 mb-2 rounded shadow-sm border-l-4 {{ $t->is_priority ? 'border-red-500' : 'border-blue-500' }} text-xs">
                                            <p class="font-medium truncate">{{ $t->title }}</p>
                                            <span class="text-[10px] text-slate-400">{{ $t->start_time->format('H:i') }}</span>
                                            
                                            <form action="{{ route('plans.destroy', $t->id) }}" method="POST" class="absolute top-1 right-1 hidden group-hover:block">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700"><iconify-icon icon="heroicons:trash" width="12"></iconify-icon></button>
                                            </form>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- MODAL: UPDATE BANNER --}}
        <div x-show="bannerModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div @click.away="bannerModal = false" class="bg-white dark:bg-slate-800 w-full max-w-md rounded-xl shadow-2xl overflow-hidden animate-fade-in-up">
                <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="font-bold text-lg">Cập nhật giao diện</h3>
                    <button @click="bannerModal = false" class="text-slate-400 hover:text-slate-600"><iconify-icon icon="heroicons:x-mark" class="text-xl"></iconify-icon></button>
                </div>
                <form action="{{ route('dashboard.update-banner') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tiêu đề chính</label>
                        <input type="text" name="banner_title" value="{{ $settings->banner_title }}" class="form-input w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Ảnh nền mới</label>
                        <input type="file" name="banner_image" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="btn btn-dark w-full">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL: EDIT GOAL --}}
        <div x-show="editGoalModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div @click.away="editGoalModal = false" class="bg-white dark:bg-slate-800 w-full max-w-md rounded-xl shadow-2xl overflow-hidden animate-fade-in-up">
                <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-blue-50 dark:bg-slate-900">
                    <h3 class="font-bold text-lg text-blue-700">Chỉnh sửa mục tiêu</h3>
                    <button @click="editGoalModal = false" class="text-slate-400 hover:text-slate-600"><iconify-icon icon="heroicons:x-mark" class="text-xl"></iconify-icon></button>
                </div>
                
                {{-- Dynamic Form Action using x-bind --}}
                <form :action="`{{ url('/dashboard/goals') }}/${currentGoal.id}`" method="POST" class="p-5 space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tên mục tiêu</label>
                        <input type="text" name="title" x-model="currentGoal.title" required class="form-input w-full rounded-lg border-slate-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tiến độ (%)</label>
                        <div class="flex items-center gap-3">
                            <input type="range" name="progress" x-model="currentGoal.progress" min="0" max="100" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                            <span class="font-bold text-blue-600 w-10" x-text="currentGoal.progress + '%'"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Màu sắc</label>
                        <select name="color" x-model="currentGoal.color" class="form-select w-full rounded-lg border-slate-300">
                            <option value="primary">Xanh dương</option>
                            <option value="success">Xanh lá</option>
                            <option value="danger">Đỏ</option>
                            <option value="warning">Vàng</option>
                        </select>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" @click="editGoalModal = false" class="btn btn-light flex-1">Hủy</button>
                        <button type="submit" class="btn btn-primary flex-1">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function togglePlan(id) {
             fetch(`/dashboard/plans/${id}/toggle`, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Optional: Show toast notification here
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</x-app-layout>