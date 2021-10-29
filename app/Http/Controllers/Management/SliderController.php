<?php

namespace App\Http\Controllers\Management;

use App\Models\Slider;
use App\Http\Requests\SliderRequest;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Slider as SliderResource;
use Carbon\Carbon;

class SliderController extends BaseController
{
    private $query = [
        'book:id,name'
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = Slider::with($this->query)
                           ->orderByDesc('id')
                           ->get();        

        return $this->sendResponse('Truy xuất danh sách slider thành công.',
                                    SliderResource::collection($records),200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SliderRequest $request)
    {
        $fields = $request->validated();
        
        $slider = Slider::create($fields);
        return $this->sendResponse('Tạo slider thành công.',
                                    new SliderResource($slider->load($this->query)),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $slider = Slider::with($this->query)->find($id);
  
        if (is_null($slider)) {
            return $this->sendError(' Không tìm thấy slider',[], 404); 
        }
        return new SliderResource($slider);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SliderRequest $request, $id)
    {
        $fields = $request->validated();

        $slider = Slider::find($id);
        if (is_null($slider)) {
            return $this->sendError('Không tìm thấy slider',[], 404); 
        }

        $slider->update($fields);
        return $this->sendResponse('Đã cập nhật slider thành công.',
                                    new SliderResource($slider->load($this->query)),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $slider = Slider::find($id);
        if (is_null($slider)) {
            return $this->sendError('Không tìm thấy slider',[], 404); 
        }
        $slider->delete();
        return $this->sendResponse('Đã xóa slider thành công.', [],204);
    }
}
