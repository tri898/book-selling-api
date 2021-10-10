<?php

namespace App\Http\Resources;

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
            'code' => $this->code,
            'description' => $this->description,
            'unit_price' => $this->unit_price,
            'weight' => $this->weight,
            'format' => $this->format,
            'release_date' => $this->release_date,
            'language' => $this->language,
            'size' => $this->size,
            'num_pages' => $this->num_pages,
            'slug' => $this->slug,
            'translator' => $this->translator ?? '',
            'quantity' => $this->inventory->available_quantity,
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->name
            ],
            'publisher' => [
                'id' => $this->publisher->id,
                'name' => $this->publisher->name
            ],
            'supplier' => [
                'id' => $this->supplier->id,
                'name' => $this->supplier->name
            ],
            'category' => [
                'id' => $this->bookCategory->category->id,
                'name' => $this->bookCategory->category->name
            ],
            'image' =>  [
                'front_cover' => $this->image->front_cover,
                'back_cover' => $this->image->back_cover
            ],
            'created_at' => $this->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i:s')
        ];
    }
}
