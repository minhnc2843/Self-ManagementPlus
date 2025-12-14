<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Lấy danh sách notification chưa đọc của user hiện tại
     */
    public function unread(Request $request)
    {
        $user = Auth::user();
        $query = $user->unreadNotifications();

        // Giới hạn số lượng nếu có param limit
        if ($request->has('limit')) {
            $query->limit($request->limit);
        }

        $notifications = $query->get();

        return response()->json([
            'success' => true,
            'data' => $notifications->map(fn ($n) => array_merge(['id' => $n->id, 'created_at' => $n->created_at], $n->data))
        ]);
    }
}
