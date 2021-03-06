<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
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
        

        if(request()->routeIs('books.store')) {
            $nameRule = 'required|string|max:255|unique:books';
        } elseif (request()->routeIs('books.update')) {
            $id = $this->route('book');
            $nameRule ='required|string|max:255|unique:books,name,' . $id;
        }
        return [
            'name' => $nameRule,
            'code' => 'required|string|max:255',
            'description' => 'required|string',
            'unit_price' => 'required|integer',
            'weight' => 'required|numeric|between:0.00,999.99',
            'format' => 'required|string|max:255',
            'release_date' => 'required|integer|min:1900|max:2090',
            'language' => 'required|string|max:255',
            'size' => 'required|string|max:255',
            'num_pages' => 'required|integer',
            'translator' => 'string|nullable|max:255',
            'author_id' => 'required|integer|exists:authors,id',
            'publisher_id' => 'required|integer|exists:publishers,id',
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'category_id' => 'required|integer|exists:categories,id',
            'front_cover' => 'required|string|max:255',
            'back_cover' => 'required|string|max:255',
        ];     
    }
}
