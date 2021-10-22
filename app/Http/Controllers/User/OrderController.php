<?php

namespace App\Http\Controllers\User;

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
            $records = Order::where(['user_id'=> auth()->user()->id, 'status' => $type])
                              ->with('details')->orderByDesc('id')->get();
        } else {
            $records = Order::where('user_id',auth()->user()->id)
                              ->with('details')->orderByDesc('id')->get();   
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
        $order = Order::where('user_id', auth()->user()->id)->with('details')->find($id);
  
        if (is_null($order)) {
            return $this->sendError('Không tìm thấy đơn hàng',[], 404);
        }
        return new OrderResource($order);  
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderRequest $request)
    {
        //After validate then check books quantity in stock
        foreach ($request->items as $item) {
            $checkQuantity = Inventory::where('book_id', $item['book_id'])
                                        ->get('available_quantity');

            $quantity = $checkQuantity[0]['available_quantity'];
            if($quantity < $item['quantity']) {
            return $this->sendError('Có lỗi.Không thể thực hiện thao tác.', [], 409);       
           }
        } 
        // only get basic order information
        $fields = $request->only(['name', 'address', 'phone', 'total','shipping_fee','total_payment', 'note']);
        $fields['user_id'] = auth()->user()->id;
        $fields['status'] = 1;
        // store basic order information
        $order = Order::create($fields);
        // get order details
        $orderDetails = [];
        foreach ($request->items as $item) {
            $orderDetails[$item['book_id']] = ['quantity' => $item['quantity'],
                                              'unit_price' => $item['unit_price'],
                                              'sale_price' => $item['sale_price']];
            // update books quantity in stock
            $decrease= Inventory::where('book_id', $item['book_id'])
                                ->decrement('available_quantity',  $item['quantity']);
        }

        $order->books()->attach($orderDetails);  
        
        return $this->sendResponse('Tạo đơn hàng thành công.',
                                    new OrderResource($order->load('details')),201);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::where('user_id', auth()->user()->id)->where('status',1)->find($id);
        if (is_null($order)) {
            return $this->sendError('Có lỗi.Không thể thực hiện thao tác',[], 409); 
        }

        $order->update(['status' => 0]);

        return $this->sendResponse('Hủy đơn hàng thành công', [],204);
    }

}
