<?php

namespace App\Http\Controllers\Management;

use App\Models\User;
use App\Http\Requests\UpdateUserStatusRequest;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\User as UserResource;

class UserController extends BaseController
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function getUserList()
    { 
        $records = User::orderByDesc('id')->get();
        return $this->sendResponse('Truy xuất danh sách người dùng thành công.',
                                    UserResource::collection($records),200);
    }
    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function updateUserStatus(UpdateUserStatusRequest $request, $id)
    {
        $fields = $request->validated();

        $user = User::find($id);
        // check id user
        if (is_null($user)) {
            return $this->sendError('Không tìm thấy người dùng.',[], 404); 
        }
        // update status
        $user->update(['status' => $fields['status']]);
        if($fields['status'] == 0) {
            $user->tokens()->delete();
        }
        return $this->sendResponse('Đã cập nhật trạng thái người dùng thành công.',
                                    new UserResource($user),200); 
    } 
}