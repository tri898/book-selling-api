<?php

namespace App\Http\Controllers\Management;

use Illuminate\Support\Str;
use App\Models\Supplier;
use App\Http\Requests\SupplierRequest;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Supplier as SupplierResource;

class SupplierController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records =  Supplier::all();         
        return SupplierResource::collection($records);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SupplierRequest $request)
    {
        $fields = $request->validated(); 
        $customValues = $fields + ['slug' => Str::slug($fields['name'])];

        $supplier = Supplier::create($customValues);
        return $this->sendResponse('Nhà cung cấp tạo thành công.', new SupplierResource($supplier),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $supplier = Supplier::find($id);
  
        if (is_null($supplier)) {
            return $this->sendError('Không tìm thấy nhà cung cấp',[], 404); 
        }
        return new SupplierResource($supplier);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SupplierRequest $request, $id)
    {
        $fields = $request->validated(); 
        $customValues = $fields + ['slug' => Str::slug($fields['name'])];

        $supplier = Supplier::find($id);
        if (is_null($supplier)) {
            return $this->sendError('Không tìm thấy nhà cung cấp',[], 404); 
        }

        $supplier->update($customValues);
        return $this->sendResponse('Đã cập nhật nhà cung cấp thành công.',  new SupplierResource($supplier),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $supplier = Supplier::find($id);
        
        if (is_null($supplier)) {
            return $this->sendError('Không tìm thấy nhà cung cấp',[], 404); 
        }
        if($supplier->books()->count()) {
            return $this->sendError('Không thể xóa do có liên kết đến sách.',[], 409); 
        }
        if($supplier->goodsReceivedNotes()->count()) {
            return $this->sendError('Không thể xóa do có liên kết đến kho.',[], 409); 
        }
        $supplier->delete();
        return $this->sendResponse('Xóa thành công.', [],204);
    }
     
}
