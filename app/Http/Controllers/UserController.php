<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordReset;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mail;
use Carbon\Carbon;
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
     
        return $this->sendResponse('Profile retrieved successfully', $records,200);
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
            'phone' => 'required|numeric|digits:10',
            'email' => 'required|email|email|max:100'

        ]);
        $user = auth()->user();
        $user->update([
            'name' => $fields['name'],
            'address' => $fields['address'],
            'phone' => $fields['phone'],
            'email' => $fields['email']
            ]);

        $records['name'] = $user->name;
        $records['address'] = $user->address;
        $records['phone'] = $user->phone;
        $records['email'] = $user->email;

        return $this->sendResponse('Profile user updated successfully.', $records,200);
        
    }
    public function changePassword(Request $request) {
        $fields = $request->validate([
            'old_password' => 'required|string|min:6|max:100',
            'new_password' => 'required|string|min:6|max:100|confirmed'
        ]);
        $user = auth()->user();
        $checkPassword = Hash::check($fields['old_password'], $user->password);

        if($checkPassword) {
            $user->update([        
                'password' => bcrypt($fields['new_password'])
                ]);

                return $this->sendResponse('Change password successfully.', [],200);
        }
         return $this->sendError('The current password is incorrect.', 401); 
       
    }
    public function forgotPassword(Request $request)
    {
        // validation
        $fields = $request->validate([
            'email' => 'required|email|max:100'
        ]);
      
          // Check user exist
          $user = User::where('email', $fields['email'])->first();
          if(!$user) {
              return $this->sendError('User does not exist.', 200); 
          }
          //Check status user
          $checkStatus= User::where('email', $fields['email'])->where('status',1)->first();     //isActive: 1
            if($checkStatus) {
                $email = $checkStatus->email;  
            }
            else {
                return $this->sendError('User has been disabled.', 200); 
            }
            //add token to database
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $email],
            ['token' => Str::random(30)]
        );
        //send email confirmation
        if($passwordReset) {
           
            Mail::send('Mails.forgot', ['token' => $passwordReset->token], function ($m) use ($email) {
                $m->from('thientri2312@gmail.com', 'Bookstore'); 
                $m->to($email);
                $m->subject('Reset your password!');
            });
          
            return $this->sendResponse('Sent confirmation link. Please check your email.', [],200);
        }
        

    }
    public function recoverPassword(Request $request,$token)
    {
        // validation
        $fields = $request->validate([
            'password' => 'string|min:6|max:100|confirmed'
        ]);

        $passwordReset = PasswordReset::where('token',$token)->first();

        if($passwordReset) {
            if (Carbon::parse($passwordReset->updated_at)->addMinutes(5)->isPast()) {
                $passwordReset->delete();

                return $this->sendError('This password reset token has expired.', 401); 
            }
            if($request->has('password')) {
                 //update password
                $user = User::where('email', $passwordReset->email)->first();
                $newPassword = bcrypt($fields['password']);
                $updatePasswordUser = $user->update(['password' => $newPassword]);
                $passwordReset->delete();

                return $this->sendResponse('Change your password successfully', [],200);
            }
        }
        else {
             return $this->sendError('This password reset token is invalid.', 401); 
        }
    }
}
