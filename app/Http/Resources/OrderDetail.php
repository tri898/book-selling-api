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
            'book' => $this->whenLoaded('book', function () {
                return $this->book->name;
            }),
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'sale_price' => $this->sale_price,
            'review_status' => $this->review_status
        ];
    }
}
