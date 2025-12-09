<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Cần import Carbon để làm việc với ngày tháng

class TransactionController extends Controller
{
    /**
     * 1. Hiển thị danh sách & Thống kê số dư và Biểu đồ
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // >>> FIX LỖI LAZY LOADING ở view <<<
        $query = Transaction::with('user')->where('user_id', $userId);

        // --- Logic Lọc / Tìm kiếm (Mục 1.3) ---
        // (Giữ nguyên logic lọc của bạn)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && $request->type != 'all' && $request->type != '') {
            $query->where('type', $request->type);
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        // Lấy danh sách giao dịch
        $transactions = $query->orderBy('transaction_date', 'desc')
                              ->orderBy('created_at', 'desc')
                              ->paginate(10);

        // --- Tính toán số dư ---
        $allTransactions = Transaction::where('user_id', $userId)->get();
        
        $totalIncome = $allTransactions->where('type', 'income')->sum('amount');
        $totalExpense = $allTransactions->where('type', 'expense')->sum('amount');
        $currentBalance = $totalIncome - $totalExpense;

        // --- Lấy dữ liệu Biểu đồ (MỚI) ---
        $chartData = $this->getChartData($userId);

        return view('dashboards.finance.index', compact(
            'transactions', 
            'totalIncome', 
            'totalExpense', 
            'currentBalance',
            'chartData' // <<< TRUYỀN DỮ LIỆU BIỂU ĐỒ VÀO VIEW
        ));
    }

    /**
     * Hàm hỗ trợ: Lấy dữ liệu Thu/Chi theo 6 tháng gần nhất.
     */
    protected function getChartData($userId, $months = 6)
    {
        $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // 1. Lấy dữ liệu Thu/Chi cho 6 tháng gần nhất
        $monthlyData = Transaction::query()
            ->where('user_id', $userId)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw("
                DATE_FORMAT(transaction_date, '%Y-%m') as month_year,
                type,
                SUM(amount) as total_amount
            ")
            ->groupBy('month_year', 'type')
            ->get();
        
        // 2. Chuẩn bị mảng để lưu kết quả (6 tháng)
        $labels = [];
        $incomeData = [];
        $expenseData = [];
        $monthPeriods = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            // Label: T10/2025
            $labels[] = 'T' . $date->format('m/Y'); 
            // Key để tra cứu data: 2025-10
            $monthPeriods[] = $date->format('Y-m'); 
            $incomeData[] = 0;
            $expenseData[] = 0;
        }

        // 3. Đổ dữ liệu vào mảng kết quả
        foreach ($monthlyData as $data) {
            $index = array_search($data->month_year, $monthPeriods);
            if ($index !== false) {
                if ($data->type === 'income') {
                    $incomeData[$index] = (int) $data->total_amount;
                } else {
                    $expenseData[$index] = (int) $data->total_amount;
                }
            }
        }

        return [
            'labels' => $labels,
            'income' => $incomeData,
            'expense' => $expenseData,
        ];
    }
    
    /**
     * 2. Hiển thị form tạo mới
     */
    public function create()
    {
        return view('dashboards.finance.create');
    }

    /**
     * 3. Lưu dữ liệu
     */
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
}