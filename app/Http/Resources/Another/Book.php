<?php

namespace App\Http\Resources\Another;

use Illuminate\Http\Resources\Json\JsonResource;

class Book extends JsonResource
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
            'author' => $this->author->name,
            'description' => $this->description,
            'unit_price' => $this->unit_price,
            'discount' => $this->discount->percent ?? '',
            'slug' => $this->slug,
            'image' =>  [
                'front_cover' => $this->image->front_cover,
                'back_cover' => $this->image->back_cover
            ]
        ];
    }
}
