<x-app-layout>
    <div class="space-y-8">
        <div class="flex justify-between items-center">
            <x-breadcrumb :page-title="'Quản Lý Tài Chính'" :breadcrumb-items="[['url' => route('finance.index'), 'name' => 'Tài Chính']]" />
            <div class="flex space-x-2 rtl:space-x-reverse">
                <a href="{{ route('finance.index', ['period' => 'week']) }}" class="btn btn-sm {{ $period == 'week' ? 'btn-dark' : 'btn-outline-dark' }}">Tuần này</a>
                <a href="{{ route('finance.index', ['period' => 'month']) }}" class="btn btn-sm {{ $period == 'month' ? 'btn-dark' : 'btn-outline-dark' }}">Tháng này</a>
                <a href="{{ route('finance.index', ['period' => 'year']) }}" class="btn btn-sm {{ $period == 'year' ? 'btn-dark' : 'btn-outline-dark' }}">Năm này</a>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success light-mode">
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <iconify-icon class="text-2xl" icon="heroicons:check-circle"></iconify-icon>
                    <p class="flex-1 font-inter">{{ session('success') }}</p>
                    <button class="relative top-0 right-0 text-xl text-slate-900 dark:text-white" data-bs-dismiss="alert">
                        <iconify-icon icon="heroicons:x-mark"></iconify-icon>
                    </button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger light-mode">
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <iconify-icon class="text-2xl" icon="heroicons:exclamation-circle"></iconify-icon>
                    <p class="flex-1 font-inter">{{ session('error') }}</p>
                    <button class="relative top-0 right-0 text-xl text-slate-900 dark:text-white" data-bs-dismiss="alert">
                        <iconify-icon icon="heroicons:x-mark"></iconify-icon>
                    </button>
                </div>
            </div>
        @endif
        <div class="grid grid-cols-12 gap-5">
            {{-- Cột bên trái: Thống kê & Biểu đồ & Transaction --}}
            <div class="lg:col-span-7 col-span-12 space-y-5">
                <div class="card p-6">
                    {{-- Thống kê số liệu --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                        <div class="flex items-center space-x-3 rtl:space-x-reverse">
                            <div class="flex-none h-12 w-12 rounded-full flex flex-col items-center justify-center bg-success-500 bg-opacity-10 text-success-500">
                                <iconify-icon icon="heroicons:arrow-trending-up" class="text-2xl"></iconify-icon>
                            </div>
                            <div class="flex-1">
                                <div class="text-slate-600 dark:text-slate-300 text-sm mb-1 font-medium">Tổng Thu</div>
                                <div class="text-slate-900 dark:text-white text-lg font-bold">{{ number_format($totalIncome) }} đ</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3 rtl:space-x-reverse">
                            <div class="flex-none h-12 w-12 rounded-full flex flex-col items-center justify-center bg-danger-500 bg-opacity-10 text-danger-500">
                                <iconify-icon icon="heroicons:arrow-trending-down" class="text-2xl"></iconify-icon>
                            </div>
                            <div class="flex-1">
                                <div class="text-slate-600 dark:text-slate-300 text-sm mb-1 font-medium">Tổng Chi</div>
                                <div class="text-slate-900 dark:text-white text-lg font-bold">{{ number_format($totalExpense) }} đ</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3 rtl:space-x-reverse">
                            <div class="flex-none h-12 w-12 rounded-full flex flex-col items-center justify-center bg-info-500 bg-opacity-10 text-info-500">
                                <iconify-icon icon="heroicons:wallet" class="text-2xl"></iconify-icon>
                            </div>
                            <div class="flex-1">
                                <div class="text-slate-600 dark:text-slate-300 text-sm mb-1 font-medium">Số Dư</div>
                                <div class="text-slate-900 dark:text-white text-lg font-bold">{{ number_format($currentBalance) }} đ</div>
                            </div>
                        </div>
                    </div>

                    {{-- Biểu đồ (Updated ID) --}}
                    <div class="legend-ring overflow-x-auto">
                      <div id="revenue-barchart" class="min-w-[600px]"
                        data-income="{{ json_encode($chartData['income']) }}"
                        data-expense="{{ json_encode($chartData['expense']) }}"
                        data-labels="{{ json_encode($chartData['labels']) }}">
                    </div>
                    </div>
                </div>

                {{-- Danh sách giao dịch --}}
                <div class="card">
                    <header class="card-header noborder">
                        <h4 class="card-title">Giao dịch gần đây</h4>
                        <a href="{{ route('finance.create') }}" class="btn btn-sm btn-dark">Thêm mới</a>
                    </header>
                    <div class="card-body px-6 pb-6">
                        <div class="overflow-x-auto -mx-6">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden">
                                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                                        <thead class="bg-slate-200 dark:bg-slate-700">
                                            <tr>
                                                <th class="table-th">Ngày</th>
                                                <th class="table-th">Loại</th>
                                                <th class="table-th">Hạng mục</th>
                                                <th class="table-th text-right">Số tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                            @forelse ($transactions as $trans)
                                            <tr>
                                                <td class="table-td">{{ $trans->transaction_date->format('d/m/Y') }}</td>
                                                <td class="table-td">
                                                    <span class="badge {{ $trans->type == 'income' ? 'bg-success-500 text-success-500' : 'bg-danger-500 text-danger-500' }} bg-opacity-20 border-0">
                                                        {{ $trans->type == 'income' ? 'Thu' : 'Chi' }}
                                                    </span>
                                                </td>
                                                <td class="table-td">{{ $trans->category }}</td>
                                                <td class="table-td text-right font-bold {{ $trans->type == 'income' ? 'text-success-500' : 'text-danger-500' }}">
                                                    {{ number_format($trans->amount) }}
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="4" class="text-center py-4">Chưa có giao dịch</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">{{ $transactions->links() }}</div>
                    </div>
                </div>
            </div>

            {{-- Cột bên phải: QUẢN LÝ KHOẢN VAY --}}
            <div class="lg:col-span-5 col-span-12 space-y-5">
                <div class="card">
                    <header class="card-header flex justify-between items-center">
                        <h4 class="card-title">Quản Lý Khoản Vay</h4>
                        <button onclick="document.getElementById('loanModal').showModal()" class="btn btn-sm btn-outline-primary">
                            <iconify-icon icon="heroicons:plus"></iconify-icon> Thêm
                        </button>
                    </header>
                    <div class="card-body p-4">
                        {{-- Search Form cho Khoản vay --}}
                        <form method="GET" action="{{ route('finance.index') }}" class="mb-4">
                            <div class="relative">
                                <input type="text" name="loan_search" value="{{ request('loan_search') }}" 
                                       class="form-control pr-8" placeholder="Tìm tên hoặc mô tả...">
                                <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-500">
                                    <iconify-icon icon="heroicons:magnifying-glass"></iconify-icon>
                                </button>
                            </div>
                        </form>

                        <div class="space-y-4">
                            @forelse ($loans as $loan)
                            <div class="p-3 border border-slate-300 dark:border-slate-700 rounded-md bg-white dark:bg-slate-800 shadow-sm">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                            {{ $loan->contact_name }}
                                            @if($loan->status == 'paid')
                                                <span class="badge bg-success-500 text-white text-[10px] px-1 py-0.5 rounded">Đã tất toán</span>
                                            @else
                                                <span class="badge bg-warning-500 text-white text-[10px] px-1 py-0.5 rounded">Đang nợ</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-slate-500">
                                            <div>{{ $loan->type == 'lent' ? 'Tôi cho vay' : 'Tôi đi vay' }}</div>
                                            <div>Ngày vay: {{ $loan->loan_date ? $loan->loan_date->format('d/m/Y') : '' }}</div>
                                            @if($loan->due_date)
                                             <div>Hạn trả: {{ $loan->due_date->format('d/m/Y') }}</div>
                                            @endif
                                            @if($loan->status == 'paid')
                                             <div class="text-success-500">Hoàn tất: {{ $loan->updated_at->format('d/m/Y') }}</div>
                                            @endif
                                        </div>
                                        @if($loan->description)
                                            <div class="text-xs text-slate-400 italic mt-1">{{Str::limit($loan->description, 30)}}</div>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold {{ $loan->type == 'lent' ? 'text-blue-500' : 'text-orange-500' }}">
                                            {{ number_format($loan->amount) }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Progress Bar thanh toán --}}
                                <div class="w-full md:w-1/2">
                                    <div class="w-full bg-slate-200 rounded-full h-2.5 mb-2 dark:bg-slate-700">
                                        @php $percent = $loan->amount > 0 ? ($loan->paid_amount / $loan->amount) * 100 : 0; @endphp
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs mb-3">
                                        <span>Đã xong: {{ number_format($loan->paid_amount) }}</span>
                                        <span>Còn lại: {{ number_format($loan->remaining_amount) }}</span>
                                    </div>
                                </div>

                                {{-- Nút hành động --}}
                                @if($loan->status != 'paid')
                                <div class="flex justify-end">
                                    <button onclick="openPaymentModal({{ $loan->id }}, '{{ $loan->contact_name }}', {{ $loan->remaining_amount }})" 
                                            class="btn btn-sm btn-dark py-1 px-3">
                                        Cập nhật / Tất toán
                                    </button>
                                </div>
                                @endif
                            </div>
                            @empty
                            <div class="text-center text-slate-500 text-sm py-4">Không tìm thấy khoản vay nào</div>
                            @endforelse
                        </div>
                        <div class="mt-4">
                            {{ $loans->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TẠO MỚI --}}
    <dialog id="loanModal" class="modal rounded-lg shadow-xl w-full max-w-lg p-0 bg-white dark:bg-slate-800 overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-700">
            <h3 class="font-bold text-lg">Thêm Khoản Vay Mới</h3>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('finance.loans.store') }}">
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Loại hình <span class="text-red-500">*</span></label>
                            <select name="type" class="form-control w-full border rounded p-2">
                                <option value="lent">Tôi cho vay (Tiền ra)</option>
                                <option value="borrowed">Tôi đi vay (Tiền vào)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Người liên quan <span class="text-red-500">*</span></label>
                            <input type="text" name="contact_name" class="form-control w-full border rounded p-2">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Số tiền (VND) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" min="1000" class="form-control w-full border rounded p-2">
                        <p class="text-xs text-slate-500 mt-1">Lưu ý: Hệ thống sẽ tự động tạo giao dịch Thu/Chi tương ứng.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Ngày tạo <span class="text-red-500">*</span></label>
                            <input type="date" name="loan_date" value="{{ date('Y-m-d') }}" class="form-control w-full border rounded p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Ngày đến hạn (Để thông báo)</label>
                            <input type="date" name="due_date" class="form-control w-full border rounded p-2">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Mô tả chi tiết</label>
                        <textarea name="description" class="form-control w-full border rounded p-2" rows="2"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('loanModal').close()" class="btn btn-secondary">Đóng</button>
                    <button type="submit" class="btn btn-dark">Lưu</button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- MODAL THANH TOÁN --}}
    <dialog id="paymentModal" class="modal rounded-lg shadow-xl w-full max-w-md p-0 bg-white dark:bg-slate-800 overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900">
            <h3 class="font-bold text-lg">Cập nhật thanh toán</h3>
            <p id="paymentLoanName" class="text-sm text-slate-500"></p>
        </div>
        <div class="p-6">
            <form id="paymentForm" method="POST" action="">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Số tiền thanh toán đợt này <span class="text-red-500">*</span></label>
                        <input type="number" name="payment_amount" id="paymentInput" min="0" class="form-control w-full border rounded p-2 font-bold">
                        <div class="flex justify-between text-xs mt-1">
                            <span class="text-slate-500">Còn lại cần trả: <span id="paymentRemaining" class="font-bold"></span></span>
                            <button type="button" onclick="fillFullAmount()" class="text-blue-600 hover:underline">Tất toán toàn bộ</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Ngày thực hiện</label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="form-control w-full border rounded p-2" >
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('paymentModal').close()" class="btn btn-secondary">Hủy</button>
                    <button type="submit" class="btn btn-success text-white">Xác nhận</button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- SCRIPTS: Modal & Chart (Updated) --}}
    @push('scripts')
    @vite(['resources/js/custom/finance.js'])
    @endpush
</x-app-layout>