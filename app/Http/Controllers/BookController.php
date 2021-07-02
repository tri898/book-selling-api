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
        $records =  Book::all();         
        return $this->sendResponse('Books list retrieved successfully.', BookResource::collection($records),200);  
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
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'unit_price' => 'required|integer',
            'weight' => 'required|numeric|between:0.00,999.99',
            'format' => 'required|string',
            'release_date' => 'required|string',
            'language' => 'required|string',
            'size' => 'required|string',
            'num_pages' => 'required|integer',
            'translator' => 'required|string',
            'author_id' => 'required|integer',
            'publisher_id' => 'required|integer',
            'supplier_id' => 'required|integer'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
        $book = Book::create([
            'name' => $fields['name'],
            'description' =>$fields['description'],
            'unit_price' =>$fields['unit_price'],
            'weight' =>$fields['weight'],
            'format' =>$fields['format'],
            'release_date' =>$fields['release_date'],
            'language' =>$fields['language'],
            'size' =>$fields['size'],
            'num_pages' =>$fields['num_pages'],
            'translator' =>$fields['translator'],
            'slug' => Str::slug($fields['name']) . '-' . Str::random(10),
            'author_id' =>$fields['author_id'],
            'publisher_id' =>$fields['publisher_id'],
            'supplier_id' =>$fields['supplier_id']
        ]);
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
            return $this->sendError('No book found',[], 404); 
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
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'unit_price' => 'required|integer',
            'weight' => 'required|numeric|between:0.00,999.99',
            'format' => 'required|string',
            'release_date' => 'required|string',
            'language' => 'required|string',
            'size' => 'required|string',
            'num_pages' => 'required|integer',
            'translator' => 'required|string',
            'author_id' => 'required|integer',
            'publisher_id' => 'required|integer',
            'supplier_id' => 'required|integer'
        ]);
        $book = Book::find($id);
        if (is_null($book)) {
            return $this->sendError('No book found',[], 404); 
        }
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
        $book->update([
            'name' => $fields['name'],
            'description' =>$fields['description'],
            'unit_price' =>$fields['unit_price'],
            'weight' =>$fields['weight'],
            'format' =>$fields['format'],
            'release_date' =>$fields['release_date'],
            'language' =>$fields['language'],
            'size' =>$fields['size'],
            'num_pages' =>$fields['num_pages'],
            'translator' =>$fields['translator'],
            'slug' => Str::slug($fields['name']) . '-' . Str::random(10),
            'author_id' =>$fields['author_id'],
            'publisher_id' =>$fields['publisher_id'],
            'supplier_id' =>$fields['supplier_id'],
        ]);
        return $this->sendResponse('Book updated successfully.',  new BookResource($book),200);
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
            return $this->sendError('No book found',[], 404); 
        }
        $book->delete();
        return $this->sendResponse('Book deleted successfully.', [],204);
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

        return $this->sendResponse('Found the results.', BookResource::collection($book),200);
    }
}
