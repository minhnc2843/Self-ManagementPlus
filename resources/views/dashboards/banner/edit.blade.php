<x-app-layout>
    <div class="max-w-2xl mx-auto mt-10">
        <div class="card bg-white dark:bg-slate-800 shadow-xl rounded-xl overflow-hidden">
            <div class="card-header p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <iconify-icon icon="heroicons:photo" class="text-blue-600"></iconify-icon>
                    Cập Nhật Giao Diện Dashboard
                </h3>
            </div>
            
            <form action="{{ route('dashboard.update-banner') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                
                {{-- Title --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tiêu đề chính</label>
                    <input type="text" name="banner_title" value="{{ $settings->banner_title }}" required class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                </div>

                {{-- Image Upload --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Ảnh nền mới</label>
                    <input type="file" name="banner_image" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-slate-700 dark:file:text-slate-300"/>
                    @if($settings->banner_path)
                        <div class="mt-2 text-xs text-slate-500">
                            Hiện tại: <a href="{{ asset('storage/' . $settings->banner_path) }}" target="_blank" class="text-blue-500 hover:underline">Xem ảnh cũ</a>
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <button type="submit" class="btn bg-slate-900 text-white hover:bg-slate-800 px-6 py-2.5 rounded-lg shadow-lg shadow-slate-500/30 transition-all">
                        <iconify-icon icon="heroicons:check" class="mr-2"></iconify-icon> Lưu thay đổi
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 px-6 py-2.5 rounded-lg transition-all">
                        Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>