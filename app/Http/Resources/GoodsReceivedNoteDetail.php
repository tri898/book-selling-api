<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GoodsReceivedNoteDetail extends JsonResource
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
            'book' => $this->whenLoaded('book', function () {
                return $this->book->name;
            }),
            'quantity' => $this->quantity,
            'import_unit_price' => $this->import_unit_price,

        ];
    }
}
