<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Models\{Order, Inventory};
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
    public function index(Request $request)
    {
        $type = $request->input('type');
        if($request->has('type')) {
            $records = Order::where('status',$type)->with(['user','details.book'])
                              ->orderByDesc('id')->get();
        } else {
            $records = Order::with(['user','details.book'])->orderByDesc('id')->get();   
        }
               
        return $this->sendResponse('Truy xuất danh sách đơn hàng thành công.',
                                    OrderResource::collection($records),200);
    }
     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::with(['user','details.book'])->find($id);
  
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

        if($request->status === 2) {
            $getBookOrder = $order->details()->get(['book_id','quantity'])->toArray();
            // check quantity in stock
            foreach ($getBookOrder as $item) {
                $checkQuantity = Inventory::where('book_id', $item['book_id'])
                                            ->get('available_quantity');
    
                $quantity = $checkQuantity[0]['available_quantity'];
                if($quantity < $item['quantity']) {
                return $this->sendError('Có lỗi. Sách trong kho không đủ.', [], 409);       
               }
            };
            // update stock
            foreach ($getBookOrder as $item) {
                $decrease= Inventory::where('book_id', $item['book_id'])->decrement('available_quantity', $item['quantity']);
             };
            
        }
        $order->update(['status' => $fields['status']]);
        
        return $this->sendResponse('Đã cập nhật trạng thái đơn hàng thành công.',
                                    new OrderResource($order->load(['user','details.book'])),200); 
    }
   
}
