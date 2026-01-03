<?php

namespace App\Http\Controllers;

use App\Http\Requests\GeneralSetting\UpdateGeneralSettingRequest;
use App\Models\GeneralSetting;
use App\Services\EnvFileService;
use App\Settings\GeneralSettings;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GeneralSettingController extends Controller
{
    public function __construct(protected EnvFileService $envFileService)
    {

    }

    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        abort(404);
    }

    public function show()
    {
        $breadcrumbsItems = [
            [
                'name' => 'Settings',
                'url' => '/settings',
                'active' => true
            ],
        ];

        return view('general-settings.index', [
            'pageTitle' => 'Settings',
            'breadcrumbItems' => $breadcrumbsItems,
        ]);
    }

    /**
     * Show the form for editing the resource.
     * ĐÃ SỬA: Bỏ Dependency Injection để tránh lỗi Crash khi thiếu setting
     */
    public function edit()
    {
        $breadcrumbsItems = [
            [
                'name' => 'Settings',
                'url' => '/general-settings',
                'active' => false,
            ],
            [
                'name' => 'General Settings',
                'url' => '#',
                'active' => true
            ],
        ];

        $envDetails = $this->envFileService->getAllEnv();

        // ĐOẠN CODE XỬ LÝ LỖI AN TOÀN
        try {
            // Cố gắng lấy settings chuẩn
            $generalSettings = app(GeneralSettings::class);
        } catch (\Exception $e) {
            // Nếu lỗi (do thiếu property trong DB), tạo một object giả chứa null
            $generalSettings = new \stdClass();
            $generalSettings->logo = null;
            $generalSettings->favicon = null;
            $generalSettings->dark_logo = null;
            $generalSettings->guest_logo = null;
            $generalSettings->guest_background = null;
        }

        $logoDetails = [
            'logoSrc' => $generalSettings->logo ?? null,
            'darkLogoSrc' => $generalSettings->dark_logo ?? null,
            'faviconSrc' => $generalSettings->favicon ?? null,
            'guestLogoSrc' => $generalSettings->guest_logo ?? null,
            'guestBackgroundSrc' => $generalSettings->guest_background ?? null,
        ];

        return view('general-settings.edit', [
            'pageTitle' => 'General Settings',
            'breadcrumbItems' => $breadcrumbsItems,
            'envDetails' => $envDetails,
            'logoDetails' => $logoDetails,
        ]);
    }

    public function update(Request $request)
    {
        $this->envFileService->updateEnv($request);

        return back()->with(['message' => 'General settings updated successfully.', 'type' => 'success']);
    }

    public function destroy()
    {
        abort(404);
    }

    // ĐÃ SỬA: Bỏ inject GeneralSettings ở đây và gọi thủ công để an toàn
    public function logoUpdate(UpdateGeneralSettingRequest $request)
    {
        // Lấy settings thủ công để update
        try {
            $logoSettings = app(GeneralSettings::class);
        } catch (\Exception $e) {
      
            return back()->with(['message' => 'Lỗi cấu hình Settings. Hãy kiểm tra lại Database.', 'type' => 'error']);
        }

        if ($request->hasFile('logo')) {
            $generalSetting = GeneralSetting::where('group', 'general-settings')
                ->where('name', 'logo')
                ->first();
            
            if ($generalSetting) {
                $generalSetting->clearMediaCollection('logo');
                $generalSetting->addMediaFromRequest('logo')->toMediaCollection('logo');
                
                // Cập nhật lại cache của Settings Class
                $logoSettings->logo = $generalSetting->getFirstMediaUrl('logo');
                $logoSettings->save();
            }
        }
        if ($request->hasFile('favicon')) {
            $generalSetting = GeneralSetting::where('group', 'general-settings')
                ->where('name', 'favicon')
                ->first();
            
            if ($generalSetting) {
                $generalSetting->clearMediaCollection('favicon');
                $generalSetting->addMediaFromRequest('favicon')->toMediaCollection('favicon');
                $logoSettings->favicon = $generalSetting->getFirstMediaUrl('favicon');
                $logoSettings->save();
            }
        }
        if ($request->hasFile('dark_logo')) {
            $generalSetting = GeneralSetting::where('group', 'general-settings')
                ->where('name', 'dark_logo')
                ->first();
            
            if ($generalSetting) {
                $generalSetting->clearMediaCollection('dark_logo');
                $generalSetting->addMediaFromRequest('dark_logo')->toMediaCollection('dark_logo');
                $logoSettings->dark_logo = $generalSetting->getFirstMediaUrl('dark_logo');
                $logoSettings->save();
            }
        }
        if ($request->hasFile('guest_logo')) {
            $generalSetting = GeneralSetting::where('group', 'general-settings')
                ->where('name', 'guest_logo')
                ->first();
            
            if ($generalSetting) {
                $generalSetting->clearMediaCollection('guest_logo');
                $generalSetting->addMediaFromRequest('guest_logo')->toMediaCollection('guest_logo');
                $logoSettings->guest_logo = $generalSetting->getFirstMediaUrl('guest_logo');
                $logoSettings->save();
            }
        }
        if ($request->hasFile('guest_background')) {
            $generalSetting = GeneralSetting::where('group', 'general-settings')
                ->where('name', 'guest_background')
                ->first();
            
            if ($generalSetting) {
                $generalSetting->clearMediaCollection('guest_background');
                $generalSetting->addMediaFromRequest('guest_background')->toMediaCollection('guest_background');
                $logoSettings->guest_background = $generalSetting->getFirstMediaUrl('guest_background');
                $logoSettings->save();
            }
        }

        return back()->with(['message' => 'Logo updated successfully.', 'type' => 'success']);
    }
}