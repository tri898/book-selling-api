<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
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
        $records =  Order::with('details')->paginate(10);         
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
            'name' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|numeric|digits:10',
            'total' => 'required|integer',
            'note' => 'string|nullable',
            'orderItems' => 'required|array',
            'orderItems.*.book_id' => 'required|integer',
            'orderItems.*.quantity' => 'required|integer',
            'orderItems.*.price' => 'required|integer',
            'orderItems.*.discount' => 'required|integer',
        ]);
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        // get id admin current
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::find($id);
        if (is_null($order)) {
            return $this->sendError('Không tìm thấy đơn hàng',[], 404); 
        }
        $order->delete();
        return $this->sendResponse('Xóa đơn hàng thành công', [],204);
    }
}
