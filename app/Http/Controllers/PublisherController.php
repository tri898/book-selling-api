<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publisher;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Publisher as PublisherResource;
use Validator;

class PublisherController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records =  Publisher::paginate(10);         
                return PublisherResource::collection($records);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields = $request->all();
        $validator = Validator::make($fields, [
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:1000'
        ]);
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        $publisher = Publisher::create([
            'name' => $fields['name'],
            'description' =>$fields['description']
        ]);
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
    public function update(Request $request, $id)
    {
        $fields = $request->all();
        $validator = Validator::make($fields, [
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:1000'
        ]);
        $publisher = Publisher::find($id);
        if (is_null($publisher)) {
            return $this->sendError('Không tìm thấy nhà xuất bản',[], 404); 
        }
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        $publisher->update([
            'name' => $fields['name'],
            'description' =>$fields['description']
        ]);
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
       if( $publisher->books()->count()) {
        return $this->sendError('Không thể xóa do có liên kết đến sách.',[], 409); 
       }
        $publisher->delete();
        return $this->sendResponse('Xóa thành công', [],204);
    }
    
}
