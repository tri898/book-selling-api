<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SliderRequest extends FormRequest
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
        if(request()->routeIs('sliders.store')) {
            $nameRule = 'required|string|max:255';
            $bookRule = 'required|integer|unique:sliders|exists:books,id';
        } elseif (request()->routeIs('sliders.update')) {
            $id = $this->route('slider');
            $nameRule ='required|string|max:255';
            $bookRule ='required|integer|exists:books,id|unique:sliders,book_id,' . $id;
        }
        return [
            'name' => $nameRule,
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'book_id' => $bookRule,
            'image' => 'required|string|max: 255'    
        ];
    }
}
