<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Order;
use App\Models\Inventory;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Order as OrderResource;
use Validator;

class OrderController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records =  Order::with('details')->get();         
        return OrderResource::collection($records);
    }
/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getOrders()
    {
        $getUserCurrent = auth()->user()->id;
        $records =  Order::where('user_id',$getUserCurrent)->with('details')->get();         
        return OrderResource::collection($records);
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
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|numeric|digits:10',
            'total' => 'required|integer',
            'note' => 'string|nullable|max:255',
            'orderItems' => 'required|array',
            'orderItems.*.book_id' => 'required|integer',
            'orderItems.*.quantity' => 'required|integer',
            'orderItems.*.price' => 'required|integer',
            'orderItems.*.discount' => 'required|integer',
        ]);
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }

        //check quantity book in stock
        foreach ($request->orderItems as $item) {
            $checkQuantity = Inventory::where('book_id', $item['book_id'])->get('available_quantity');
            $quantity = $checkQuantity[0]['available_quantity'];
           if($quantity < $item['quantity']) {
            return $this->sendError('Không thể thực hiện thao tác.', [], 400);       
           }
        }
        // get user current
        $getUserCurrent = auth()->user()->id;

        $order = Order::create([
            'user_id' => $getUserCurrent,
            'name' =>$fields['name'],
            'address' =>$fields['address'],
            'phone' =>$fields['phone'],
            'total' =>$fields['total'],
            'note' =>$fields['note']
        ]);
        // add list item to table detail
        foreach ($request->orderItems as $item) {
            $order->details()->create([
                'book_id' => $item['book_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'discount' => $item['discount']
            ]);
          // update quantity in stock
          $increase= Inventory::where('book_id', $item['book_id'])->decrement('available_quantity',  $item['quantity']);
        }
        return $this->sendResponse('Tạo đơn hàng thành công.',  new OrderResource($order->load('details')),201);
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showOrder($id)
    {
        $getUserCurrent = auth()->user()->id;
        $order = Order::where('user_id', $getUserCurrent)->with('details')->find($id);
  
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
    public function updateStatus(Request $request, $id)
    {
        $fields = $request->all();
        $validator = Validator::make($fields, [
            'status' => [
                'required',
                Rule::in(['Chờ xác nhận','Đã xác nhận','Đang giao','Giao thành công', 'Giao thất bại']),
            ]
        ]);
        $order = Order::find($id);
        if(is_null($order)) {
            return $this->sendError('Không tìm thấy đơn hàng',[], 404);
        }
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        // update status
        $order->update(['status' => $fields['status']]);
        return $this->sendResponse('Đã cập nhật trạng thái đơn hàng thành công.', new OrderResource($order->load('details')),200); 
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
            return $this->sendError('Không thể thực hiện',[], 400); 
        }
         // update quantity in stock
         $result = $order->details()->pluck('book_id','quantity');
         $result->each(function($key, $item) {
            $decrease= Inventory::where('book_id', $key)->increment('available_quantity', $item);
         });
        $order->delete();
        return $this->sendResponse('Hủy đơn hàng thành công', [],204);
    }
}
