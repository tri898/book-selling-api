<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Supplier as SupplierResource;
use Validator;

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
    public function store(Request $request)
    {
        $fields = $request->all();
        $validator = Validator::make($fields, [
            'name' => 'required|string|max:255|unique:suppliers',
            'address' => 'required|string|max:255',
            'phone' => 'required|numeric|digits:10',
            'email' => 'required|email|max:100',
            'description' => 'required|string|max:255'
        ]);
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        $supplier = Supplier::create([
            'name' => $fields['name'],
            'address' => $fields['address'],
            'phone' => $fields['phone'],
            'email' => $fields['email'],
            'description' =>$fields['description'],
            'slug' => Str::slug($fields['name'])
        ]);
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
    public function update(Request $request, $id)
    {
        $fields = $request->all();
        $validator = Validator::make($fields, [
            'name' => 'required|string|max:255|unique:suppliers,name,' . $id,
            'address' => 'required|string|max:255',
            'phone' => 'required|numeric|digits:10',
            'email' => 'required|email|max:100',
            'description' => 'required|string|max:255'
        ]);
        $supplier = Supplier::find($id);
  
        if (is_null($supplier)) {
            return $this->sendError('Không tìm thấy nhà cung cấp',[], 404); 
        }
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        $supplier->update([
            'name' => $fields['name'],
            'address' => $fields['address'],
            'phone' => $fields['phone'],
            'email' => $fields['email'],
            'description' =>$fields['description'],
            'slug' => Str::slug($fields['name'])
        ]);
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
