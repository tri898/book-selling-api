<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\BaseController as BaseController;
use App\Models\Slider;
use App\Http\Resources\Another\Slider as SliderResource;

class SliderController extends BaseController
{
    public function index()
    {
        $records = Slider::with(['book:id,slug'])
            ->orderByDesc('id')->limit(5)
            ->get(['book_id','name','start_date','end_date','image']);
       
        return $this->sendResponse('Truy xuất danh sách slider thành công.',
                                    SliderResource::collection($records),200); 
    }
}
