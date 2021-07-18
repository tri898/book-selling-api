<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discount;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Discount as DiscountResource;
use Validator;

class DiscountController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = Discount::paginate(10);         
        return DiscountResource::collection($records);
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
            'book_id' => 'required|integer|unique:discounts',
            'percent' => 'required|integer'
        ]);
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        $discount = Discount::create([
            'book_id' => $fields['book_id'],
            'percent' =>$fields['percent']
        ]);
        return $this->sendResponse('Tạo giảm giá thành công.', new DiscountResource($discount),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $discount = Discount::find($id);
  
        if (is_null($discount)) {
            return $this->sendError(' Không tìm thấy giảm giá',[], 404); 
        }
        return new DiscountResource($discount);
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
            'book_id' => 'required|integer|unique:discounts,book_id,' . $id,
            'percent' => 'required|integer'     
        ]);
        
        $discount = Discount::find($id);
        if (is_null($discount)) {
            return $this->sendError('Không tìm thấy giảm giá',[], 404); 
        }

        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        
        $discount->update([
            'book_id' => $fields['book_id'],
            'percent' =>$fields['percent']
        ]);
        return $this->sendResponse('Đã cập nhật giảm giá thành công.',  new DiscountResource($discount),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $discount = Discount::find($id);
        if (is_null($discount)) {
            return $this->sendError('Không tìm thấy giảm giá',[], 404); 
        }
        $discount->delete();
        return $this->sendResponse('Đã xóa giảm giá thành công.', [],204);
    }
    public function search($name)
    {
        // $book_category=  BookCategory::where('name', 'like', '%'.$name.'%')->get();

        // return $this->sendResponse('Found the results.', BookCategoryResource::collection($book_category),200);
    }
}
