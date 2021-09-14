<?php

namespace App\Http\Controllers\Management;

use Illuminate\Support\Str;
use App\Models\Author;
use App\Http\Requests\AuthorRequest;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Author as AuthorResource;

class AuthorController extends BaseController
{
        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function index() {
            $records =  Author::all();         
            return AuthorResource::collection($records);
        }
        /**
         * Store a newly created resource in storage.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
        public function store(AuthorRequest $request)
        {
            $fields = $request->validated(); 
            $customValues = $fields + ['slug' => Str::slug($fields['name'])];

            $author = Author::create($customValues);
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
    
       return new AuthorResource($author);  
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AuthorRequest $request, $id)
    {
        $fields = $request->validated(); 
        $customValues = $fields + ['slug' => Str::slug($fields['name'])];

        $author = Author::find($id);
        if (is_null($author)) {
            return $this->sendError('Không tìm thấy tác giả',[], 404); 
        }
    
        $author->update($customValues);
        return $this->sendResponse('Đã cập nhật tác giả thành công.', new AuthorResource($author),200);
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
        // check foreign key
        if($author->books()->count()) {
            return $this->sendError('Không thể xóa do có liên kết đến sách.',[], 409); 
        }
        
        $author->delete();
        return $this->sendResponse('Đã xóa tác giả thành công.', [],204);
    }

}
