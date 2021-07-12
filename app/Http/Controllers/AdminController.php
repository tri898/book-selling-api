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

        return $this->sendResponse('Danh sách người dùng được truy xuất thành công.', UserResource::collection($records),200);  
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
            return $this->sendError('Không tìm thấy người dùng.',[], 404); 
        }
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        // update status
        $user->update(['status' => $fields['status']]);

        return $this->sendResponse('Đã cập nhật trạng thái người dùng thành công.', new UserResource($user),200); 
    } 
}