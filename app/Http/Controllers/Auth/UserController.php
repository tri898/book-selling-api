<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Requests\{LoginRequest,RegisterRequest};
use App\Models\User;
use App\Http\Controllers\BaseController as BaseController;

class UserController extends BaseController
{
    public function register(RegisterRequest $request)
    {
        $fields = $request->validated();
         //Check email exists
         $checkEmail= User::where('email', $fields['email'])->first();      //email exist
         if($checkEmail) {
            return $this->sendError('Email đã tồn tại.',[], 409); 
         }
        
        // insert user to database
        $user = User::create([
            'email' => $fields['email'],
            'name' => Str::random(10),
            'password' => bcrypt($fields['password'])
        ]);

        $records['name'] = $user->name;
        $records['token'] = $user->createToken('user_token', ['user'])->plainTextToken;
        return $this->sendResponse('Đăng ký người dùng thành công.', $records,201);
    }

    public function login(LoginRequest $request)
    {
        $fields = $request->validated();
        //Check status User
        $checkStatus= User::where('email', $fields['email'])->where('status','Khóa')->first();      //User was locked
        if($checkStatus) {
            $checkStatus->tokens()->delete();
            return $this->sendError('Người dùng đã bị vô hiệu hóa.',[], 401); 
        }

        // Check email password
        $user = User::where('email', $fields['email'])->first();
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return $this->sendError('Đăng nhập không thành công. Email hoặc mật khẩu không chính xác.',[], 401); 
        }

        $records['name'] = $user->name;
        $records['token'] = $user->createToken('user_token', ['user'])->plainTextToken;

        return $this->sendResponse('Người dùng đăng nhập thành công.', $records,200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
       
        return $this->sendResponse('Đăng xuất',[],204);
    }
    
    
    
}