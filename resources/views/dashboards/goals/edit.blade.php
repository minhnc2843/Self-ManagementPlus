<x-app-layout>
    <div class="max-w-2xl mx-auto mt-12 px-4 sm:px-6">
        {{-- Card Container --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl
                    shadow-lg shadow-slate-200/60 dark:shadow-none
                    border border-slate-200 dark:border-slate-700
                    overflow-hidden">

            {{-- Header --}}
            <div class="px-8 py-6 border-b border-slate-200 dark:border-slate-700
                        bg-slate-50 dark:bg-slate-800/60
                        flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="h-11 w-11 rounded-xl
                                bg-blue-100 text-blue-600
                                dark:bg-blue-900/30 dark:text-blue-400
                                flex items-center justify-center
                                border border-blue-200 dark:border-blue-800">
                        <iconify-icon icon="heroicons:pencil-square" class="text-xl"></iconify-icon>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white leading-tight">
                            Chỉnh Sửa Mục Tiêu
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Cập nhật thông tin chi tiết
                        </p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form action="{{ route('goals.update', $goal->id) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')

                <div class="space-y-7">

                    {{-- Title --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Tên mục tiêu <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="title"
                            value="{{ old('title', $goal->title) }}"
                            required
                            placeholder="Nhập tên mục tiêu của bạn..."
                            class="w-full px-4 py-3 rounded-xl
                                   bg-white dark:bg-slate-800
                                   border border-slate-300 dark:border-slate-600
                                   text-slate-800 dark:text-slate-100
                                   placeholder-slate-400
                                   focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20
                                   transition-all duration-200"
                        >
                    </div>

                    {{-- Grid: Color & Progress --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Color --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                Màu đại diện
                            </label>

                            <div class="relative">
                                <select
                                    name="color"
                                    class="w-full px-4 py-3 rounded-xl
                                           bg-white dark:bg-slate-800
                                           border border-slate-300 dark:border-slate-600
                                           text-slate-800 dark:text-slate-100
                                           focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20
                                           appearance-none cursor-pointer
                                           transition-all duration-200">
                                    @foreach(['primary' => 'Xanh dương', 'success' => 'Xanh lá', 'danger' => 'Đỏ', 'warning' => 'Vàng', 'info' => 'Xanh nhạt'] as $value => $label)
                                        <option value="{{ $value }}" {{ $goal->color == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>

                                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400">
                                    <iconify-icon icon="heroicons:chevron-down" class="text-base"></iconify-icon>
                                </div>
                            </div>
                        </div>

                        {{-- Progress --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                Tiến độ (%)
                            </label>

                            <div class="relative">
                                <input
                                    type="number"
                                    name="progress"
                                    value="{{ old('progress', $goal->progress) }}"
                                    min="0"
                                    max="100"
                                    class="w-full px-4 py-3 pr-10 rounded-xl
                                           bg-white dark:bg-slate-800
                                           border border-slate-300 dark:border-slate-600
                                           text-slate-800 dark:text-slate-100
                                           focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20
                                           transition-all duration-200">
                                
                            </div>
                        </div>
                    </div>

                    {{-- Deadline --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Hạn chót
                        </label>

                        <div class="relative">
                            <input
                                type="date"
                                name="deadline"
                                value="{{ old('deadline', $goal->deadline ? \Carbon\Carbon::parse($goal->deadline)->format('Y-m-d') : '') }}"
                                class="w-full px-4 py-3 pr-10 rounded-xl
                                       bg-white dark:bg-slate-800
                                       border border-slate-300 dark:border-slate-600
                                       text-slate-800 dark:text-slate-100
                                       focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20
                                       transition-all duration-200">

                           
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="flex items-center justify-end gap-3 pt-8 mt-8
                            border-t border-slate-200 dark:border-slate-700">
                    <a href="{{ route('dashboard') }}"
                       class="px-5 py-2.5 rounded-xl text-sm font-medium
                              text-slate-600 dark:text-slate-300
                              bg-white dark:bg-slate-800
                              border border-slate-300 dark:border-slate-600
                              hover:bg-slate-50 dark:hover:bg-slate-700
                              transition-all focus:ring-4 focus:ring-slate-300/30">
                      <iconify-icon icon="heroicons:arrow-down-left"></iconify-icon>  Hủy bỏ
                    </a>

                    <button type="submit"
                            class="inline-flex items-center gap-2
                                   px-6 py-2.5 rounded-xl text-sm font-semibold
                                   text-white bg-slate-900
                                   hover:bg-slate-800 hover:shadow-lg hover:shadow-slate-900/20
                                   focus:ring-4 focus:ring-slate-900/30
                                   transition-all">
                        <iconify-icon icon="heroicons:check" class="text-lg"></iconify-icon>
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
