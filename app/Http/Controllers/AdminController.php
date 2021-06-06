<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
     /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
    public function usersList() {
        
        $records = User::all();
        $response = [
            'message' => 'Users list retrieved successfully.',
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
    public function updateStatus(Request $request, $id)
    {
        $fields = $request->validate([
            'status' => 'required|numeric|boolean'
        ]);
        $user = User::find($id);
        $user->update(['status' => $fields['status']]);
        return response($user,200);
        
    }
    
}