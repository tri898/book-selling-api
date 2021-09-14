<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscountRequest extends FormRequest
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
        if(request()->routeIs('discounts.store')) {
            $bookRule = 'required|integer|unique:discounts|exists:books,id';
        } elseif (request()->routeIs('discounts.update')) {
            $id = $this->route('discount');
            $bookRule ='required|integer|exists:books,id|unique:discounts,book_id,' . $id;
        }
        return [
            'book_id' => $bookRule,
            'percent' => 'required|integer|min: 1|max: 100'    
        ];
    }
}
