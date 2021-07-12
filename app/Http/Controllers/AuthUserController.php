<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\BaseController as BaseController;
use Validator;

class AuthUserController extends BaseController
{
    public function register(Request $request)
    {
        // validation
        $fields = $request->all();
        $validator = Validator::make($fields, [
            'email' => 'required|email|max:100',
            'password' => 'required|string|min:6|max:100|confirmed'
        ]);
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
         //Check email exists
         $checkEmail= User::where('email', $fields['email'])->first();      //email exist
         if($checkEmail) {

             return $this->sendError('Email đã tồn tại.',[], 422); 
         }
        
        // insert user to database
        $user = User::create([
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $records['email'] = $user->email;
        $records['token'] = $user->createToken('user_token', ['user'])->plainTextToken;
        return $this->sendResponse('Đăng ký người dùng thành công.', $records,201);
    }

    public function login(Request $request)
    {

        $fields = $request->all();
        $validator = Validator::make($fields, [
            'email' => 'required|email|max:100',
            'password' => 'required|string|min:6|max:100'
        ]);
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        //Check status User
        $checkStatus= User::where('email', $fields['email'])->where('status',0)->first();      //User was locked
        if($checkStatus) {
            return $this->sendError('Người dùng đã bị vô hiệu hóa.', 401); 
        }

        // Check email password
        $user = User::where('email', $fields['email'])->first();
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return $this->sendError('Đăng nhập không thành công. Email hoặc mật khẩu không chính xác.',[], 401); 
        }

        $records['email'] = $user->email;
        $records['token'] = $user->createToken('user_token', ['user'])->plainTextToken;

        return $this->sendResponse('Người dùng đăng nhập thành công.', $records,200);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
       
        return $this->sendResponse('Đăng xuất',[],204);
    }
    
    
    
}