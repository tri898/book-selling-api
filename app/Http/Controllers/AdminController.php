<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\User as UserResource;
use Validator;
class AdminController extends BaseController
{
     /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
    public function usersList()
    { 
        $records = User::all();

        return $this->sendResponse('Users list retrieved successfully.', UserResource::collection($records),200);  
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
        $fields = $request->all();
        $validator = Validator::make($fields, [
            'status' => 'required|numeric|boolean'
        ]);

        $user = User::find($id);
        // check id user
        if (is_null($user)) {
            return $this->sendError('User not found.',[], 404); 
        }
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
        // update status
        $user->update(['status' => $fields['status']]);

        return $this->sendResponse('User status updated successfully.', new UserResource($user),200); 
    } 
}