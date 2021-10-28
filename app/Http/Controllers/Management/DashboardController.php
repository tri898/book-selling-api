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
                        ->with('inventory')
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

    public function getTotalIncomeInMonth(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

         $totalImport = GoodsReceivedNote::select(DB::raw('sum(total) as total_import'))
                                        ->whereMonth('created_at', $month)
                                        ->whereYear('created_at', $year)
                                        ->whereFormality(1)
                                        ->whereStatus(1)
                                        ->get();
        $totalExport = Order::select(DB::raw('sum(total) as total_export'))
                          ->whereMonth('created_at', $month)
                          ->whereYear('created_at', $year)
                          ->whereStatus(4)
                          ->get();
        $totalIncome = $totalExport[0]['total_export'] - $totalImport[0]['total_import'];                
        $response = [
            'total_import' => $totalImport[0]['total_import'] ?? 0,
            'total_export' => $totalExport[0]['total_export'] ?? 0,
            'total_income' => $totalIncome
        ];
        return $this->sendResponse('Tổng nhập,tổng xuất, lợi nhuận trong tháng.',
                                    $response,200);
                          
    }
    public function getTotalOrderInMonth(Request $request)
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
    public function getUserStatistics(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);

        $records = User::select(DB::raw('MONTH(created_at) as code'),
                        DB::raw('count(id) as value'))
                        ->whereYear('created_at', $year)
                        ->groupBy('code')                   
                        ->get()->toArray();
       
        $userArray = $this->ConvertToArray($records,12);
        
        return $this->sendResponse('Truy xuất tổng người dùng đăng ký mới trong năm thành công.',
                                    json_encode($userArray),200);
                          
    }
    public function getGRNStatistics(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);

        $records = GoodsReceivedNote::select('formality',DB::raw('MONTH(created_at) as code'),
                                      DB::raw('count(id) as value'))
                                      ->whereYear('created_at', $year)
                                      ->whereStatus(1);
        $recordsClone =  clone $records;                                                 
        $import = $records->whereFormality(1)->groupBy('code')->get();
        $reimport = $recordsClone->whereFormality(2)->groupBy('code')->get();
        
        $response = [
            'import' => json_encode($this->ConvertToArray($import,12)),
            'reimport' => json_encode($this->ConvertToArray($reimport,12)),
        ];
        return $this->sendResponse('Tổng đơn nhập kho của nhập mới/nhập lại kho trong năm.',
                                    $response,200);
                          
    }
    public function getBookStatistics()
    {
        $records = Book::query()
                        ->select('id','name')
                        ->with('inventory')
                        ->withSum('goodsReceivedNotes as import','goods_received_note_details.quantity')
                        ->orderByDesc('import')
                        ->get();
        return $this->sendResponse('Số lượng sách đã nhập và còn lại trong kho.',
                                    BookStatisticResource::collection($records),200); 
    }
    
}
