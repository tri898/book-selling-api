<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\PasswordReset;
use App\Http\Controllers\BaseController as BaseController;
use Validator;
use Mail;

class PasswordController extends BaseController
{
    
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