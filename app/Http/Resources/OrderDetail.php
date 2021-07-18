<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'book' => $this->book->name,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'discount' => $this->discount
        ];
    }
}
