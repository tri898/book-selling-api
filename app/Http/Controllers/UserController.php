<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile() {

        $user = auth()->user();

        $records['name'] = $user->name;
        $records['address'] = $user->address;
        $records['phone'] = $user->phone;
        $records['email'] = $user->email;
        $response = [
            'message' => 'Profile user retrieved successfully.',
            'data' => $records
        ];
        return response($response, 200);
    }
     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:50',
            'address' => 'required|string|min:10|max:100',
            'phone' => 'required|numeric|digits:10'

        ]);
        $user = auth()->user();
        $user->update([
            'name' => $fields['name'],
            'address' => $fields['address'],
            'phone' => $fields['phone']
            ]);

        $records['name'] = $user->name;
        $records['address'] = $user->address;
        $records['phone'] = $user->phone;
        $records['email'] = $user->email;
        $response = [
            'message' => 'Profile user updated successfully.',
            'data' => $records
        ];
        return response($response,200);
        
    }
}
