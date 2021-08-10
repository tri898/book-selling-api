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
    public function getSellingBook()
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
    public function getBookDetails($id)
    {
        $book = Book::with(['image','bookCategory'])->find($id);
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
        $category = Category::with(['books'])->find($id);
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
        $author = Author::with(['books'])->find($id);
        if (is_null($author)) {
            return $this->sendError('Không tìm thấy tác giả nào',[], 404); 
        }
        return new BooksOfAuthorResource($author);
    }
}
