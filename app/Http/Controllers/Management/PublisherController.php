<?php

namespace App\Http\Controllers\Management;

use App\Models\Publisher;
use App\Http\Requests\PublisherRequest;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Publisher as PublisherResource;

class PublisherController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records =  Publisher::all();         
        return PublisherResource::collection($records);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PublisherRequest $request)
    {
        $fields = $request->validated(); 
        $publisher = Publisher::create($fields);
        return $this->sendResponse('Nhà xuất bản được tạo thành công.',  new PublisherResource($publisher),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $publisher = Publisher::find($id);
  
        if (is_null($publisher)) {
            return $this->sendError('Không tìm thấy nhà xuất bản',[], 404); 
        }
        return new PublisherResource($publisher);  
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PublisherRequest $request, $id)
    {
        $fields = $request->validated(); 
        $publisher = Publisher::find($id);
        if (is_null($publisher)) {
            return $this->sendError('Không tìm thấy nhà xuất bản',[], 404); 
        }
        $publisher->update($fields);
        return $this->sendResponse('Đã cập nhật nhà xuất bản thành công.',   new PublisherResource($publisher),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $publisher = Publisher::find($id);
        if (is_null($publisher)) {
            return $this->sendError('Không tìm thấy nhà xuất bản',[], 404); 
        }
        if ($publisher->books()->count()) {
            return $this->sendError('Không thể xóa do có liên kết đến sách.',[], 409); 
        }
        $publisher->delete();
        return $this->sendResponse('Xóa thành công', [],204);
    }
    
}
