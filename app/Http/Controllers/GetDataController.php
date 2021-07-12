<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Another\Book as BookResource;

class GetDataController extends BaseController
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records =  Book::get();         
        return $this->sendResponse('Danh sách sách được truy xuất thành công.',  BookResource::collection($records),200);  
    }
}
