<?php

namespace App\Http\Controllers\Management;

use App\Models\{Category, Author, Publisher, Supplier};
use App\Http\Controllers\BaseController as BaseController;

class SelectiveDataController extends BaseController
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = Category::orderByDesc('id')->get(['id','name']);
        $author = Author::orderByDesc('id')->get(['id','name']);
        $publisher = Publisher::orderByDesc('id')->get(['id','name']);
        $supplier = Supplier::orderByDesc('id')->get(['id','name']);

        $records = [
                'category' => $category,
                'author' => $author,
                'publisher' => $publisher,
                'supplier' => $supplier
        ];
        return $this->sendResponse('Lấy dữ liệu lựa chọn thành công',  $records,200);  
    }
}
