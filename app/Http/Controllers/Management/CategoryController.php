<?php

namespace App\Http\Controllers\Management;

use Illuminate\Support\Str;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Category as CategoryResource;

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
        return CategoryResource::collection($records); 
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        $fields = $request->validated(); 
        $customValues = $fields + ['slug' => Str::slug($fields['name'])];

        $category = Category::create($customValues);
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
    public function update(CategoryRequest $request, $id)
    {
        $fields = $request->validated(); 
        $customValues = $fields + ['slug' => Str::slug($fields['name'])];

        $category = Category::find($id);
        if (is_null($category)) {
            return $this->sendError('Không tìm thấy thể loại',[], 404); 
        }

        $category->update($customValues);
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
    
}
