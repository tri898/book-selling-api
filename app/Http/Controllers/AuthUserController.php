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
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
         //Check email exists
         $checkEmail= User::where('email', $fields['email'])->first();      //email exist
         if($checkEmail) {

             return $this->sendError('Email already exists.',[], 422); 
         }
        
        // insert user to database
        $user = User::create([
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $records['email'] = $user->email;

        return $this->sendResponse('User register successfully.', $records,201);
    }

    public function login(Request $request)
    {

        $fields = $request->all();
        $validator = Validator::make($fields, [
            'email' => 'required|email|max:100',
            'password' => 'required|string|min:6|max:100'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
        //Check status User
        $checkStatus= User::where('email', $fields['email'])->where('status',0)->first();      //User was locked
        if($checkStatus) {
            return $this->sendError('User has been disabled.', 401); 
        }

        // Check email password
        $user = User::where('email', $fields['email'])->first();
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return $this->sendError('Login unsuccessful. The email or password is incorrect.',[], 401); 
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