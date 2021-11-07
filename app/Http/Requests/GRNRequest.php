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
            'formality' => 'required|numeric|min:1|max:2',
            'supplier_id' => 'integer|exists:suppliers,id|nullable',
            'total' => 'required|integer|min: 0',
            'note' => 'required|string|max:255',
            'items' => 'required|array|min: 1',
            'items.*.book_id' => 'required|integer|distinct|exists:books,id',
            'items.*.quantity' => 'required|integer|min: 1',
            'items.*.import_unit_price' => 'required|integer|min: 0'
        ];
    }
}
