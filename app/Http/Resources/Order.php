<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\OrderDetail as OrderDetailResource;

class Order extends JsonResource
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
            'user' => $this->user->email,
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'total' => $this->total,
            'note' => $this->note,
            'status' => $this->status,
            'created_at' => $this->created_at->format('d/m/Y H:i:s'),
            'details' => OrderDetailResource::collection($this->whenLoaded('details')),
            
        ];
    }
}
