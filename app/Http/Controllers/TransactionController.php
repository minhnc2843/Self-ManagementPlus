<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Notifications\LoanDueNotification;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $period = $request->get('period', 'month');

        // 1. Query Transactions
        $transactionQuery = Transaction::with('user')->where('user_id', $userId);
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $transactionQuery->where(function($q) use ($search) {
                $q->where('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // 2. Query Loans (Tìm kiếm khoản vay)
        $loanQuery = Loan::where('user_id', $userId);
        if ($request->has('loan_search') && $request->loan_search != '') {
            $s = $request->loan_search;
            $loanQuery->where(function($q) use ($s) {
                $q->where('contact_name', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        // 3. Tính toán tổng quan Dashboard (Đã tối ưu bằng SQL Aggregation để tránh Memory Limit)
        $sums = Transaction::where('user_id', $userId)
            ->selectRaw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income")
            ->selectRaw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense")
            ->first();

        $totalIncome = $sums->income ?? 0;
        $totalExpense = $sums->expense ?? 0;
        $currentBalance = $totalIncome - $totalExpense;

        // 4. Lấy dữ liệu biểu đồ (Đã tối ưu thành 1 truy vấn duy nhất)
        $chartData = $this->getChartData($userId, $period);

        // 5. Phân trang
        $transactions = $transactionQuery->orderBy('transaction_date', 'desc')->paginate(10, ['*'], 'trans_page');
        
        // Sắp xếp khoản vay: Chưa trả lên trước -> Ngày đến hạn gần nhất
        $loans = $loanQuery->orderBy('status', 'desc')
                           ->orderBy('due_date', 'asc')
                           ->paginate(10, ['*'], 'loan_page');

        return view('dashboards.finance.index', compact(
            'transactions', 
            'loans', 
            'totalIncome', 
            'totalExpense', 
            'currentBalance', 
            'chartData', 
            'period'
        ));
    }

    /**
     * Hàm xử lý dữ liệu biểu đồ tối ưu hóa (Chỉ chạy duy nhất 1 câu SQL gom nhóm)
     */
    protected function getChartData($userId, $period)
    {
        $incomeData = [];
        $expenseData = [];
        $labels = [];

        if ($period == 'week') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();

            // Truy vấn duy nhất cho cả tuần
            $results = Transaction::where('user_id', $userId)
                ->whereBetween('transaction_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                ->selectRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') as date, type, SUM(amount) as total")
                ->groupBy('date', 'type')
                ->get();

            $dataMap = [];
            foreach ($results as $row) {
                $dataMap[$row->date][$row->type] = (float)$row->total;
            }

            // Lặp 7 ngày trong tuần
            for ($i = 0; $i < 7; $i++) {
                $dateObj = $start->copy()->addDays($i);
                $dateStr = $dateObj->format('Y-m-d');
                $labels[] = $dateObj->format('d/m'); // Label: 20/12
                
                $incomeData[] = $dataMap[$dateStr]['income'] ?? 0;
                $expenseData[] = $dataMap[$dateStr]['expense'] ?? 0;
            }

        } elseif ($period == 'year') {
            $year = Carbon::now()->year;

            // Truy vấn duy nhất cho cả năm
            $results = Transaction::where('user_id', $userId)
                ->whereYear('transaction_date', $year)
                ->selectRaw("MONTH(transaction_date) as month, type, SUM(amount) as total")
                ->groupBy('month', 'type')
                ->get();

            $dataMap = [];
            foreach ($results as $row) {
                $dataMap[(int)$row->month][$row->type] = (float)$row->total;
            }

            // Lặp 12 tháng
            for ($i = 1; $i <= 12; $i++) {
                $labels[] = "T$i"; // Label: T1, T2...
                $incomeData[] = $dataMap[$i]['income'] ?? 0;
                $expenseData[] = $dataMap[$i]['expense'] ?? 0;
            }
        } else { 
            // Mặc định là 'month': Lặp qua số ngày của tháng hiện tại
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();

            // Truy vấn duy nhất cho cả tháng
            $results = Transaction::where('user_id', $userId)
                ->whereBetween('transaction_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                ->selectRaw("DAY(transaction_date) as day, type, SUM(amount) as total")
                ->groupBy('day', 'type')
                ->get();

            $dataMap = [];
            foreach ($results as $row) {
                $dataMap[(int)$row->day][$row->type] = (float)$row->total;
            }

            $daysInMonth = Carbon::now()->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT); // Label: 01, 02...
                
                $incomeData[] = $dataMap[$i]['income'] ?? 0;
                $expenseData[] = $dataMap[$i]['expense'] ?? 0;
            }
        }

        return [
            'labels' => $labels,
            'income' => $incomeData,
            'expense' => $expenseData
        ];
    }

    public function create()
    {
        return view('dashboards.finance.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric|min:1000',
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $validatedData['user_id'] = auth()->id();
        Transaction::create($validatedData);

        return redirect()->route('finance.index')->with('success', 'Đã thêm giao dịch thành công!');
    }

    /**
     * TẠO KHOẢN VAY MỚI & TỰ ĐỘNG CẬP NHẬT DOANH THU
     */
    public function storeLoan(Request $request)
    {
        $validated = $request->validate([
            'contact_name' => 'required|string|max:255',
            'amount' => 'required|numeric|gt:0',
            'type' => 'required|in:borrowed,lent',
            'loan_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:loan_date',
            'description' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';
        $validated['paid_amount'] = 0;

        $loan = null;

        DB::transaction(function () use ($validated, &$loan) {
            // 1. Tạo khoản vay
            $loan = Loan::create($validated);

            // 2. Tự động trừ/cộng tiền trong Transaction
            $transData = [
                'user_id' => auth()->id(),
                'transaction_date' => $validated['loan_date'],
                'amount' => $validated['amount'],
                'description' => "Tạo khoản vay: " . ($validated['description'] ?? $validated['contact_name']),
            ];

            if ($validated['type'] == 'lent') {
                // Tôi cho vay -> Tiền đi ra -> Expense
                $transData['type'] = 'expense';
                $transData['category'] = 'Cho vay';
            } else {
                // Tôi đi vay -> Tiền đi vào -> Income
                $transData['type'] = 'income';
                $transData['category'] = 'Đi vay';
            }

            Transaction::create($transData);
        });

        // Gửi thông báo realtime
        if ($loan) {
            try {
                auth()->user()->notify(new LoanDueNotification($loan, $validated['amount'], 'create'));
            } catch (\Exception $e) {
                Log::error("Lỗi gửi thông báo realtime (Store Loan): " . $e->getMessage());
            }
        }

        return redirect()->route('finance.index')->with('success', 'Đã tạo khoản vay và cập nhật số dư!');
    }

    /**
     * THANH TOÁN / TẤT TOÁN KHOẢN VAY
     */
   // ... (các phần use giữ nguyên)

    public function payLoan(Request $request, $id)
    {
        $user = Auth::user(); // Lấy user hiện tại để gửi thông báo
        $loan = Loan::where('user_id', $user->id)->findOrFail($id);

        if ($loan->status == 'paid') {
            return back()->with('error', 'Khoản vay này đã tất toán xong từ trước.');
        }

        // Validate
        $request->validate([
            'payment_amount' => 'required|numeric|gt:0|lte:' . $loan->remaining_amount,
            'payment_date' => 'required|date',
        ], [
            'payment_amount.required' => 'Vui lòng nhập số tiền thanh toán.',
            'payment_amount.numeric' => 'Số tiền phải là số.',
            'payment_amount.gt' => 'Số tiền thanh toán phải lớn hơn 0.',
            'payment_amount.lte' => 'Số tiền trả không được lớn hơn số nợ còn lại (' . number_format($loan->remaining_amount) . ' đ).',
            'payment_date.required' => 'Vui lòng chọn ngày thanh toán.',
            'payment_date.date' => 'Ngày thanh toán không hợp lệ.',
        ]);

        $amount = $request->payment_amount;
        $message = '';

        DB::transaction(function () use ($loan, $amount, $request, $user, &$message) {
            // 1. Cập nhật khoản vay
            $loan->paid_amount += $amount;
            
            // Kiểm tra nếu trả hết
            if ($loan->paid_amount >= ($loan->amount - 100)) { 
                $loan->status = 'paid';
                $loan->completed_at = now();
                $loan->paid_amount = $loan->amount; 
                $message = 'Chúc mừng! Bạn đã tất toán xong khoản vay này.';
            } else {
                $remaining = number_format($loan->amount - $loan->paid_amount);
                $message = "Đã thanh toán " . number_format($amount) . " đ. Còn lại: {$remaining} đ.";
            }
            $loan->save();

            // 2. Tạo Transaction cân bằng dòng tiền
            $transData = [
                'user_id' => $user->id,
                'transaction_date' => $request->payment_date,
                'amount' => $amount,
                'description' => "Thanh toán khoản vay: " . $loan->contact_name,
            ];

            if ($loan->type == 'lent') {
                $transData['type'] = 'income'; // Thu nợ (Tiền vào)
                $transData['category'] = 'Thu nợ';
            } else {
                $transData['type'] = 'expense'; // Trả nợ (Tiền ra)
                $transData['category'] = 'Trả nợ';
            }

            Transaction::create($transData);
           
        });

        // Gửi thông báo realtime
        try {
            $user->notify(new LoanDueNotification($loan, $amount, 'payment'));
        } catch (\Exception $e) {
            Log::error("Lỗi gửi thông báo realtime (Pay Loan): " . $e->getMessage());
        }

        return redirect()->back()->with('success', $message);
    }
}