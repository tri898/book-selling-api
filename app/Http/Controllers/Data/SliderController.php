<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\BaseController as BaseController;
use App\Models\Slider;
use App\Http\Resources\Another\Slider as SliderResource;

class SliderController extends BaseController
{
    public function index()
    {
        $records = Slider::orderByDesc('id')->limit(5)
                           ->get(['name','start_date','end_date','image']);
       
        return $this->sendResponse('Truy xuất danh sách slider thành công.',
                                    SliderResource::collection($records),200); 
    }
}
