<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublisherRequest extends FormRequest
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
        if(request()->routeIs('publishers.store')) {
            $nameRule = 'required|string|unique:publishers|max:255';
        } elseif (request()->routeIs('publishers.update')) {
            $id = $this->route('publisher');
            $nameRule ='required|string|max:255|unique:publishers,name,' . $id;
        }
        return [
            'name' => $nameRule,
            'description' => 'required|string|max:255'
        ];
    }
}
