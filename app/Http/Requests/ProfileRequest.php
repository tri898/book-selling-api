<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'address' => 'string|nullable|max:255',
            'phone' => 'numeric|nullable|digits:10',
            'image' => 'string|max:255|nullable'
            // 'email' => 'required|email|unique:users,email, ' .auth()->user()->id
        ];
    }
}
