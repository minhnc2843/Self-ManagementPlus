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
        // Lấy thông báo của user đang đăng nhập, phân trang
        $notifications = Auth::user()->notifications()->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Đánh dấu tất cả là đã đọc.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Đã đánh dấu tất cả là đã đọc.');
    }
}