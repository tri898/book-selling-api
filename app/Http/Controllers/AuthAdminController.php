<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\User as UserResource;
use Validator;

class AuthAdminController extends BaseController
{
    public function login(Request $request)
    {
        // validation
        $fields = $request->all();
        $validator = Validator::make($fields, [
            'email' => 'required|email|max:100',
            'password' => 'required|string|min:6|max:100'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
        
        $admin = Admin::where('email', $fields['email'])->first();
        // Check email & password
        if(!$admin || !Hash::check($fields['password'], $admin->password)) {

            return $this->sendError('Login unsuccessful. The email or password is incorrect.',[], 401); 
        }

        $records['name'] = $admin->name;
        $records['token']  = $admin->createToken('admin_token', ['admin'])->plainTextToken;

        return $this->sendResponse('Admin login successfully.', $records,200);
    }

    public function logout(Request $request)
    {
        // delete token
        auth()->user()->tokens()->delete();
    
        return $this->sendResponse('Logout',[],204);
    }
  
    
}