<?php

namespace App\Http\Resources\Another;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Another\BookCategory as BookCategoryResource;

class BookDetails extends JsonResource
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
            'translator' => $this->translator,
            'quantity' => $this->inventory->available_quantity,
            'discount' => $this->discount->percent ?? '',
            'author' => [
                'name' => $this->author->name,
                'slug' => $this->author->slug,
            ],
            'publisher' => $this->publisher->name,
            'supplier' => $this->supplier->name,
            'category' => new  BookCategoryResource($this->whenLoaded('bookCategory')),
            'image' =>  [
                'front_cover' => $this->image->front_cover,
                'back_cover' => $this->image->back_cover
            ]
        ];
    }
}
