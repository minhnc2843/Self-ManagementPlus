<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Hiển thị danh sách toàn bộ thông báo.
     */
    public function index()
    {
        $user = Auth::user();
        // Lấy thông báo của user đang đăng nhập, phân trang
        $notifications = Auth::user()->notifications()->paginate(10);

        return view('dashboards.notifications.index', compact('notifications'));
    }

    /**
     * Đánh dấu tất cả là đã đọc.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Đã đánh dấu tất cả là đã đọc.');
    }
    public function markAsReadAndRedirect($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            
            // Lấy URL từ dữ liệu thông báo, nếu không có thì về trang chủ
            $url = $notification->data['url'] ?? route('events.list');
            
            return redirect($url);
        }

        return back()->with('error', 'Không tìm thấy thông báo');
    }


}