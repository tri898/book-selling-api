<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Controllers\BaseController as BaseController;
use Validator;
use Mail;

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
    public function updatePersonalData(Request $request)
    {
        $fields = $request->all();
        $validator = Validator::make($fields, [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|numeric|digits:10',
            // 'email' => 'required|email|unique:users,email, ' .auth()->user()->id

        ]);
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        $user = auth()->user();
        $user->update([
            'name' => $fields['name'],
            'address' => $fields['address'],
            'phone' => $fields['phone'],
            // 'email' => $fields['email']
            ]);
            //record data
        $records['name'] = $user->name;
        $records['address'] = $user->address;
        $records['phone'] = $user->phone;
       

        return $this->sendResponse('Hồ sơ đã được cập nhật thành công', $records,200);
        
    }
    
}