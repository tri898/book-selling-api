<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Another\Book as BookResource;

class GetBookController extends BaseController
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getNewBook()
    {
        $records = Book::with('image')->take(9)->orderBy('created_at', 'desc')->get();
        return BookResource::collection($records); 
    }
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBestSellingBook()
    {
        $records = Book::with('image')->withCount('orders')->orderBy('orders_count', 'desc')->take(9)->get();
       
        return BookResource::collection($records);
    }
}
