<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class SellingBook extends JsonResource
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
            'name' => $this->name,
            'sold' => $this->orders_sum_order_detailsquantity ?? 0,
            'quantity_in_stock' => $this->whenLoaded('inventory', function () {
                return $this->inventory->available_quantity;
            })
        ];
    }
}
