<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Image;
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
        $records =  Book::all();         
        return $this->sendResponse('Danh sách sách được truy xuất thành công.', BookResource::collection($records),200);  
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
            'name' => 'required|string|max:100|unique:books',
            'code' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
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
        
        $bookCategory = BookCategory::create([
            'category_id' => $fields['category_id'],
            'book_id' =>$book->id
        ]);
        // get info image
        $input['book_id'] = $book->id;
        $input['front_cover'] =$book->id . '-front'.'.'.$fields['front_cover']->getClientOriginalExtension();
        $input['back_cover'] = $book->id . '-back'.'.'.$fields['back_cover']->getClientOriginalExtension();
        // move image file to public
        $fields['front_cover']->move(public_path('images'), $input['front_cover']);
        $fields['back_cover']->move(public_path('images'), $input['back_cover']);

        $image = Image::create($input);

        return $this->sendResponse('Book create successfully.', new BookResource($book),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = Book::find($id);
  
        if (is_null($book)) {
            return $this->sendError('Không tìm thấy cuốn sách nào',[], 404); 
        }
        return $this->sendResponse('Book retrieved successfully.', new BookResource($book),200);  
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
            'name' => 'required|string|max:100|unique:books,name,' . $id,
            'code' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
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
        $bookCategory = BookCategory::where('book_id',$book->id)->first();
        if($bookCategory) {
            $bookCategory->update(['category_id' => $fields['category_id']]);
        }
        //If there is a file, it will be updated 
        if($request->hasFile('front_cover') & $request->hasFile('back_cover')) {
            // get info image
         $input['front_cover'] =$book->id . '-front'.'.'.$fields['front_cover']->getClientOriginalExtension();
         $input['back_cover'] = $book->id . '-back'.'.'.$fields['back_cover']->getClientOriginalExtension();
         // move image file to public
         $fields['front_cover']->move(public_path('images'), $input['front_cover']);
         $fields['back_cover']->move(public_path('images'), $input['back_cover']);
        }
        return $this->sendResponse('Đã cập nhật sách thành công.',  new BookResource($book),200);
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
        $image =Image::where('book_id',$book->id)->first();
        if($image) {
            $front_cover = public_path('images/' . $image->front_cover);
            $back_cover = public_path('images/' . $image->back_cover);
            if(file_exists($front_cover) & file_exists($back_cover)) {
                unlink($front_cover);
                unlink($back_cover);
            }
        }
        $book->delete();
        return $this->sendResponse('Sách đã được xóa thành công.', [],204);
    }
      /**
     * Search for a name
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        $book=  Book::where('name', 'like', '%'.$name.'%')->get();

        return $this->sendResponse('Đã tìm thấy các kết quả.', BookResource::collection($book),200);
    }
}
