<?php

namespace App\Http\Controllers\Management;

use App\Models\Order;
use App\Http\Requests\OrderRequest;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Order as OrderResource;

class OrderController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = Order::with('details')->orderByDesc('id')->get();         
        return OrderResource::collection($records);
    }
     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::with('details')->find($id);
  
        if (is_null($order)) {
            return $this->sendError('Không tìm thấy đơn hàng',[], 404); 
        }
        return new OrderResource($order);  
    }
   
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateOrderStatus(OrderRequest $request, $id)
    {
        $fields = $request->validated(); 

        $order = Order::find($id);
        if(is_null($order)) {
            return $this->sendError('Không tìm thấy đơn hàng',[], 404);
        }
        // update status
        $order->update(['status' => $fields['status']]);
        
        return $this->sendResponse('Đã cập nhật trạng thái đơn hàng thành công.', new OrderResource($order->load('details')),200); 
    }
   
}
