<?php

namespace App\Http\Controllers\User;

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
    public function index()
    {
        $getUserCurrent = auth()->user()->id;
        $records =  Order::where('user_id',$getUserCurrent)->with('details')->get();         
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
        $getUserCurrent = auth()->user()->id;
        $order = Order::where('user_id', $getUserCurrent)->with('details')->find($id);
  
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
        //After validate then check quantity book in stock
        foreach ($request->orderItems as $item) {
            $checkQuantity = Inventory::where('book_id', $item['book_id'])->get('available_quantity');
            $quantity = $checkQuantity[0]['available_quantity'];
           if($quantity < $item['quantity']) {
            return $this->sendError('Có lỗi.Không thể thực hiện thao tác.', [], 409);       
           }
        }
        
        $fields = $request->only(['name', 'address', 'phone', 'total', 'note']);
        // userId current
        $UserIdCurrent = auth()->user()->id;
        $customValues = ['user_id' => $UserIdCurrent] + $fields;

        $order = Order::create($customValues);

        // add list item to table detail
        foreach ($request->orderItems as $item) {
            $order->details()->create([
                'book_id' => $item['book_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'discount' => $item['discount']
            ]);
        // update quantity in stock
        $decrease= Inventory::where('book_id', $item['book_id'])->decrement('available_quantity',  $item['quantity']);
        }
        return $this->sendResponse('Tạo đơn hàng thành công.',  new OrderResource($order->load('details')),201);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $getUserCurrent = auth()->user()->id;
        $order = Order::where('user_id', $getUserCurrent)->where('status','Chờ xác nhận')->find($id);
        if (is_null($order)) {
            return $this->sendError('Có lỗi.Không thể thực hiện thao tác',[], 409); 
        }
         // update quantity in stock
         $result = $order->details()->pluck('book_id','quantity');
         $result->each(function($key, $item) {
            $increase= Inventory::where('book_id', $key)->increment('available_quantity', $item);
         });

        $order->delete();

        return $this->sendResponse('Hủy đơn hàng thành công', [],204);
    }

}
