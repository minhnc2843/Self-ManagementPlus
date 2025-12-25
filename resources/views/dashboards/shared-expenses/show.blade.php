<x-app-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-end">
            <div>
                <h4 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                    <iconify-icon icon="heroicons-outline:user-group" class="text-primary-500"></iconify-icon>
                    {{ $group->name }}
                </h4>
                <p class="text-slate-500 text-sm mt-1">{{ $group->description }}</p>
            </div>
            <div>
                <a href="{{ route('expense-groups.add-expense-view', $group->id) }}" class="btn btn-primary btn-sm shadow-lg hover:shadow-xl transition-all">
                    <iconify-icon icon="heroicons-outline:plus" class="mr-1"></iconify-icon> Thêm khoản chi
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($memberStats as $stat)
            <div class="card border border-slate-200 shadow-none dark:border-slate-700">
                <div class="card-body p-4">
                    <div class="flex items-center mb-3">
                        <div class="flex-none mr-3">
                            <img src="{{ $stat['avatar'] ?? asset('images/all-img/user.png') }}" class="w-10 h-10 rounded-full object-cover">
                        </div>
                        <div class="flex-1">
                            <h5 class="text-sm font-bold text-slate-900 dark:text-slate-300">{{ $stat['name'] }}</h5>
                            @if($stat['balance'] > 0)
                                <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-0.5 rounded-full inline-flex items-center mt-1">
                                    <iconify-icon icon="heroicons-outline:arrow-trending-up" class="mr-1"></iconify-icon>
                                    Nhận lại: {{ number_format($stat['balance']) }}
                                </span>
                            @elseif($stat['balance'] < 0)
                                <span class="text-xs font-bold text-red-600 bg-red-100 px-2 py-0.5 rounded-full inline-flex items-center mt-1">
                                    <iconify-icon icon="heroicons-outline:arrow-trending-down" class="mr-1"></iconify-icon>
                                    Trả thêm: {{ number_format(abs($stat['balance'])) }}
                                </span>
                            @else
                                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full mt-1">
                                    Đã cân bằng
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex justify-between text-xs border-t border-slate-100 dark:border-slate-700 pt-3">
                        <div class="text-center w-1/2 border-r border-slate-100 dark:border-slate-700">
                            <span class="block text-slate-500 mb-1">Đã chi ra</span>
                            <span class="block font-semibold text-slate-700 dark:text-slate-300">{{ number_format($stat['paid']) }}</span>
                        </div>
                        <div class="text-center w-1/2">
                            <span class="block text-slate-500 mb-1">Trách nhiệm</span>
                            <span class="block font-semibold text-slate-700 dark:text-slate-300">{{ number_format($stat['share']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="card">
            <header class="card-header border-b border-slate-100 dark:border-slate-700 flex justify-between items-center py-4 px-6">
                <h4 class="card-title flex items-center gap-2">
                    <iconify-icon icon="heroicons-outline:receipt-refund" class="text-xl"></iconify-icon>
                    Lịch sử chi tiêu
                </h4>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Tổng chi tiêu nhóm:</span>
                    <div class="flex items-center gap-1 text-red-600 dark:text-red-500 bg-red-50 dark:bg-red-500/10 px-3 py-1 rounded-md">
                        <iconify-icon icon="heroicons-outline:chart-bar-square" class="text-lg"></iconify-icon>
                        <b class="text-base font-bold">{{ number_format($group->expenses->sum('total_amount')) }} đ</b>
                    </div>
                </div>
            </header>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700 border-separate border-spacing-y-3" style="border-spacing: 0 10px;">
                        <thead class="bg-slate-50 dark:bg-slate-800">
                            <tr>
                                <th class="table-th text-left px-4 py-3">Ngày & Nội dung</th>
                                <th class="table-th px-4 py-3" style="text-align: right;">Tổng bill</th>
                                <th class="table-th text-left px-4 py-3">Người trả tiền (Chi)</th>
                                <th class="table-th text-left px-4 py-3">Phân bổ (Chia)</th>
                              
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-900 dark:divide-slate-700">
                            @forelse($expenses as $expense)
                            <tr class="bg-white dark:bg-slate-800 shadow-sm rounded-md hover:shadow-md transition-all duration-200 group">
                                    <td class="table-td px-4 py-3 align-top">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2 mb-1">
                                            <div class="h-8 w-8 rounded bg-primary-100 text-primary-600 flex flex-col items-center justify-center text-[10px] font-bold leading-tight">
                                                <span>{{ $expense->date->format('d') }}</span>
                                                <span>{{ $expense->date->format('M') }}</span>
                                            </div>
                                            <span class="font-bold text-slate-900 text-sm">{{ $expense->title }}</span>
                                        </div>
                                        <span class="text-xs text-slate-400 pl-10">Tạo bởi: {{ $expense->creator->name ?? 'N/A' }}</span>
                                    </div>
                                </td>

                                <td class="table-td px-4 py-3 text-right align-top">
                                    <span class="font-bold text-base text-slate-900 block">{{ number_format($expense->total_amount) }}</span>
                                </td>

                                <td class="table-td px-4 py-3 align-top">
                                    <div class="space-y-1">
                                        @foreach($expense->payers as $payer)
                                        <div class="flex items-center justify-between bg-slate-50 rounded p-1 px-2 border border-slate-100">
                                            <div class="flex items-center gap-2">
                                                <img src="{{ $payer->user->avatar ?? asset('images/all-img/user.png') }}" class="w-5 h-5 rounded-full">
                                                <span class="text-xs font-medium">{{ $payer->user->name }}</span>
                                            </div>
                                            <span class="text-xs font-bold text-blue-600">{{ number_format($payer->amount_paid) }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </td>

                                <td class="table-td px-4 py-3 align-top">
                                    <div class="mb-2 text-xs text-slate-500 font-medium flex items-center gap-1">
                                        <iconify-icon icon="heroicons-outline:calculator"></iconify-icon>
                                        @php
                                            $firstShare = $expense->shares->first();
                                            $isEqual = $expense->shares->every(fn($s) => $s->amount_owed == $firstShare->amount_owed);
                                        @endphp
                                        
                                        @if($isEqual && $firstShare)
                                            Chia đều: <span class="text-slate-900 font-bold">{{ number_format($firstShare->amount_owed) }} /người</span>
                                        @else
                                            Chia theo tỷ lệ khác nhau
                                        @endif
                                    </div>

                                    <div class="flex flex-wrap gap-1">
                                        @foreach($expense->shares as $share)
                                            <div class="relative group/avatar cursor-pointer">
                                                <img class="inline-block h-6 w-6 rounded-full ring-1 ring-white grayscale opacity-70 hover:grayscale-0 hover:opacity-100 transition-all" 
                                                     src="{{ $share->user->avatar ?? asset('images/all-img/user.png') }}" 
                                                     alt="{{ $share->user->name }}">
                                                
                                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 hidden group-hover/avatar:block bg-black text-white text-[10px] py-1 px-2 rounded whitespace-nowrap z-10">
                                                    {{ $share->user->name }}: -{{ number_format($share->amount_owed) }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>

                               
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-12">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="h-24 w-24 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                            <iconify-icon icon="heroicons-outline:currency-dollar" class="text-4xl text-slate-400"></iconify-icon>
                                        </div>
                                        <h5 class="text-slate-600 font-medium text-lg">Chưa có khoản chi tiêu nào</h5>
                                        <p class="text-slate-400 text-sm mt-1">Hãy thêm khoản chi đầu tiên để bắt đầu theo dõi.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                    {{ $expenses->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>