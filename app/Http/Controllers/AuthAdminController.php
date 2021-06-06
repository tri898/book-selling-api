<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthAdminController extends Controller
{
    public function login(Request $request) {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:100'
        ]);

        // Check email
        $admin = Admin::where('email', $fields['email'])->first();

        // Check password
        if(!$admin || !Hash::check($fields['password'], $admin->password)) {
            return response([
                'message' => 'Login unsuccessful. The email or password is incorrect.'
            ], 401);
        }

        $records['name'] = $admin->name;
        $records['token']  = $admin->createToken('admin_token', ['admin'])->plainTextToken;

        $response = [
            'message' => 'Admin login successfully.',
            'data' => $records
        ];

        return response($response, 201);
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();
        $response = [
            'message' => 'Logged out.'
        ];

        return response($response, 204);
    }
  
    
}