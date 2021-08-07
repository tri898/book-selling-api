<?php

namespace App\Http\Resources\Another;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Inventory as InventoryResource;
use App\Http\Resources\BookCategory as BookCategoryResource;
use App\Http\Resources\Image as ImageResource;

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
            'author' => $this->author->name,
            'publisher' => $this->publisher->name,
            'supplier' => $this->supplier->name,
            'quantity' => $this->inventory->available_quantity,
            'category' => $this->bookCategory->category_id,
            'image' =>  new ImageResource($this->whenLoaded('image')),
            'created_at' => $this->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i:s')
        ];
    }
}
