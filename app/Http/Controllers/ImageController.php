<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Image as ImageResource;
use Validator;
class ImageController extends BaseController
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = Image::all();         
        return $this->sendResponse('Images book  list retrieved successfully.', ImageResource::collection($records),200); 
    }
    public function store(Request $request){
        $fields = $request->all();
        $validator = Validator::make($fields,[
    		'book_id' => 'required|integer|unique:images,book_id',
            'front_cover' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'back_cover' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
        $input['book_id'] = $request->book_id;
        $input['front_cover'] =$request->book_id . '-front'.'.'.$request->front_cover->getClientOriginalExtension();
        $input['back_cover'] = $request->book_id . '-back'.'.'.$request->back_cover->getClientOriginalExtension();

        $request->front_cover->move(public_path('images'), $input['front_cover']);
        $request->back_cover->move(public_path('images'), $input['back_cover']);

        $image = Image::create($input);

    	return $this->sendResponse('Image book create successfully.', new ImageResource($image),201);
      }
       /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $image = Image::find($id);
  
        if (is_null($image)) {
            return $this->sendError('No image book found',[], 404); 
        }
        return $this->sendResponse('Image book retrieved successfully.', new ImageResource($image),200);
    }
     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $image = Image::find($id);
        if (is_null($image)) {
            return $this->sendError('No image book found',[], 404); 
        }
        $image->delete();
        return $this->sendResponse('Image book deleted successfully.', [],204);
    }
}
