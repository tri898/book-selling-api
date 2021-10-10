<?php

namespace App\Http\Controllers\Management;

use Illuminate\Support\Str;
use App\Models\Book;
use App\Http\Requests\BookRequest;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Book as BookResource;

class BookController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = Book::orderByDesc('id')->get();
        return $this->sendResponse('Truy xuất danh sách sách thành công.',
                                    BookResource::collection($records),200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BookRequest $request)
    {
        $fields = $request->validated(); 
        $fields['slug'] = Str::slug($fields['name']);
        
        $book = Book::create($fields);
        // insert book category
        $book->category()->attach($fields['category_id']);
        // insert quantity in inventory
        $book->inventory()->create(['available_quantity' => 0]);


        //add image name  to (link)
        $image = $book->image()->create(['front_cover'=>$fields['front_cover'],
                                         'back_cover' =>$fields['back_cover']]);

        return $this->sendResponse('Tạo sách thành công.',
                                    new BookResource($book->load(['bookCategory'])),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = Book::with(['bookCategory','image'])->find($id);
    
        if (is_null($book)) {
            return $this->sendError('Không tìm thấy cuốn sách nào',[], 404); 
        }
        return new BookResource($book);  
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BookRequest $request, $id)
    {
        $fields = $request->validated(); 
        $fields['slug'] = Str::slug($fields['name']);

        $book = Book::find($id);
        if (is_null($book)) {
            return $this->sendError('Không tìm thấy cuốn sách nào',[], 404); 
        }

        $book->update($fields);
         // update book category
        $book->category()->sync($fields['category_id']);

        //Update image   
        $image = $book->image()->update(['front_cover'=>$fields['front_cover'],
                                         'back_cover' =>$fields['back_cover']]);

        return $this->sendResponse('Đã cập nhật sách thành công.',
                                    new BookResource($book->load(['bookCategory'])),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::find($id);
        if (is_null($book)) {
            return $this->sendError('Không tìm thấy cuốn sách nào',[], 404); 
        }

        // check foreign keys
        if($book->goodsReceivedNotes()->count()) {
            return $this->sendError('Không thể xóa do có liên kết đến phiếu nhập.',[], 409); 
        }
        if($book->discount()->count()) {
            return $this->sendError('Không thể xóa do có liên kết đến giảm giá.',[], 409); 
        }
        if($book->orders()->count()) {
            return $this->sendError('Không thể xóa do có liên kết đến đặt hàng.',[], 409); 
        }
        
        $book->delete();
        return $this->sendResponse('Sách đã được xóa thành công.', [],204);
    }
    
     
}
