<?php

namespace App\Http\Controllers\Data;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\{Book, Category, Author};
use App\Http\Resources\Another\{
    Book as BookResource,
    BooksOfCategory as BooksOfCategoryResource,
    BooksOfAuthor as BooksOfAuthorResource,
    BookDetails as BookDetailsResource
};

class BookController extends BaseController
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getNewBook(Request $request)
    {
        $limit =  $request->input('limit', 10);
        $records = Book::query()
            ->select('id','name','author_id','description','unit_price','slug')
            ->take($limit)
            ->latest()
            ->get();
    
        return $this->sendResponse('Truy xuất top sách mới cập nhật thành công.',
                                    BookResource::collection($records),200); 
    }
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSellingBook(Request $request)
    {
        $limit =  $request->input('limit', 10);
        $records = Book::query()
            ->select('id','name','author_id','description','unit_price','slug')
            ->withSum('orders','order_details.quantity')
            ->orderByDesc('orders_sum_order_detailsquantity')
            ->take($limit)
            ->get();
       
        return $this->sendResponse('Truy xuất top sách bán chạy thành công.',
                                    BookResource::collection($records),200);
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
        return $this->sendResponse('Truy xuất chi tiết sách thành công.',
                                    new BookDetailsResource($book),200);  
    }
 /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getBookOfCategory($id)
    {
        $booksOfCategory = Category::query()
            ->select('id','name','slug')
            ->with('books')
            ->find($id);
  
        if (is_null($booksOfCategory)) {
            return $this->sendError('Không tìm thấy thể loại nào',[], 404); 
        }
        return $this->sendResponse('Truy xuất sách của thể loại thành công.',
                                    new BooksOfCategoryResource($booksOfCategory),200);
    }
     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getBookOfAuthor($id)
    {
        $booksOfAuthor = Author::query()
            ->select('id','name','description','slug')
            ->with(['books'])
            ->find($id);

        if (is_null($booksOfAuthor)) {
            return $this->sendError('Không tìm thấy tác giả nào',[], 404); 
        }
        return $this->sendResponse('Truy xuất sách của tác giả thành công.',
                                    new BooksOfAuthorResource($booksOfAuthor),200);
    }
}
