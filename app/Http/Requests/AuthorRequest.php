<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthorRequest extends FormRequest
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
        if(request()->routeIs('authors.store')) {
            $nameRule = 'required|string|unique:authors|max:255';
        } elseif (request()->routeIs('authors.update')) {
            $id = $this->route('author');
            $nameRule ='required|string|max:255|unique:authors,name,' . $id;
        }
        return [
            'name' => $nameRule,
            'description' => 'required|string',
            'image' => 'required|string|max:255'
        ];
    }
}
