<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Author;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Author as AuthorResource;
use Validator;

class AuthorController extends BaseController
{
        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function index() {
            $records =  Author::all();         
                return $this->sendResponse('Danh sách tác giả được truy xuất thành công.', AuthorResource::collection($records),200);  
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
                'name' => 'required|string|unique:authors|max:100',
                'description' => 'required|string|max:1000'
            ]);
            if($validator->fails()){
                return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
            }
            $author = Author::create([
                'name' => $fields['name'],
                'description' =>$fields['description'],
                'slug' => Str::slug($fields['name'])
            ]);
            return $this->sendResponse('Tác giả được tạo thành công.', new AuthorResource($author),201);
        }
         /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $author = Author::find($id);
  
        if (is_null($author)) {
            return $this->sendError('Không tìm thấy tác giả.',[], 404); 
        }
        return $this->sendResponse('Tác giả được truy xuất thành công.', new AuthorResource($author),200);  
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
            'name' => 'required|string|max:100|unique:authors,name,' . $id,
            'description' => 'required|string|max:1000'
        ]);
        $author = Author::find($id);
        if (is_null($author)) {
            return $this->sendError('Không tìm thấy tác giả',[], 404); 
        }
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        $author->update([
            'name' => $fields['name'],
            'description' =>$fields['description'],
            'slug' => Str::slug($fields['name'])
        ]);
        return $this->sendResponse('Đã cập nhật tác giả thành công.',  new AuthorResource($author),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $author = Author::find($id);
        if (is_null($author)) {
            return $this->sendError('Không tìm thấy tác giả',[], 404); 
        }
        $author->delete();
        return $this->sendResponse('Đã xóa tác giả thành công.', [],204);
    }

     /**
     * Search for a name
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        $author=  Author::where('name', 'like', '%'.$name.'%')->get();

        return $this->sendResponse('Đã tìm thấy các kết quả.', AuthorResource::collection($author),200);
    }
}
