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
            'author' => $this->whenLoaded('author', function () {
                return $this->author->name;
            }),
            'description' => $this->description,
            'unit_price' => $this->unit_price,
            'discount' => $this->whenLoaded('discount', function () {
                return $this->discount->percent;
            }) ?? 0,
            'slug' => $this->slug,
            'image' => $this->whenLoaded('image', function () {
                return $this->image->front_cover;
            })
        ];
    }
}
