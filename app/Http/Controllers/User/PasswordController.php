<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\{User, PasswordReset};
use App\Http\Requests\PasswordRequest;
use App\Http\Controllers\BaseController as BaseController;
use Mail;

class PasswordController extends BaseController
{
    
    public function changePassword(PasswordRequest $request)
    {
        $fields = $request->validated();

        $user = auth()->user();
        // check old password
        $checkPassword = Hash::check($fields['old_password'], $user->password);

        if($checkPassword) {
            $user->update(['password' => bcrypt($fields['new_password'])]);
            $user->tokens()->delete();
            return $this->sendResponse('Thay đổi mật khẩu thành công.', [],200);
        }
        return $this->sendError('Mật khẩu cũ không chính xác.',[], 401); 
       
    }
    public function forgotPassword(PasswordRequest $request)
    {
        $fields = $request->validated();

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
        $url = 'https://staciabook.store/khoi-phuc-mat-khau?token=';
        //send email confirmation
        if($passwordReset) {
           
            Mail::send('Mails.forgot', ['token' => $passwordReset->token, 'url' => $url], function ($m) use ($email) {
                $m->from('staciabook@gmail.com', 'Bookstore'); 
                $m->to($email);
                $m->subject('Lấy lại mật khẩu!');
            });
          
            return $this->sendResponse('Đã gửi liên kết xác nhận. Xin vui lòng kiểm tra email của bạn.', [],200);
        }
        

    }
    public function recoverPassword(PasswordRequest $request,$token)
    {
        $fields = $request->validated();
        
        $passwordReset = PasswordReset::where('token',$token)->first();

        if($passwordReset) {
            if (Carbon::parse($passwordReset->updated_at)->addMinutes(5)->isPast()) {
                $passwordReset->delete();

                return $this->sendError('Mã xác thực đặt lại mật khẩu này đã hết hạn.',[], 401); 
            }
            if($request->has('password')) {
                 //update password
                $user = User::where('email', $passwordReset->email)->first();
                $newPassword = bcrypt($fields['password']);
                $updatePasswordUser = $user->update(['password' => $newPassword]);
                $passwordReset->delete();
                $user->tokens()->delete();
                return $this->sendResponse('Thay đổi mật khẩu của bạn thành công', [],200);
            }
            else
                return $this->sendResponse('Mã xác thực đặt lại mật khẩu này hợp lệ.', [],200);;
        }
        else {
             return $this->sendError('Mã xác thực đặt lại mật khẩu này không hợp lệ.',[], 401); 
        }
    }
}