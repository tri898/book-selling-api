<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GRNRequest extends FormRequest
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
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'total' => 'required|integer|min: 1',
            'items' => 'required|array|min: 1',
            'items.*.book_id' => 'required|integer|distinct|exists:books,id',
            'items.*.quantity' => 'required|integer|min: 1',
            'items.*.import_unit_price' => 'required|integer|min: 1'
        ];
    }
}
