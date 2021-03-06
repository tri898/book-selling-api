<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
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
        if(request()->routeIs('user-orders.store')) {
            return [
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => 'required|numeric|digits:10',
                'total' => 'required|integer|min: 1',
                'shipping_fee' => 'required|integer|min: 1',
                'total_payment' => 'required|integer|min: 1',
                'note' => 'string|nullable|max:255',
                'items' => 'required|array|min: 1',
                'items.*.book_id' => 'required|integer|distinct|exists:books,id',
                'items.*.quantity' => 'required|integer|min: 1',
                'items.*.unit_price' => 'required|integer|min: 1',
                'items.*.sale_price' => 'required|integer|min: 1',
            ];
        }
        if(request()->routeIs('shipper-orders.update')) {
            return [
                'status' =>'required|numeric|min:4|max:5', 
            ];
        } elseif (request()->routeIs('orders.update')) {
            return [
                'status' =>'required|numeric|min:2|max:6', 
            ];
        }
        
    }
}
