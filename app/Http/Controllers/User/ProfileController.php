<?php

namespace App\Http\Controllers\User;


use App\Models\User;
use App\Http\Requests\ProfileRequest;
use App\Http\Controllers\BaseController as BaseController;

class ProfileController extends BaseController
{
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPersonalData()
    {

        $user = auth()->user();
        
        $records['name'] = $user->name;
        $records['address'] = $user->address;
        $records['phone'] = $user->phone;
        $records['email'] = $user->email;
     
        return $this->sendResponse('Hồ sơ được truy xuất thành công', $records,200);
    }
     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePersonalData(ProfileRequest $request)
    {
        $fields = $request->validated();

        $user = auth()->user();
        $user->update($fields);
        //record data
        $records['name'] = $user->name;
        $records['address'] = $user->address;
        $records['phone'] = $user->phone;
    
        return $this->sendResponse('Hồ sơ đã được cập nhật thành công', $records,200);
        
    }
    
}