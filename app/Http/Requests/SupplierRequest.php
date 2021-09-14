<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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
        if(request()->routeIs('suppliers.store')) {
            $nameRule = 'required|string|max:255|unique:suppliers';
        } elseif (request()->routeIs('suppliers.update')) {
            $id = $this->route('supplier');
            $nameRule ='required|string|max:255|unique:suppliers,name,' . $id;
        }
        return [
            'name' => $nameRule,
            'address' => 'required|string|max:255',
            'phone' => 'required|numeric|digits:10',
            'email' => 'required|email|max:100',
            'description' => 'required|string|max:255'
        ];
    }
}
