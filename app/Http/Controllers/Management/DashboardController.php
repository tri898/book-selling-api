<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Dashboard as DashboardResource;
use App\Models\{Book, Order, User};
use Carbon\Carbon;

class DashboardController extends BaseController
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSellingBook(Request $request)
    {
        $limit = $request->input('limit', 5);
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $records = Book::query()
            ->select('id','name')
            ->withSum(['orders' => function ($query) use ($month, $year) {
                $query->whereMonth('orders.created_at', $month)
                      ->whereYear('orders.created_at', $year);                 
            }],'order_details.quantity')
            ->orderByDesc('orders_sum_order_detailsquantity')
            ->take($limit)
            ->get();

        return $this->sendResponse('Truy xuất top sách bán chạy và số lượng đã bán thành công.',
                                    DashboardResource::collection($records),200); 
    }

    public function getTotalOrdersInMonth(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $records = Order::whereMonth('created_at', $month)
                          ->whereYear('created_at', $year)
                          ->count();
        return $this->sendResponse('Truy xuất tổng đơn hàng trong tháng thành công.', $records,200);
                          
    }
    public function getTotalUsersInMonth(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $records = User::whereMonth('created_at', $month)
                          ->whereYear('created_at', $year)
                          ->count();
        return $this->sendResponse('Truy xuất tổng người dùng đăng ký mới trong tháng thành công.', $records,200);
                          
    }
}
