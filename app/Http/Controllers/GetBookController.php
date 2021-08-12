<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;
use App\Models\Author;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Another\Book as BookResource;
use App\Http\Resources\Another\BooksOfCategory as BooksOfCategoryResource;
use App\Http\Resources\Another\BooksOfAuthor as BooksOfAuthorResource;
use App\Http\Resources\Another\BookDetails as BookDetailsResource;
use DB;

class GetBookController extends BaseController
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getNewBook()
    {
        $records = Book::query()
            ->select('id','name','author_id','description','unit_price','slug')
            ->orderByDesc('created_at')
            ->take(9)
            ->get();
    
        return BookResource::collection($records); 
    }
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSellingBook()
    {
        $records = Book::query()
            ->select('id','name','author_id','description','unit_price','slug')
            ->withSum('orders','order_details.quantity')
            ->orderByDesc('orders_sum_order_detailsquantity')
            ->take(9)
            ->get();
       
        return BookResource::collection($records); 
    }
      /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getBookDetails($id)
    {
        $book = Book::with(['bookCategory'])->find($id); 
        if (is_null($book)) {
            return $this->sendError('Không tìm thấy cuốn sách nào',[], 404); 
        }
        return new BookDetailsResource($book);  
    }
 /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getBookOfCategory($id)
    {
        DB::enableQueryLog();
        $category = Category::query()
            ->select('id','name','slug')
            ->with('books')
            ->find($id);
        $queries = DB::getQueryLog();
        print_r($queries);
        if (is_null($category)) {
            return $this->sendError('Không tìm thấy thể loại nào',[], 404); 
        }
        return new BooksOfCategoryResource($category);
    }
     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getBookOfAuthor($id)
    {
        $author = Author::query()
            ->select('id','name','description','slug')
            ->with(['books'])
            ->find($id);
        if (is_null($author)) {
            return $this->sendError('Không tìm thấy tác giả nào',[], 404); 
        }
        return new BooksOfAuthorResource($author);
    }
}
