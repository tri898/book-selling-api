<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GoodsReceivedNote extends JsonResource
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
            'book_id' => $this->book->name,
            'quantity' => $this->quantity,
            'import_unit_price' => $this->import_unit_price,
            'supplier_id' => $this->supplier->name,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at->format('d/m/Y')

        ];
    }
}
