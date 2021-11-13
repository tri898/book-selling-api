<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\BaseController as BaseController;
use App\Models\{Category, Author};

class SelectiveDataController extends BaseController
{
    public function index()
    {
        $category = Category::orderByDesc('id')->get(['id','name', 'slug']);

        $author = Author::select(['id','name', 'slug'])
                    ->withSum('orderDetails as total_book_ordered','order_details.quantity')
                    ->orderByDesc('total_book_ordered')
                    ->get();
        $records = [
            'category' => $category,
            'author' => $author
        ];
        return $this->sendResponse('Lấy dữ liệu lựa chọn thành công',  $records,200);  
    }
}
