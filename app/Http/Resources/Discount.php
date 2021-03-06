<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Discount extends JsonResource
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
                return [
                    'id' => $this->book->id,
                    'name' => $this->book->name
                ];
            }),
            'quantity' => $this->whenLoaded('book', function () {
                return $this->book->inventory->available_quantity;
            }),
            'percent' => $this->percent,
            'created_at' => $this->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i:s')
        ];
    }
}
