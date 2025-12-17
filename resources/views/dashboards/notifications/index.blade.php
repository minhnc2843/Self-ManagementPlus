<x-app-layout>
    <div class="space-y-8">
        <div class="flex justify-between items-center">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Thông Báo Của Bạn
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
            <div class="card-body px-6 pb-6">
                <div class="overflow-x-auto -mx-6 dashcode-data-table">
                    <span class="col-span-8 hidden"></span>
                    <span class="col-span-4 hidden"></span>
                    
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700 table-fixed dark:divide-opacity-20">
                                <thead class="bg-slate-200 dark:bg-slate-700">
                                    <tr>
                                        <th scope="col" class="table-th w-[50px]">Trạng thái</th>
                                        <th scope="col" class="table-th">Nội dung thông báo</th>
                                        <th scope="col" class="table-th w-[200px]">Thời gian</th>
                                        <th scope="col" class="table-th w-[100px]">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @forelse ($notifications as $notification)
                                        @php
                                            $data = $notification->data;
                                            $isRead = !is_null($notification->read_at);
                                            $rowClass = $isRead ? 'opacity-60' : 'bg-slate-50 dark:bg-slate-700/20';
                                            $iconColor = match($data['color'] ?? 'info') {
                                                'success' => 'text-success-500 bg-success-500',
                                                'warning' => 'text-warning-500 bg-warning-500',
                                                'danger' => 'text-danger-500 bg-danger-500',
                                                default => 'text-info-500 bg-info-500',
                                            };
                                        @endphp
                                        <tr class="hover:bg-slate-200 dark:hover:bg-slate-700 {{ $rowClass }} transition duration-150">
                                            <td class="table-td text-center">
                                                @if(!$isRead)
                                                    <span class="inline-block w-3 h-3 rounded-full bg-danger-500 ring-2 ring-white"></span>
                                                @else
                                                    <iconify-icon icon="heroicons-outline:check" class="text-xl text-slate-400"></iconify-icon>
                                                @endif
                                            </td>
                                            <td class="table-td">
                                                <a href="{{ route('notifications.read', $notification->id) }}" class="flex items-center">
                                                    <div class="flex-none">
                                                        <div class="w-10 h-10 rounded-full text-white flex flex-col items-center justify-center text-lg {{ $iconColor }}">
                                                            <iconify-icon icon="{{ $data['icon'] ?? 'heroicons-outline:bell' }}"></iconify-icon>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1 ltr:pl-4 rtl:pr-4">
                                                        <div class="text-slate-900 dark:text-slate-300 text-sm font-medium mb-1">
                                                            {{ $data['title'] ?? 'Thông báo hệ thống' }}
                                                        </div>
                                                        <div class="text-slate-600 dark:text-slate-400 text-xs">
                                                            {{ $data['message'] ?? '' }}
                                                        </div>
                                                    </div>
                                                </a>
                                            </td>
                                            <td class="table-td text-sm text-slate-500">
                                                {{ $notification->created_at->diffForHumans() }}
                                                <div class="text-xs text-slate-400">{{ $notification->created_at->format('H:i d/m/Y') }}</div>
                                            </td>
                                            <td class="table-td">
                                                <a href="{{ route('notifications.read', $notification->id) }}" class="btn btn-sm inline-flex justify-center btn-outline-secondary rounded-[25px]">
                                                    Xem
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="table-td text-center py-10 text-slate-500">
                                                <div class="flex flex-col items-center justify-center">
                                                    <iconify-icon icon="heroicons-outline:inbox" class="text-6xl mb-3 text-slate-300"></iconify-icon>
                                                    <span>Bạn chưa có thông báo nào.</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>