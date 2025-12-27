<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Goal;
use App\Models\Plan;
use App\Models\UserDashboardSetting;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $settings = UserDashboardSetting::firstOrCreate(
            ['user_id' => $user->id],
            ['banner_title' => 'QUẢN TRỊ BẢN THÂN']
        );

        $yearlyGoals = Goal::where('user_id', $user->id)
            ->where('type', 'year')
            ->orderBy('progress', 'desc')
            ->get();

        $topPriorities = Plan::where('user_id', $user->id)
            ->where(function($q) {
                $q->where('is_priority', true)
                  ->orWhereDate('start_time', Carbon::today());
            })
            ->where('status', '!=', 'completed')
            ->orderBy('is_priority', 'desc')
            ->orderBy('start_time', 'asc')
            ->limit(5)
            ->get();

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $weeklyPlans = Plan::where('user_id', $user->id)
            ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->orderBy('start_time', 'asc')
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->start_time)->format('l');
            });

        return view('dashboards.self-management', compact('settings', 'yearlyGoals', 'topPriorities', 'weeklyPlans'));
    }

    public function updateBanner(Request $request)
    {
        $request->validate([
            'banner_title' => 'required|string|max:255',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $settings = UserDashboardSetting::where('user_id', Auth::id())->first();
        $settings->banner_title = $request->banner_title;

        if ($request->hasFile('banner_image')) {
            if ($settings->banner_path) {
                Storage::delete($settings->banner_path);
            }
            $path = $request->file('banner_image')->store('banners', 'public');
            $settings->banner_path = $path;
        }

        $settings->save();
        return redirect()->back()->with('success', 'Đã cập nhật giao diện!');
    }

    public function createGoalPage()
    {
        return view('dashboards.goals.create');
    }

    public function storeGoal(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'progress' => 'integer|min:0|max:100'
        ]);

        Goal::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'color' => $request->color ?? 'primary',
            'progress' => $request->progress ?? 0,
            'type' => 'year'
        ]);

        return redirect()->route('dashboard')->with('success', 'Thêm mục tiêu thành công');
    }

    public function updateGoal(Request $request, $id)
    {
        $goal = Goal::where('user_id', Auth::id())->findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'progress' => 'integer|min:0|max:100'
        ]);

        $goal->update([
            'title' => $request->title,
            'progress' => $request->progress,
            'color' => $request->color ?? $goal->color,
        ]);

        return redirect()->back()->with('success', 'Cập nhật mục tiêu thành công');
    }

    public function destroyGoal($id)
    {
        $goal = Goal::where('user_id', Auth::id())->findOrFail($id);
        $goal->delete();

        return redirect()->back()->with('success', 'Đã xóa mục tiêu');
    }

    public function createPlanPage()
    {
        return view('dashboards.plans.create');
    }

    public function storePlan(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
        ]);

        Plan::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'start_time' => $request->start_time,
            'is_priority' => $request->has('is_priority'),
            'status' => 'pending'
        ]);

        return redirect()->route('dashboard')->with('success', 'Thêm kế hoạch thành công');
    }

    public function togglePlanStatus($id)
    {
        $plan = Plan::where('user_id', Auth::id())->findOrFail($id);
        $plan->status = $plan->status === 'completed' ? 'pending' : 'completed';
        $plan->save();

        return response()->json(['success' => true, 'new_status' => $plan->status]);
    }

    public function destroyPlan($id)
    {
        $plan = Plan::where('user_id', Auth::id())->findOrFail($id);
        $plan->delete();

        return redirect()->back()->with('success', 'Đã xóa kế hoạch');
    }
}