<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mail;
use Carbon\Carbon;
use App\Models\User;
use App\Models\PasswordReset;
use App\Http\Controllers\BaseController as BaseController;
use Validator;

class UserController extends BaseController
{
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
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
    public function updateProfile(Request $request)
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
    public function changePassword(Request $request)
    {
        $fields = $request->all();
        $validator = Validator::make($fields, [
            'old_password' => 'required|string|min:6|max:100',
            'new_password' => 'required|string|min:6|max:100|confirmed'
        ]);
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        $user = auth()->user();
        // check old password
        $checkPassword = Hash::check($fields['old_password'], $user->password);

        if($checkPassword) {
            $user->update([        
                'password' => bcrypt($fields['new_password'])
                ]);

                return $this->sendResponse('Thay đổi mật khẩu thành công.', [],200);
        }
         return $this->sendError('Mật khẩu cũ không chính xác.',[], 401); 
       
    }
    public function forgotPassword(Request $request)
    {
        // validation
        $fields = $request->all();
        $validator = Validator::make($fields, [
            'email' => 'required|email|max:100'
        ]);
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
          // Check email exists
          $user = User::where('email', $fields['email'])->first();
          if(!$user) {
              return $this->sendError('Email không tồn tại.',[], 404); 
          }
          //Check status user
          $checkStatus= User::where('email', $fields['email'])->where('status',1)->first();     //isActive: 1
            if($checkStatus) {
                $email = $checkStatus->email;  
            }
            else {
                return $this->sendError('Người dùng đã bị vô hiệu hóa.',[], 401); 
            }
            //create or update token to table forgot password
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $email],
            ['token' => Str::random(30)]
        );
        //send email confirmation
        if($passwordReset) {
           
            Mail::send('Mails.forgot', ['token' => $passwordReset->token], function ($m) use ($email) {
                $m->from('thientri2312@gmail.com', 'Bookstore'); 
                $m->to($email);
                $m->subject('Lấy lại mật khẩu!');
            });
          
            return $this->sendResponse('Đã gửi liên kết xác nhận. Xin vui lòng kiểm tra email của bạn.', [],200);
        }
        

    }
    public function recoverPassword(Request $request,$token)
    {
        // validation
        $fields = $request->all();
        $validator = Validator::make($fields, [
            'password' => 'string|min:6|max:100|confirmed'
        ]);
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        $passwordReset = PasswordReset::where('token',$token)->first();

        if($passwordReset) {
            if (Carbon::parse($passwordReset->updated_at)->addMinutes(5)->isPast()) {
                $passwordReset->delete();

                return $this->sendError('Mã thông báo đặt lại mật khẩu này đã hết hạn.',[], 401); 
            }
            if($request->has('password')) {
                 //update password
                $user = User::where('email', $passwordReset->email)->first();
                $newPassword = bcrypt($fields['password']);
                $updatePasswordUser = $user->update(['password' => $newPassword]);
                $passwordReset->delete();

                return $this->sendResponse('Thay đổi mật khẩu của bạn thành công', [],200);
            }
        }
        else {
             return $this->sendError('Mã thông báo đặt lại mật khẩu này không hợp lệ.',[], 401); 
        }
    }
}
