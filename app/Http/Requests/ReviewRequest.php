<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
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
        if(request()->routeIs('reviews.store')) {
            return [
                'rating' => 'required|numeric|between:1,5',
                'comment' => 'required|string|max:255',
                'order_detail_id' => 'required|integer|unique:reviews|exists:order_details,id'
            ];
        } elseif (request()->routeIs('reviews.update')) {
            return [
                'rating' => 'required|numeric|between:1,5',
                'comment' => 'required|string|max:255'
            ];
        }
        
    }
}
