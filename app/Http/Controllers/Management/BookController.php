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
        $records = Book::with(['bookCategory'])->get();
        return BookResource::collection($records); 
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
        $customValues = $fields + ['slug' => Str::slug($fields['name'])];
        
        $book = Book::create($customValues);
        // insert book category
        $book->category()->attach($fields['category_id']);
        // insert quantity in inventory
        $book->inventory()->create(['available_quantity' => 0]);
        // get image name
        $imageName['front_cover'] = $this->getNameAndMoveImageFile('front_cover', $request);
        $imageName['back_cover'] = $this->getNameAndMoveImageFile('back_cover', $request);

        //add image name  to (link)
        $image = $book->image()->create($imageName);

        return $this->sendResponse('Tạo sách thành công.', new BookResource($book->load(['bookCategory'])),201);
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
        $customValues = $fields + ['slug' => Str::slug($fields['name'])];

        $book = Book::find($id);
        if (is_null($book)) {
            return $this->sendError('Không tìm thấy cuốn sách nào',[], 404); 
        }

        $book->update($customValues);
         // update book category
         $book->category()->sync($fields['category_id']);

        //If there is a file, it will be updated 
        if($request->hasFile('front_cover')) {
            $frontCover = $this->getNameAndMoveImageFile('front_cover', $request);
            $image = $book->image()->update(['front_cover' => $frontCover]);
        }

        if($request->hasFile('back_cover')) {
            $backCover = $this->getNameAndMoveImageFile('back_cover', $request);
            $image = $book->image()->update(['back_cover' => $backCover]);
        }

        return $this->sendResponse('Đã cập nhật sách thành công.',  new BookResource($book->load(['bookCategory'])),200);
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
        // remove image book
        $image = $book->image()->first();
        if($image) {
            $front_cover = public_path('images/books' . $image->front_cover);
            $back_cover = public_path('images/books' . $image->back_cover);
            if(file_exists($front_cover) && file_exists($back_cover)) {
                unlink($front_cover);
                unlink($back_cover);
            }
        }
        $book->delete();
        return $this->sendResponse('Sách đã được xóa thành công.', [],204);
    }
    private function getNameAndMoveImageFile($face,BookRequest $request) {
        $image = $request->$face;
        if($face == "front_cover") {
            // get name image 
            $getName = date('YmdHis') . 'f'. '.' . $image->getClientOriginalExtension();
        }
        else
            // get name image 
            $getName = date('YmdHis') . 'b'. '.' . $image->getClientOriginalExtension();
        
        // move image file to public images/books
        $image->move(public_path('images/books'), $getName);
        return $getName;
    }
     
}
