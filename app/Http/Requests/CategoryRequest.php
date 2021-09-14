<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            if(request()->routeIs('categories.store')) {
                $nameRule = 'required|string|unique:categories|max:255';
            } elseif (request()->routeIs('categories.update')) {
                $id = $this->route('category');
                $nameRule ='required|string|max:255|unique:categories,name,' . $id;
            }
            return [
                'name' => $nameRule,
                'description' => 'required|string|max:255'
            ];
    }
}
