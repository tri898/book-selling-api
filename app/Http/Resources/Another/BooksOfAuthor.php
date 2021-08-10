<?php

namespace App\Http\Resources\Another;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Another\Book as BookResource;

class BooksOfAuthor extends JsonResource
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
            'description' => $this->description,
            'slug' => $this->slug,
            'books' => BookResource::collection($this->whenLoaded('books')),
        ];
    }
}
