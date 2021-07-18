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
        $records =  Category::paginate(10);         
        return CategoryResource::collection($records); 
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
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        $category = Category::create([
            'name' => $fields['name'],
            'description' =>$fields['description'],
            'slug' => Str::slug($fields['name'])
        ]);
        return $this->sendResponse('Tạo thể loại thành công.',  new CategoryResource($category),201);
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
            return $this->sendError('Không tìm thấy thể loại',[], 404); 
        }
        return new CategoryResource($category);  
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
            return $this->sendError('Không tìm thấy thể loại',[], 404); 
        }
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        $category->update([
            'name' => $fields['name'],
            'description' =>$fields['description'],
            'slug' => Str::slug($fields['name'])
        ]);
        return $this->sendResponse('Đã cập nhật thể loại thành công',  new CategoryResource($category),200);
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
            return $this->sendError('Không tìm thấy thể loại',[], 404); 
        }
        if($category->books()->count()) {
            return $this->sendError('Không thể xóa do có liên kết đến sách.',[], 409); 
        }
        $category->delete();
        return $this->sendResponse(' Đã xóa thể loại thành công', [],204);
    }
     /**
     * Search for a name
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        $category=  Category::where('name', 'like', '%'.$name.'%')->paginate(10);

        return CategoryResource::collection($category);
    }
}
