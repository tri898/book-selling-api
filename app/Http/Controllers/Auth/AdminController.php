<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use App\Models\Admin;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\User as UserResource;

class AdminController extends BaseController
{
    public function login(LoginRequest $request)
    {
        // if validate fail return 422 with message and errors
        $fields = $request->validated();       
        $admin = Admin::where('email', $fields['email'])->first();
        // Check email & password
        if(!$admin || !Hash::check($fields['password'], $admin->password)) {

            return $this->sendError('Đăng nhập không thành công. Email hoặc mật khẩu không chính xác.',[], 401); 
        }

        $records['email'] = $admin->email;
        $records['token']  = $admin->createToken('admin_token', ['admin'])->plainTextToken;

        return $this->sendResponse('Đăng nhập quản trị thành công.', $records,200);
    }

    public function logout()
    {
        // delete token
        auth()->user()->tokens()->delete();
    
        return $this->sendResponse('Đăng xuất.',[],204);
    }
  
    
}