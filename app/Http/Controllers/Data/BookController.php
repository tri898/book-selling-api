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
use DB;

class BookController extends BaseController
{   
    private $query = [
        'id','name',
        'author_id',
        'description',
        'unit_price','slug'
    ];
    private $subQuery = [
        'author:id,name',
        'discount:book_id,percent',
        'image:book_id,front_cover'
    ];
   
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllBook()
    {
        $records = Book::query()
            ->select($this->query)
            ->with($this->subQuery)
            ->orderByDesc('id')
            ->paginate(12);
       
        return BookResource::collection($records); 
    }
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getNewBook(Request $request)
    {
        $limit =  $request->input('limit', 12);
        $records = Book::query()
            ->select($this->query)
            ->with($this->subQuery)
            ->take($limit)
            ->orderByDesc('id')
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
        $limit =  $request->input('limit', 12);
        $records = Book::query()
            ->select($this->query)
            ->with($this->subQuery)
            ->withSum('orders','order_details.quantity')
            ->orderByDesc('orders_sum_order_detailsquantity')
            ->take($limit)
            ->get();
       
        return $this->sendResponse('Truy xuất top sách bán chạy thành công.',
                                    BookResource::collection($records),200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRandomBook(Request $request)
    {
        $limit =  $request->input('limit', 12);
        $records = Book::query()
            ->select($this->query)
            ->with($this->subQuery)
            ->inRandomOrder()
            ->take($limit)
            ->orderByDesc('id')
            ->get();
    
        return $this->sendResponse('Truy xuất sách ngẫu nhiên.',
                                    BookResource::collection($records),200); 
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTheMostDiscountedBook()
    {
        $records = Book::query()
            ->select($this->query)
            ->with($this->subQuery)
            ->withMax('discount','percent')
            ->orderByDesc('discount_max_percent')
            ->limit(1)
            ->get();
       
        return $this->sendResponse('Sách giảm giá cao nhất.',
                                    BookResource::collection($records),200);
    }
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getHighlightAuthor()
    {
        $getIdAuthor = Book::query()
            ->select('author_id',
            DB::raw('count(id) as value'))
            ->orderByDesc('value')
            ->groupBy('author_id')
            ->limit(1)
            ->get();
        $id = $getIdAuthor[0]['author_id'];
        $author = Author::query()
                    ->select('id','name','description','slug','image')
                    ->find($id);
        $booksOfAuthor = Book::query()
            ->select(['id','name','slug'])
            ->WhereHas('author', function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->with(['image:book_id,front_cover'])
            ->limit(2)
            ->get();
        
        return [
            'author' => $author,
            'books' =>BookResource::collection($booksOfAuthor)
        ];
    }
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function bookSearch(Request $request)
    {
        $q =  $request->input('q');
        $records = Book::query()
            ->select($this->query)
            ->where('name', 'like','%'.$q.'%')
            ->orWhereHas('author', function ($query) use ($q) {
                $query->where('name', 'like', '%'.$q.'%');
                })
            ->with($this->subQuery)
            ->orderByDesc('id')
            ->paginate(12);
    
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
        $book = Book::find($id); 
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
        $category = Category::query()
            ->select('id','name','slug')
            ->find($id);

        $booksOfCategory = Book::query()
            ->select($this->query)
            ->WhereHas('category', function ($query) use ($id) {
                $query->where('categories.id', $id);
            })
            ->with($this->subQuery)
            ->orderByDesc('id')
            ->paginate(12);

        if (is_null($booksOfCategory)) {
            return $this->sendError('Không tìm thấy thể loại nào',[], 404); 
        }
        return [
            'category' => $category,
            'books' => BookResource::collection($booksOfCategory)->response()->getData(true)
        ];
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
                    ->select('id','name','description','slug','image')
                    ->find($id);
        $booksOfAuthor = Book::query()
            ->select($this->query)
            ->WhereHas('author', function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->with($this->subQuery)
            ->orderByDesc('id')
            ->paginate(12);

        if (is_null($booksOfAuthor)) {
            return $this->sendError('Không tìm thấy tác giả nào',[], 404); 
        }

        return [
            'author' => $author,
            'books' =>BookResource::collection($booksOfAuthor)->response()->getData(true)
        ];
    }
}
