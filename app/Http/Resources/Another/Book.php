<?php

namespace App\Http\Resources\Another;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Image as ImageResource;

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
            'discount' => $this->discount,
            'slug' => $this->slug,
            'image' =>  new ImageResource($this->whenLoaded('image')),
        ];
    }
}
