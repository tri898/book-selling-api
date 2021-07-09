<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discount;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Discount as DiscountResource;
use Validator;

class DiscountController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = Discount::all();         
        return $this->sendResponse('Discount list retrieved successfully.', DiscountResource::collection($records),200); 
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
            'book_id' => 'required|integer|unique:discounts',
            'percent' => 'required|integer'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
        $discount = Discount::create([
            'book_id' => $fields['book_id'],
            'percent' =>$fields['percent']
        ]);
        return $this->sendResponse('Discount book create successfully.', new DiscountResource($discount),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $discount = Discount::find($id);
  
        if (is_null($discount)) {
            return $this->sendError('No discount book found',[], 404); 
        }
        return $this->sendResponse('Discount book retrieved successfully.', new DiscountResource($discount),200);
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
            'book_id' => 'required|integer|unique:discounts,book_id,' . $id,
            'percent' => 'required|integer'     
        ]);
        
        $discount = Discount::find($id);
        if (is_null($discount)) {
            return $this->sendError('No discount book category found',[], 404); 
        }

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
        
        $discount->update([
            'book_id' => $fields['book_id'],
            'percent' =>$fields['percent']
        ]);
        return $this->sendResponse('Discount book updated successfully.',  new DiscountResource($discount),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $discount = Discount::find($id);
        if (is_null($discount)) {
            return $this->sendError('No discount book found',[], 404); 
        }
        $discount->delete();
        return $this->sendResponse('Discount book deleted successfully.', [],204);
    }
    public function search($name)
    {
        // $book_category=  BookCategory::where('name', 'like', '%'.$name.'%')->get();

        // return $this->sendResponse('Found the results.', BookCategoryResource::collection($book_category),200);
    }
}
