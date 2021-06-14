<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthUserController extends BaseController
{
    public function register(Request $request)
    {
        // validation
        $fields = $request->validate([
            'name' => 'required|string|max:50',
            'address' => 'required|string|min:10|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email|max:100',
            'password' => 'required|string|min:6|max:100|confirmed'
        ]);
        // insert user to database
        $user = User::create([
            'name' => $fields['name'],
            'address' => $fields['address'],
            'phone' => $fields['phone'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $records['name'] = $user->name;

        return $this->sendResponse('User register successfully.', $records,201);
    }

    public function login(Request $request)
    {

        $fields = $request->validate([
            'email' => 'required|email|max:100',
            'password' => 'required|string|min:6|max:100'
        ]);
        
        //Check status User
        $checkStatus= User::where('email', $fields['email'])->where('status',0)->first();      //User was locked
        if($checkStatus) {

            return $this->sendError('User has been disabled.', 200); 
        }
        // Check email password
        $user = User::where('email', $fields['email'])->first();
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return $this->sendError('Login unsuccessful. The email or password is incorrect.', 401); 
        }

        $records['name'] = $user->name;
        $records['token'] = $user->createToken('user_token', ['user'])->plainTextToken;

        return $this->sendResponse('User login successfully.', $records,200);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
       
        return $this->sendResponse('Logout',[],204);
    }
    
    
    
}