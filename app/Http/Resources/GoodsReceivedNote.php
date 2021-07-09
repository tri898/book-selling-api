<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\GoodsReceivedNoteDetail as GRNDetailResource;

class GoodsReceivedNote extends JsonResource
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
            'supplier' => $this->supplier->name,
            'by_admin' => $this->admin->name,
            'details' => GRNDetailResource::collection($this->details),
            'created_at' => $this->created_at->format('d/m/Y')
        ];
    }
}
