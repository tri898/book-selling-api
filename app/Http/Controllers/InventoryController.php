<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Inventory as InventoryResource;
use Validator;

class InventoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = Inventory::all();         
        return $this->sendResponse('Danh sách tồn kho được truy xuất thành công.', InventoryResource::collection($records),200); 
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
            'book_id' => 'required|integer|unique:inventories',
            'available_quantity' => 'required|integer'
        ]);
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        $inventory = Inventory::create([
            'book_id' => $fields['book_id'],
            'available_quantity' =>$fields['available_quantity']
        ]);
        return $this->sendResponse('Tạo số lượng tồn thành công', new InventoryResource($inventory),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $inventory = Inventory::find($id);
  
        if (is_null($inventory)) {
            return $this->sendError('Không tìm thấy số lượng tồn',[], 404); 
        }
        return $this->sendResponse('Danh sách tồn kho được truy xuất thành công.', new InventoryResource($inventory),200);
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
            'book_id' => 'required|integer|unique:inventories,book_id,' . $id,
            'available_quantity' => 'required|integer'     
        ]);
        
        $inventory = Inventory::find($id);
        if (is_null($inventory)) {
            return $this->sendError('Không tìm thấy số lượng tồn',[], 404); 
        }

        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        
        $inventory->update([
            'book_id' => $fields['book_id'],
            'available_quantity' =>$fields['available_quantity']
        ]);
        return $this->sendResponse('Cập nhật số lượng tồn thành công',  new InventoryResource($inventory),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $inventory = Inventory::find($id);
        if (is_null($inventory)) {
            return $this->sendError('Không tìm thấy số lượng tồn',[], 404); 
        }
        $inventory->delete();
        return $this->sendResponse('Xóa thành công', [],204);
    }
    public function search($name)
    {
        // $book_category=  BookCategory::where('name', 'like', '%'.$name.'%')->get();

        // return $this->sendResponse('Found the results.', BookCategoryResource::collection($book_category),200);
    }
}
