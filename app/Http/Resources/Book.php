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
            'author_id' => $this->author_id,
            'publisher_id' => $this->publisher_id,
            'supplier_id' => $this->supplier_id
        ];
    }
}
