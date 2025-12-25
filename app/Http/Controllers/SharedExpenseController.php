<?php

namespace App\Http\Controllers;

use App\Models\ExpenseGroup;
use App\Models\SharedExpense;
use App\Models\SharedExpenseSplit;
use App\Models\ExpensePayer;
use App\Models\ExpenseShare;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SharedExpenseController extends Controller
{
   public function index()
{
    $groups = ExpenseGroup::where('creator_id', Auth::id())
        ->orWhereHas('members', function ($q) {
            $q->where('user_id', Auth::id());
        })
        ->with('members')
        ->withCount('members') // <--- THÊM DÒNG NÀY
        ->get();
        
    return view('dashboards.shared-expenses.index', compact('groups'));
}

    public function create()
    {
        $users = User::where('id', '!=', Auth::id())->get(); // Lấy list user để add vào nhóm
        return view('dashboards.shared-expenses.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'members' => 'array', // List ID các thành viên
        ]);

        DB::transaction(function () use ($request) {
            $group = ExpenseGroup::create([
                'name' => $request->name,
                'description' => $request->description,
                'creator_id' => Auth::id(),
            ]);

            // Thêm chính mình vào nhóm
            $group->members()->attach(Auth::id());
            
            // Thêm thành viên khác
            if ($request->has('members')) {
                $group->members()->attach($request->members);
            }
        });

        return redirect()->route('expense-groups.index')->with('success', 'Tạo nhóm thành công!');
    }

// app/Http/Controllers/SharedExpenseController.php

public function show($id)
{
    // 1. Load dữ liệu cho thống kê (Cần tất cả expenses để tính toán số dư)
    $group = ExpenseGroup::with([
        'members',
        'expenses.payers',
        'expenses.shares'
    ])->findOrFail($id);

    // 2. Load dữ liệu cho danh sách hiển thị (Phân trang 10 dòng/trang)
    $expenses = $group->expenses()
        ->orderBy('date', 'desc')
        ->with([
            'creator',
            'payers.user',
            'shares.user'
        ])
        ->paginate(10);

    // --- LOGIC TÍNH TOÁN THỐNG KÊ (MỚI) ---
    $memberStats = [];
    
    foreach ($group->members as $member) {
        // 1. Tổng tiền người này đã móc ví trả (Paid)
        $totalPaid = $group->expenses->flatMap->payers
            ->where('user_id', $member->id)
            ->sum('amount_paid');

        // 2. Tổng tiền người này có trách nhiệm phải trả (Share)
        $totalShare = $group->expenses->flatMap->shares
            ->where('user_id', $member->id)
            ->sum('amount_owed');

        // 3. Số dư (Balance): Dương là được nhận lại, Âm là phải trả thêm
        $balance = $totalPaid - $totalShare;

        $memberStats[$member->id] = [
            'name' => $member->name,
            'avatar' => $member->avatar,
            'paid' => $totalPaid,
            'share' => $totalShare,
            'balance' => $balance
        ];
    }

    return view('dashboards.shared-expenses.show', compact('group', 'memberStats', 'expenses'));
}

public function storeExpense(Request $request, $groupId)
{
    // 1. Validate dữ liệu đầu vào
    $request->validate([
        'title' => 'required|string|max:255',
        'date' => 'required|date',
        'total_amount' => 'required|numeric|min:1000',
        'payers' => 'required|array', // Mảng [user_id => amount]
        'shares' => 'required|array|min:1', // Mảng [user_id] những người tham gia
    ], [
        'shares.required' => 'Bạn phải chọn ít nhất 1 người để chia tiền.',
        'total_amount.required' => 'Vui lòng nhập tổng số tiền.',
    ]);

    // 2. Lọc ra những người có trả tiền thực tế (Amount > 0)
    // Input từ form gửi lên: ['1' => '500000', '2' => '0'] -> Lọc bỏ số 0
    $actualPayers = array_filter($request->payers, function ($amount) {
        return $numericAmount = (float) filter_var($amount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) > 0;
    });

    if (empty($actualPayers)) {
        return back()->withErrors(['payers' => 'Chưa xác định ai là người trả tiền.'])->withInput();
    }

    // 3. Kiểm tra tổng tiền Payers có khớp với Total Amount không?
    $totalPaid = array_sum($actualPayers);
    // Cho phép sai số nhỏ (dưới 100đ) do làm tròn
    if (abs($totalPaid - $request->total_amount) > 100) {
        return back()->withErrors(['total_amount' => 'Tổng tiền người trả (' . number_format($totalPaid) . ') không khớp với tổng hóa đơn (' . number_format($request->total_amount) . ').'])->withInput();
    }

    DB::transaction(function () use ($request, $groupId, $actualPayers) {
        // A. Tạo Header hóa đơn
        $expense = SharedExpense::create([
            'expense_group_id' => $groupId,
            'created_by' => Auth::id(),
            'title' => $request->title,
            'date' => $request->date,
            'total_amount' => $request->total_amount,
            'description' => $request->description
        ]);

        // B. Lưu người trả tiền (Payers)
        foreach ($actualPayers as $userId => $amount) {
            ExpensePayer::create([
                'shared_expense_id' => $expense->id,
                'user_id' => $userId,
                'amount_paid' => $amount
            ]);
        }

        // C. Lưu người chia tiền (Shares)
        $shareCount = count($request->shares);
        $amountPerPerson = $request->total_amount / $shareCount;

        foreach ($request->shares as $userId) {
            ExpenseShare::create([
                'shared_expense_id' => $expense->id,
                'user_id' => $userId,
                'amount_owed' => $amountPerPerson
            ]);
        }
    });

    return redirect()->route('expense-groups.show', $groupId)->with('success', 'Đã thêm khoản chi thành công!');
}

    public function createExpenseView($groupId)
    {
        $group = ExpenseGroup::with('members')->findOrFail($groupId);
        return view('dashboards.shared-expenses.create_expense', compact('group'));
    }
}