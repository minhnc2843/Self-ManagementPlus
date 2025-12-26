<?php

namespace App\Http\Controllers;

use App\Models\ExpenseGroup;
use App\Models\SharedExpense;
use App\Models\SharedExpenseSplit;
use App\Models\ExpensePayer;
use App\Models\ExpenseShare;
use App\Notifications\SharedExpenseNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
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
            'members' => 'array',
        ]);

        // Sử dụng transaction để đảm bảo dữ liệu nhất quán
        DB::transaction(function () use ($request) {
            // 1. Tạo nhóm
            $group = ExpenseGroup::create([
                'name' => $request->name,
                'description' => $request->description,
                'creator_id' => Auth::id(),
            ]);

            // 2. Thêm chính mình vào nhóm
            $group->members()->attach(Auth::id());

            // 3. Lấy danh sách thành viên từ request
            $memberIds = $request->input('members', []); // <--- SỬA: Gán biến $memberIds tại đây

            // 4. Thêm thành viên khác và gửi thông báo
            if (!empty($memberIds)) {
                // Attach vào DB
                $group->members()->attach($memberIds);

                // Lấy thông tin user để gửi thông báo
                $usersToNotify = User::whereIn('id', $memberIds)
                                    ->where('id', '!=', auth()->id()) 
                                    ->get();

                $notificationData = [
                    'message' => auth()->user()->name . ' đã thêm bạn vào nhóm chi tiêu: ' . $request->name,
                    // SỬA: Thay $sharedExpense->id bằng $group->id
                    // Đảm bảo route 'shared-expenses.show' hoặc 'expense-groups.show' tồn tại trong routes/web.php
                    'url' => route('expense-groups.show', $group->id), 
                ];

                // Gửi thông báo
                Notification::send($usersToNotify, new SharedExpenseNotification($notificationData));
            }
        });

        return redirect()->route('expense-groups.index')->with('success', 'Tạo nhóm thành công!');
    }



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
        $actualPayers = array_filter($request->payers, function ($amount) {
            // Xử lý chuỗi tiền tệ (bỏ dấu phẩy nếu có) trước khi float
            $cleanAmount = str_replace([',', '.'], '', $amount); 
            return (float) $cleanAmount > 0;
        });

        if (empty($actualPayers)) {
            return back()->withErrors(['payers' => 'Chưa xác định ai là người trả tiền.'])->withInput();
        }

        // 3. Kiểm tra tổng tiền Payers có khớp với Total Amount không?
        $totalPaid = array_sum($actualPayers);
        // Cho phép sai số nhỏ do làm tròn
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

            // --- PHẦN THÊM MỚI: GỬI THÔNG BÁO ---
            
            // 1. Lấy danh sách ID tất cả người liên quan (Người trả + Người thụ hưởng)
            $payerIds = array_keys($actualPayers);
            $sharerIds = $request->shares;
            
            // Gộp mảng và loại bỏ trùng lặp
            $involvedUserIds = array_unique(array_merge($payerIds, $sharerIds));

            // 2. Lấy danh sách User Object để gửi thông báo (trừ người tạo)
            $usersToNotify = User::whereIn('id', $involvedUserIds)
                                ->where('id', '!=', Auth::id()) 
                                ->get();

            if ($usersToNotify->count() > 0) {
                $formattedAmount = number_format($request->total_amount, 0, ',', '.');
                
                $notificationData = [
                    'message' => Auth::user()->name . " đã thêm khoản chi: {$request->title} ({$formattedAmount} đ)",
                    'url' => route('expense-groups.show', $groupId), // Link về trang chi tiết nhóm
                ];

                Notification::send($usersToNotify, new SharedExpenseNotification($notificationData));
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