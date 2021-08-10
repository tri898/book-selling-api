<?php

namespace App\Http\Resources\Another;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Another\Book as BookResource;

class BooksOfCategory extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'books' => BookResource::collection($this->whenLoaded('books')),
        ];
    }
}
