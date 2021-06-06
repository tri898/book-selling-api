<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthUserController extends Controller
{
    public function register(Request $request) {

        $fields = $request->validate([
            'name' => 'required|string|max:50',
            'address' => 'required|string|min:15|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email||max:100',
            'password' => 'required|string|min:6|max:100|confirmed'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'address' => $fields['address'],
            'phone' => $fields['phone'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $records['name'] = $user->name;

        $response = [
            'message' => 'User register successfully.',
            'data' => $records
        ];

        return response($response, 201);
    }

    public function login(Request $request) {

        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:100'
        ]);
        
        //Check status account
        $checkStatus= User::where('email', $fields['email'])->where('status',0)->first();      //account was locked
        if($checkStatus) {
            return response([
                'message' => 'Account has been disabled.'
            ], 202);
        }
        // Check email password
        $user = User::where('email', $fields['email'])->first();
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Login unsuccessful. The email or password is incorrect.'
            ], 401);
        }

        $records['name'] = $user->name;
        $records['token'] = $user->createToken('user_token', ['user'])->plainTextToken;

        $response = [
            'message' => 'User login successfully.',
            'data' => $records
        ];

        return response($response, 200);
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();
        $response = [
            'message' => 'Logged out.'
        ];
        return response($response, 204);
    }
    
    
    
}