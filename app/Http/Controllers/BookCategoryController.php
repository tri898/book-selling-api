<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookCategory;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\BookCategory as BookCategoryResource;
use Validator;

class BookCategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = BookCategory::all();         
        return $this->sendResponse('Book categories list retrieved successfully.', BookCategoryResource::collection($records),200); 
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
            'category_id' => 'required|integer',
            'book_id' => 'required|integer|unique:book_categories'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
        $book_category = BookCategory::create([
            'category_id' => $fields['category_id'],
            'book_id' =>$fields['book_id']
        ]);
        return $this->sendResponse('Book category create successfully.', new BookCategoryResource($book_category),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book_category = BookCategory::find($id);
  
        if (is_null($book_category)) {
            return $this->sendError('No book category found',[], 404); 
        }
        return $this->sendResponse('Book category retrieved successfully.', new BookCategoryResource($book_category),200);
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
            'category_id' => 'required|integer',
            'book_id' => 'required|integer|unique:book_categories,book_id,' . $id
        ]);
        
        $book_category = BookCategory::find($id);
        if (is_null($book_category)) {
            return $this->sendError('No book category found',[], 404); 
        }

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
        
        $book_category->update([
            'category_id' => $fields['category_id'],
            'book_id' =>$fields['book_id']
        ]);
        return $this->sendResponse('BookCategory updated successfully.',  new BookCategoryResource($book_category),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book_category = BookCategory::find($id);
        if (is_null($book_category)) {
            return $this->sendError('No book category found',[], 404); 
        }
        $book_category->delete();
        return $this->sendResponse('Book category deleted successfully.', [],204);
    }
    
     /**
     * Search for a name
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        // $book_category=  BookCategory::where('name', 'like', '%'.$name.'%')->get();

        // return $this->sendResponse('Found the results.', BookCategoryResource::collection($book_category),200);
    }
}
