<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Models\{Order, Inventory};
use App\Http\Requests\OrderRequest;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Order as OrderResource;

class OrderController extends BaseController
{
    private $query = [
        'user:id,email',
        'details.book:id,name'
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->input('type');
        if($request->has('type')) {
            $records = Order::where('status',$type)->with($this->query)
                              ->orderByDesc('id')->get();
        } else {
            $records = Order::with($this->query)->orderByDesc('id')->get();   
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
        $order = Order::with($this->query)->find($id);
  
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

        $order = Order::whereIn('status', [1,2,3,4,5,6])->find($id);
        if(is_null($order)) {
            return $this->sendError('Không tìm thấy đơn hàng',[], 404);
        }
        if($request->status == 2) {
            if($order->status == 1) {
                $getQtyOrder = $order->details()->get(['book_id','quantity'])->toArray();
                // check quantity in stock
                foreach ($getQtyOrder as $item) {
                    $getQtyInventory = Inventory::where('book_id', $item['book_id'])
                                                ->get('available_quantity');
    
                    $quantity = $getQtyInventory[0]['available_quantity'];
                    if($quantity < $item['quantity']) {
                        return $this->sendError('Có lỗi. Sách trong kho không đủ.', [], 409);       
                    }
                };
                // update stock
                foreach ($getQtyOrder as $item) {
                    $decrease= Inventory::where('book_id', $item['book_id'])
                                        ->decrement('available_quantity', $item['quantity']);
                };
            } else return $this->sendError('Có lỗi. Đơn đã được xác nhận.', [], 409);     
        } else if($order->status == 1) return $this->sendError('Có lỗi. Đơn chưa xác nhận.', [], 409); 
        
        $order->update(['status' => $fields['status']]);
        
        return $this->sendResponse('Đã cập nhật trạng thái đơn hàng thành công.',
                                    new OrderResource($order->load($this->query)),200); 
    }
   
}
