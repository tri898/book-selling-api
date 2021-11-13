<?php

namespace App\Http\Controllers\Shipper;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Requests\OrderRequest;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Order as OrderResource;

class OrderController extends BaseController
{
    private $query = [
        'details.book:id,name'
    ];
    public function index()
    {
        $records = Order::where('status',3)
                        ->with($this->query)
                        ->orderByDesc('id')->get();
               
        return $this->sendResponse('Truy xuất các đơn hàng đã giao cho đơn vị vận chuyển thành công.',
                                    OrderResource::collection($records),200);
    }
    public function update(OrderRequest $request,$id)
    {
        $fields = $request->validated(); 

        $order = Order::whereIn('status', [3])->find($id);
        if(is_null($order)) {
            return $this->sendError('Không tìm thấy đơn hàng',[], 404);
        }
        $order->update(['status' => $fields['status']]);
               
        return $this->sendResponse('Đã cập nhật trạng thái đơn hàng thành công.',
                                    new OrderResource($order->load($this->query)),200);
    }
}
