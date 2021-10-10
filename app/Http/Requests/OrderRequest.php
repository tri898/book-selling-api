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
                'note' => 'string|nullable|max:255',
                'items' => 'required|array|min: 1',
                'items.*.book_id' => 'required|integer|distinct|exists:books,id',
                'items.*.quantity' => 'required|integer|min: 1',
                'items.*.price' => 'required|integer|min: 1',
                'items.*.discount' => 'required|integer|min: 0|max: 100',
            ];
        } elseif (request()->routeIs('orders.update')) {
            return [
                'status' => [
                    'required',
                    Rule::in(['Chờ xác nhận','Đã xác nhận','Đang giao','Giao thành công', 'Giao thất bại']),
                ]
            ];
        }
        
    }
}
