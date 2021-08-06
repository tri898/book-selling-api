<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Another\Book as BookResource;
use App\Http\Resources\Book as BookResourceDetail;

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
      /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getBookDetail($id)
    {
        // $book = Book::with(['inventory', 'image','category'])->find($id)->makeHidden('pivot');
    
        // if (is_null($book)) {
        //     return $this->sendError('Không tìm thấy cuốn sách nào',[], 404); 
        // }
        // return new BookResourceDetail($book);  
    }

}
