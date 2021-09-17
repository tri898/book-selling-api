<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if(request()->routeIs('password.change')) {
            return [
                'old_password' => 'required|string|min:6|max:100',
                'new_password' => 'required|string|min:6|max:100|confirmed'
            ];
        }
        if(request()->routeIs('password.forgot')) {
            return [
                'email' => 'required|email|max:100'
            ];
        } elseif (request()->routeIs('password.recover')) {
            return [
                'password' => 'string|min:6|max:100|confirmed'
            ];
        }
        
    }
}
