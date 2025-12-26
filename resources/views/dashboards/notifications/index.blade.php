<x-app-layout>
    <div class="space-y-8">
        <div class="flex justify-between items-center">
            <h4 class="font-medium lg:text-2xl text-xl capitalize inline-block ltr:pr-4 rtl:pl-4 text-primary-500">
               <iconify-icon icon="heroicons-outline:bell" class="text-xl ltr:mr-2 rtl:ml-2"></iconify-icon> Thông Báo Của Bạn
            </h4>
            <div class="flex space-x-2">
                 <a href="{{ route('notifications.readAll') }}" class="btn inline-flex justify-center btn-outline-dark btn-sm rounded-[25px]">
                    <span class="flex items-center">
                        <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="heroicons-outline:check-circle"></iconify-icon>
                        <span>Đánh dấu tất cả đã đọc</span>
                    </span>
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <ul class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($notifications as $notification)
                        @php
                            $data = $notification->data;
                            $isRead = !is_null($notification->read_at);
                            // Giữ logic class như cũ: đã đọc thì mờ đi, chưa đọc thì có nền nổi bật
                            $rowClass = $isRead ? 'opacity-60 bg-white dark:bg-slate-800' : 'bg-slate-50 dark:bg-slate-700/20';
                            
                            $iconColor = match($data['color'] ?? 'info') {
                                'success' => 'text-success-500 bg-success-500',
                                'warning' => 'text-warning-500 bg-warning-500',
                                'danger' => 'text-danger-500 bg-danger-500',
                                default => 'text-info-500 bg-info-500',
                            };
                        @endphp
                        <li>
                            <a href="{{ route('notifications.read', $notification->id) }}" class="block p-4 hover:bg-slate-100 dark:hover:bg-slate-700 transition duration-150 {{ $rowClass }}">
                                <div class="flex items-start space-x-4 rtl:space-x-reverse">
                                    
                                    {{-- Phần 1: Trạng thái (Đã đọc/Chưa đọc) - Giữ vị trí đầu tiên --}}
                                    <div class="flex-none w-[20px] pt-3 flex justify-center">
                                        @if(!$isRead)
                                            <span class="inline-block w-3 h-3 rounded-full bg-danger-500 ring-2 ring-white dark:ring-slate-700" title="Chưa đọc"></span>
                                        @else
                                            <iconify-icon icon="heroicons-outline:check" class="text-xl text-slate-400" title="Đã đọc"></iconify-icon>
                                        @endif
                                    </div>

                                    {{-- Phần 2: Icon loại thông báo --}}
                                    <div class="flex-none">
                                        <div class="w-10 h-10 rounded-full text-white flex flex-col items-center justify-center text-lg {{ $iconColor }}">
                                            <iconify-icon icon="{{ $data['icon'] ?? 'heroicons-outline:bell' }}"></iconify-icon>
                                        </div>
                                    </div>

                                    {{-- Phần 3: Nội dung chính --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start">
                                            <div class="text-slate-900 dark:text-slate-300 text-sm font-medium mb-1 break-words ltr:pr-2 rtl:pl-2">
                                                {{ $data['title'] ?? 'Thông báo hệ thống' }}
                                            </div>
                                            
                                            {{-- Thời gian (Tương đối) --}}
                                            <div class="text-xs text-slate-500 whitespace-nowrap hidden sm:block">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                        
                                        <div class="text-slate-600 dark:text-slate-400 text-xs mb-1 line-clamp-2">
                                            {{ $data['message'] ?? '' }}
                                        </div>

                                        {{-- Thời gian chi tiết + Thời gian tương đối (cho mobile) --}}
                                        <div class="flex items-center justify-between mt-1">
                                            <div class="text-xs text-slate-400">
                                                {{ $notification->created_at->format('H:i d/m/Y') }}
                                            </div>
                                            {{-- Mobile only: Hiển thị thời gian tương đối ở dưới cùng nếu màn hình nhỏ --}}
                                            <div class="text-xs text-slate-400 sm:hidden">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="p-10 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-500">
                                <iconify-icon icon="heroicons-outline:inbox" class="text-6xl mb-3 text-slate-300"></iconify-icon>
                                <span>Bạn chưa có thông báo nào.</span>
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
        
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </div>
</x-app-layout>