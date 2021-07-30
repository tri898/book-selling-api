<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Supplier;

use App\Http\Controllers\BaseController as BaseController;

class GetDataController extends BaseController
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = Category::get(['id','name']);
        $author = Author::get(['id','name']);
        $publisher = Publisher::get(['id','name']);
        $supplier = Supplier::get(['id','name']);

        $records = [
                'category' => $category,
                'author' => $author,
                'publisher' => $publisher,
                'supplier' => $supplier
        ];
        return $this->sendResponse('Lấy dữ liệu thành công',  $records,200);  
    }
}
