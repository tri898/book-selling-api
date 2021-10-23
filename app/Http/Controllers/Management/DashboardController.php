<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\{
    SellingBook as SellingBookResource,
    BookStatistic as BookStatisticResource,
};
use App\Models\{Book, Order, User, GoodsReceivedNote};
use Carbon\Carbon;
use DB;

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

        return $this->sendResponse('Top sách bán chạy và số lượng đã bán trong tháng.',
                                    SellingBookResource::collection($records),200); 
    }

    public function getOrderStatisticsInMonth(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $records = Order::select('status', DB::raw('count(id) as total_orders'),
                          DB::raw('sum(total) as total_income'))
                          ->whereMonth('created_at', $month)
                          ->whereYear('created_at', $year)
                          ->groupBy('status')
                          ->get();

        return $this->sendResponse('Tổng số đơn và giá trị đơn hàng theo trạng thái trong tháng.',
                                    $records,200);
                          
    }
    public function getUserStatisticsInMonth(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $records = User::whereMonth('created_at', $month)
                          ->whereYear('created_at', $year)
                          ->count();
        return $this->sendResponse('Truy xuất tổng người dùng đăng ký mới trong tháng thành công.',
                                    $records,200);
                          
    }
    public function getGRNStatisticsInMonth(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $records = GoodsReceivedNote::select('formality', DB::raw('count(id) as total_import'),
                                            DB::raw('sum(total) as total_income'))
                                            ->whereMonth('created_at', $month)
                                            ->whereYear('created_at', $year)
                                            ->whereStatus(1)
                                            ->groupBy('formality')
                                            ->get();
        return $this->sendResponse('Tổng đơn nhập kho và giá trị của nhập mới/nhập lại kho trong tháng.',
                                    $records,200);
                          
    }
    public function getBookStatistics()
    {
        $records = Book::query()
                        ->select('id','name')
                        ->withSum('goodsReceivedNotes as import','goods_received_note_details.quantity')
                        ->orderByDesc('import')
                        ->get();
        return $this->sendResponse('Số lượng sách đã nhập và còn lại trong kho.',
                                    BookStatisticResource::collection($records),200); 
    }
}
