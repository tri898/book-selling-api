<?php

namespace App\Http\Resources\Another;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class Slider extends JsonResource
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
            'start_date' => Carbon::parse($this->start_date)->format('d/m/yy'),
            'end_date' =>Carbon::parse($this->end_date)->format('d/m/yy'),         
            'image' => $this->image
        ];
    }
}
