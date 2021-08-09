<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Book as BookResource;
use Validator;

class BookController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records =   Book::with(['bookCategory','image'])->get();
        return BookResource::collection($records); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields = $request->all();
        $validator = Validator::make($fields, [
            'name' => 'required|string|max:255|unique:books',
            'code' => 'required|string|max:255',
            'description' => 'required|string',
            'unit_price' => 'required|integer',
            'weight' => 'required|numeric|between:0.00,999.99',
            'format' => 'required|string',
            'release_date' => 'required|integer|min:1900|max:2090',
            'language' => 'required|string',
            'size' => 'required|string',
            'num_pages' => 'required|integer',
            'translator' => 'string|nullable',
            'author_id' => 'required|integer',
            'publisher_id' => 'required|integer',
            'supplier_id' => 'required|integer',
            'category_id' => 'required|integer',
            'front_cover' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'back_cover' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        $book = Book::create([
            'name' => $fields['name'],
            'code' => $fields['code'],
            'description' =>$fields['description'],
            'unit_price' =>$fields['unit_price'],
            'weight' =>$fields['weight'],
            'format' =>$fields['format'],
            'release_date' =>$fields['release_date'],
            'language' =>$fields['language'],
            'size' =>$fields['size'],
            'num_pages' =>$fields['num_pages'],
            'translator' =>$fields['translator'],
            'slug' => Str::slug($fields['name']),
            'author_id' =>$fields['author_id'],
            'publisher_id' =>$fields['publisher_id'],
            'supplier_id' =>$fields['supplier_id']
        ]);
        // insert book category
        $book->category()->attach($fields['category_id']);
        // insert quantity in inventory
        $book->inventory()->create(['available_quantity' => 0]);
        // get info image 
        $input['front_cover'] = date('YmdHis') . 'f'. '.' . $fields['front_cover']->getClientOriginalExtension();
        $input['back_cover'] = date('YmdHis') . 'b'. '.' . $fields['back_cover']->getClientOriginalExtension();
        // move image file to public
        $fields['front_cover']->move(public_path('images/books'), $input['front_cover']);
        $fields['back_cover']->move(public_path('images/books'), $input['back_cover']);
        //add image link to db
        $image = $book->image()->create($input);

        return $this->sendResponse('Tạo sách thành công.', new BookResource($book->load(['bookCategory','image'])),201);
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
    public function update(Request $request, $id)
    {
        $fields = $request->all();
        $validator = Validator::make($fields, [
            'name' => 'required|string|max:255|unique:books,name,' . $id,
            'code' => 'required|string|max:255',
            'description' => 'required|string',
            'unit_price' => 'required|integer',
            'weight' => 'required|numeric|between:0.00,999.99',
            'format' => 'required|string',
            'release_date' => 'required|integer|min:1900|max:2090',
            'language' => 'required|string',
            'size' => 'required|string',
            'num_pages' => 'required|integer',
            'translator' => 'string|nullable',
            'author_id' => 'required|integer',
            'publisher_id' => 'required|integer',
            'supplier_id' => 'required|integer',
            'category_id' => 'required|integer',
            'front_cover' => 'image|mimes:jpeg,png,jpg|max:2048',
            'back_cover' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $book = Book::find($id);
        if (is_null($book)) {
            return $this->sendError('Không tìm thấy cuốn sách nào',[], 404); 
        }
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        $book->update([
            'name' => $fields['name'],
            'code' => $fields['code'],
            'description' =>$fields['description'],
            'unit_price' =>$fields['unit_price'],
            'weight' =>$fields['weight'],
            'format' =>$fields['format'],
            'release_date' =>$fields['release_date'],
            'language' =>$fields['language'],
            'size' =>$fields['size'],
            'num_pages' =>$fields['num_pages'],
            'translator' =>$fields['translator'],
            'slug' => Str::slug($fields['name']),
            'author_id' =>$fields['author_id'],
            'publisher_id' =>$fields['publisher_id'],
            'supplier_id' =>$fields['supplier_id'],
        ]);
         // update book category
         $book->category()->sync($fields['category_id']);

        //If there is a file, it will be updated 
        if($request->hasFile('front_cover')) {
            $frontCover = date('YmdHis') . 'f' . '.' . $fields['front_cover']->getClientOriginalExtension();
            $fields['front_cover']->move(public_path('images/books'), $frontCover);
            $image = $book->image()->update(['front_cover' => $frontCover]);
        }

        if($request->hasFile('back_cover')) {
            $backCover = date('YmdHis') . 'b'. '.' . $fields['back_cover']->getClientOriginalExtension();
            $fields['back_cover']->move(public_path('images/books'), $backCover);
            $image = $book->image()->update(['back_cover' => $backCover]);
        }

        return $this->sendResponse('Đã cập nhật sách thành công.',  new BookResource($book->load(['bookCategory','image'])),200);
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
     
}
