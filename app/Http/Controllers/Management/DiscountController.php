<?php

namespace App\Http\Controllers\Management;

use App\Models\Discount;
use App\Http\Requests\DiscountRequest;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Discount as DiscountResource;

class DiscountController extends BaseController
{
    private $query = [
        'book:id,name',
        'book.inventory:book_id,available_quantity'
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = Discount::with($this->query)
                            ->orderByDesc('id')->get();   

        return $this->sendResponse('Truy xuất danh sách giảm giá thành công.',
                                    DiscountResource::collection($records),200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DiscountRequest $request)
    {
        $fields = $request->validated();
        $discount = Discount::create($fields);
        return $this->sendResponse('Tạo giảm giá thành công.',
                                    new DiscountResource($discount->load($this->query)),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $discount = Discount::with($this->query)->find($id);
  
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
    public function update(DiscountRequest $request, $id)
    {
        $fields = $request->validated();
        
        $discount = Discount::find($id);
        if (is_null($discount)) {
            return $this->sendError('Không tìm thấy giảm giá',[], 404); 
        }

        $discount->update($fields);
        return $this->sendResponse('Đã cập nhật giảm giá thành công.',
                                    new DiscountResource($discount->load($this->query)),200);
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
    
}
