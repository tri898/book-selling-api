<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\User as UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthAdminController extends BaseController
{
    public function login(Request $request)
    {
        // validation
        $fields = $request->validate([
            'email' => 'required|email|max:100',
            'password' => 'required|string|min:6|max:100'
        ]);

        // Check email
        $admin = Admin::where('email', $fields['email'])->first();

        // Check password
        if(!$admin || !Hash::check($fields['password'], $admin->password)) {

            return $this->sendError('Login unsuccessful. The email or password is incorrect.', 401); 
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