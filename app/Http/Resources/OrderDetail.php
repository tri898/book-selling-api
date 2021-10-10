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
            'id' => $this->id,
            'book' => $this->book->name,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'discount' => $this->discount,
            'review_status' => $this->review_status
        ];
    }
}
