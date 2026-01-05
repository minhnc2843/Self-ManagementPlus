<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Goal;
use App\Models\Plan;
use App\Models\UserDashboardSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $settings = UserDashboardSetting::firstOrCreate(
            ['user_id' => $user->id],
            ['banner_title' => 'QUẢN TRỊ BẢN THÂN']
        );

        $yearlyGoals = Goal::where('user_id', auth()->id())
        ->orderBy('is_completed') // chưa hoàn thành lên trước
        ->orderByRaw('ISNULL(deadline), deadline ASC')
        ->orderByDesc('completed_at')
        ->paginate(5);

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

        return view('dashboards.home', compact('settings', 'yearlyGoals', 'topPriorities', 'weeklyPlans'));
    }

    public function editBannerPage()
    {
        $settings = UserDashboardSetting::firstOrCreate(['user_id' => Auth::id()]);
        return view('dashboards.banner.edit', compact('settings'));
    }



    public function updateBanner(Request $request)
    {
        $request->validate([
            'banner_title' => 'required|string|max:255',
            'banner_quote' => 'nullable|string|max:500',
            'show_banner_title' => 'boolean',
            'show_banner_quote' => 'boolean',
        ]);

        try {
            $settings = UserDashboardSetting::firstOrCreate(['user_id' => Auth::id()]);
            
            $settings->banner_title = $request->banner_title;
            $settings->banner_quote = $request->banner_quote;
            $settings->show_banner_title = $request->boolean('show_banner_title');
            $settings->show_banner_quote = $request->boolean('show_banner_quote');

            if($request->has('banner_height')) $settings->banner_height = $request->banner_height;
            if($request->has('banner_position_y')) $settings->banner_position_y = $request->banner_position_y;

            if ($request->filled('banner_image_base64')) {
                if ($settings->banner_path && Storage::disk('public')->exists($settings->banner_path)) {
                    Storage::disk('public')->delete($settings->banner_path);
                }

                $image_parts = explode(";base64,", $request->input('banner_image_base64'));
                $image_base64 = base64_decode($image_parts[1]);

                $fileName = 'banners/' . uniqid() . '.png';
                Storage::disk('public')->put($fileName, $image_base64);
                
                $settings->banner_path = $fileName;
            } 
            elseif ($request->hasFile('banner_image')) {
                if ($settings->banner_path && Storage::disk('public')->exists($settings->banner_path)) {
                    Storage::disk('public')->delete($settings->banner_path);
                }
                $path = $request->file('banner_image')->store('banners', 'public');
                $settings->banner_path = $path;
            }

            $settings->save();
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Cập nhật thành công!']);
            }

            return redirect()->route('dashboard')->with('success', 'Đã cập nhật giao diện!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
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

        try {
            Goal::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'color' => $request->color ?? 'primary',
                'progress' => $request->progress ?? 0,
                'type' => 'year',
                'deadline' => $request->deadline // Thêm deadline nếu có trong form
            ]);

            return redirect()->route('dashboard')->with('success', 'Thêm mục tiêu thành công');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function editGoalPage($id)
    {
        $goal = Goal::where('user_id', Auth::id())->findOrFail($id);
        return view('dashboards.goals.edit', compact('goal'));
    }

    public function updateGoal(Request $request, $id)
    {
        $yearlyGoals = Goal::where('user_id', Auth::id())->findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'progress' => 'integer|min:0|max:100'
        ]);

        $yearlyGoals->update([
            'title' => $request->title,
            'progress' => $request->progress,
            'color' => $request->filled('color') ? $request->color : $yearlyGoals->color,
        ]);

        return redirect()->route('dashboard')->with('success', 'Cập nhật mục tiêu thành công');
    }

    public function updateProgress(Request $request, $id)
{
    $goal = Goal::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    $progress = min(100, max(0, $request->progress));

    $goal->update([
        'progress' => $progress,
        'is_completed' => $progress == 100,
        'completed_at' => $progress == 100 ? now() : null,
    ]);

    return back()->with('success', 'Cập nhật tiến độ thành công');
}

public function completeGoal($id)
{
    $goal = Goal::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    $goal->update([
        'progress' => 100,
        'is_completed' => true,
        'completed_at' => now(),
    ]);

    return back()->with('success', 'Mục tiêu đã hoàn thành 🎉');
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

        try {
            Plan::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'start_time' => $request->start_time,
                'is_priority' => $request->has('is_priority') ? 1 : 0,
                'status' => 'pending'
            ]);

            return redirect()->route('dashboard')->with('success', 'Thêm kế hoạch thành công');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
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