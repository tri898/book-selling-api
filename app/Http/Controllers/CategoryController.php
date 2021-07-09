<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Category as CategoryResource;
use Validator;

class CategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records =  Category::all();         
        return $this->sendResponse('Categories list retrieved successfully.', CategoryResource::collection($records),200); 
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
            'name' => 'required|string|max:100|unique:categories',
            'description' => 'required|string|max:1000'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
        $category = Category::create([
            'name' => $fields['name'],
            'description' =>$fields['description'],
            'slug' => Str::slug($fields['name'])
        ]);
        return $this->sendResponse('Category create successfully.', new CategoryResource($category),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::find($id);
  
        if (is_null($category)) {
            return $this->sendError('No category found',[], 404); 
        }
        return $this->sendResponse('Category retrieved successfully.', new CategoryResource($category),200);  
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
            'name' => 'required|string|max:100|unique:categories,name,' . $id,
            'description' => 'required|string|max:1000'
        ]);
        $category = Category::find($id);
        if (is_null($category)) {
            return $this->sendError('No category found',[], 404); 
        }
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
        $category->update([
            'name' => $fields['name'],
            'description' =>$fields['description'],
            'slug' => Str::slug($fields['name'])
        ]);
        return $this->sendResponse('Category updated successfully.', new CategoryResource($category),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (is_null($category)) {
            return $this->sendError('No category found',[], 404); 
        }
        $category->delete();
        return $this->sendResponse('Category deleted successfully.', [],204);
    }
     /**
     * Search for a name
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        $category=  Category::where('name', 'like', '%'.$name.'%')->get();

        return $this->sendResponse('Found the results.', CategoryResource::collection($category),200);
    }
}
