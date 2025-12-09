<x-app-layout>
    <div class="space-y-8">
        <div>
            {{-- Breadcrumb: Thay thế bằng dữ liệu tĩnh tạm thời, bạn có thể truyền động sau --}}
            @php
                $pageTitle = 'Quản Lý Tài Chính';
                $breadcrumbItems = [
                    ['url' => route('finance.index'), 'name' => 'Tài Chính Cá Nhân']
                ];
            @endphp
            <x-breadcrumb :page-title="$pageTitle" :breadcrumb-items="$breadcrumbItems" />
        </div>

        <div class="space-y-5">
            
            {{-- START::GROUP CARD THỐNG KÊ (Mục 1.1) --}}
            <div class="grid md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6">
                
                {{-- CARD: Tổng Thu Nhập --}}
                <div class="card bg-success-500 bg-opacity-10">
                    <div class="card-body pt-4 pb-3 px-4">
                        <div class="flex space-x-3 rtl:space-x-reverse">
                            <div class="flex-none">
                                <div class="h-12 w-12 rounded-full flex flex-col items-center justify-center text-2xl bg-success-500 text-white">
                                    <iconify-icon icon="heroicons:arrow-up-circle"></iconify-icon>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="text-slate-600 dark:text-slate-300 text-sm mb-1 font-medium">
                                    Tổng Thu Nhập
                                </div>
                                <div class="text-slate-900 dark:text-white text-lg font-bold">
                                    {{ number_format($totalIncome, 0, ',', '.') }} VND
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD: Tổng Chi Tiêu --}}
                <div class="card bg-danger-500 bg-opacity-10">
                    <div class="card-body pt-4 pb-3 px-4">
                        <div class="flex space-x-3 rtl:space-x-reverse">
                            <div class="flex-none">
                                <div class="h-12 w-12 rounded-full flex flex-col items-center justify-center text-2xl bg-danger-500 text-white">
                                    <iconify-icon icon="heroicons:arrow-down-circle"></iconify-icon>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="text-slate-600 dark:text-slate-300 text-sm mb-1 font-medium">
                                    Tổng Chi Tiêu
                                </div>
                                <div class="text-slate-900 dark:text-white text-lg font-bold">
                                    {{ number_format($totalExpense, 0, ',', '.') }} VND
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD: Số Dư Hiện Tại --}}
                <div class="card bg-info-500 bg-opacity-10">
                    <div class="card-body pt-4 pb-3 px-4">
                        <div class="flex space-x-3 rtl:space-x-reverse">
                            <div class="flex-none">
                                <div class="h-12 w-12 rounded-full flex flex-col items-center justify-center text-2xl 
                                    @if($currentBalance < 0) bg-danger-500 @else bg-info-500 @endif text-white">
                                    <iconify-icon icon="heroicons:wallet"></iconify-icon>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="text-slate-600 dark:text-slate-300 text-sm mb-1 font-medium">
                                    Số Dư Hiện Tại
                                </div>
                                <div class="text-slate-900 dark:text-white text-lg font-bold 
                                    @if($currentBalance < 0) text-danger-500 @else text-info-500 @endif">
                                    {{ number_format($currentBalance, 0, ',', '.') }} VND
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- END::GROUP CARD THỐNG KÊ --}}
            
            {{-- BIỂU ĐỒ THỐNG KÊ --}}
           {{-- <div class="card">
                <header class="card-header">
                    <h4 class="card-title">Thống Kê Thu/Chi Theo Tuần/Tháng</h4>
                </header>
            
                        <canvas id="financeBarChart" class="h-64"></canvas>
                   
               
            </div> --}}

            {{-- DANH SÁCH GIAO DỊCH --}}
            <div class="card">
                <header class="card-header">
                    <h4 class="card-title">Danh Sách Giao Dịch</h4>
                    <a href="{{ route('finance.create') }}" class="btn inline-flex justify-center btn-dark btn-sm">
                        <iconify-icon icon="heroicons:plus" class="text-lg ltr:mr-1 rtl:ml-1"></iconify-icon>
                        Thêm Giao Dịch
                    </a>
                </header>
                <div class="card-body px-6 pt-4 pb-4">

                    {{-- Form Lọc/Tìm kiếm (Mục 1.3) --}}
                    @include('dashboards.finance._filter_form')

                    <div class="overflow-x-auto -mx-6">
                        <div class="inline-block min-w-full align-middle">
                            <div class="overflow-hidden ">
                                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                                    <thead class="bg-slate-200 dark:bg-slate-700">
                                        <tr>
                                            <th scope="col" class="table-th w-[100px]">Ngày</th>
                                            <th scope="col" class="table-th">Người Thực Hiện</th>
                                            <th scope="col" class="table-th">Loại Biến Động</th>
                                            <th scope="col" class="table-th">Hạng Mục (Category)</th>
                                             <th scope="col" class="table-th">Notes</th>
                                            <th scope="col" class="table-th text-right w-[150px]">Số Tiền (VND)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                        @forelse ($transactions as $transaction)
                                        <tr>
                                            <td class="table-td text-slate-900 dark:text-slate-300">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                            <td class="table-td font-medium text-slate-700 dark:text-slate-300">{{ $transaction->user->name ?? 'N/A' }}</td>
                                            <td class="table-td">
                                                <span class="badge 
                                                    @if($transaction->type == 'income') bg-success-500 @else bg-danger-500 @endif
                                                    bg-opacity-20 text-xs font-semibold 
                                                    @if($transaction->type == 'income') text-success-500 @else text-danger-500 @endif">
                                                    {{ $transaction->type_label }}
                                                </span>
                                            </td>
                                            <td class="table-td text-sm text-slate-600 dark:text-slate-400">
                                                {{ $transaction->category }}
                                            </td>
                                            <td class="table-td text-sm text-slate-600 dark:text-slate-400">
                                                {{ $transaction->description ?: 'none' }}
                                            </td>

                                            <td class="table-td text-right font-bold text-base 
                                                @if($transaction->type == 'income') text-success-600 @else text-danger-600 @endif">
                                                {{ number_format($transaction->amount, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="table-td text-center text-slate-500 py-6">
                                                <iconify-icon icon="heroicons:folder-open" class="text-3xl block mx-auto mb-2"></iconify-icon>
                                                Không có giao dịch nào được tìm thấy.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Phân trang --}}
                    <div class="mt-6">
                        {{ $transactions->appends(request()->except('page'))->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>

</x-app-layout>


{{-- File partial cho Form Lọc --}}
<div style="display: none">
  
       @include('dashboards.finance._filter_form')
 
</div>