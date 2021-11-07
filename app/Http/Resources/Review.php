<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Review extends JsonResource
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
            'order_detail_id' => $this->order_detail_id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'user' => $this->whenLoaded('orderDetails', function () {
                return [
                    'name' => $this->orderDetails->order->user->name,
                    'image' => $this->orderDetails->order->user->image                    
                ];
            }),            
            'created_at' => $this->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i:s')
        ];
    }
}
