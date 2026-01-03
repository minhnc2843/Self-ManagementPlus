<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('profiles.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateProfileRequest  $request
     * @param  User  $user
     * @return RedirectResponse
     */
    
            public function update(UpdateProfileRequest $request, User $user)
        {
            $user->update($request->safe([
                'name', 
                'phone', 
                'post_code', 
                'city', 
                'country', 
                'status', 
                'profile_description'
            ]));
            // Email change
            if ($user->email !== $request->validated('email')) {
                $user->newEmail($request->validated('email'));
            }

            /**
             * CASE 1: Avatar từ Cropper (Base64)
             * SỬ DỤNG HÀM addMediaFromBase64 CỦA SPATIE
             */
            if ($request->filled('avatar_base64')) {
                
                // 1. Xóa avatar cũ
                $user->clearMediaCollection('profile-image');

                // 2. Add trực tiếp từ chuỗi Base64 (Thư viện tự xử lý decode và lưu file)
                try {
                    $user->addMediaFromBase64($request->avatar_base64)
                        ->usingFileName('avatar-' . time() . '.png') // Đặt tên file tránh trùng cache
                        ->toMediaCollection('profile-image');
                } catch (\Exception $e) {
                    // Log lỗi nếu cần thiết
                    return redirect()->back()->with('error', 'Lỗi upload ảnh: ' . $e->getMessage());
                }
            }

            /**
             * CASE 2: Upload thường (fallback)
             */
            elseif ($request->hasFile('photo')) {
                $user->clearMediaCollection('profile-image');
                $user->addMediaFromRequest('photo')->toMediaCollection('profile-image');
            }

            return to_route('profiles.index')
                ->with('message', 'Profile updated successfully');
        }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
