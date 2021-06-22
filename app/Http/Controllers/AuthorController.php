<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Author as AuthorResource;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AuthorController extends BaseController
{
        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function index() {
            $records =  Author::all();         
                return $this->sendResponse('Authors list retrieved successfully.', AuthorResource::collection($records),200);  
        }
        /**
         * Store a newly created resource in storage.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
        public function store(Request $request)
        {
            $fields = $request->validate([
                'name' => 'required|string|unique:authors|max:100',
                'description' => 'required|string|max:1000'
            ]);
            $author = Author::create([
                'name' => $fields['name'],
                'description' =>$fields['description'],
                'slug' => Str::slug($fields['name'])
            ]);
            return $this->sendResponse('Author create successfully.', new AuthorResource($author),201);
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
            return 'Author not found.';
        }
        return $this->sendResponse('Author retrieved successfully.', new AuthorResource($author),200);  
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
        $fields = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:1000'
        ]);
        $author = Author::find($id);
        if (is_null($author)) {
            return 'Author not found.';
        }
        $author->update([
            'name' => $fields['name'],
            'description' =>$fields['description'],
            'slug' => Str::slug($fields['name'])
        ]);
        return $this->sendResponse('Author updated successfully.', $author,200);
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
            return 'Author not found.';
        }
        $author->delete();
        return $this->sendResponse('Author deleted successfully.', [],204);
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

        return $this->sendResponse('Found the results.', AuthorResource::collection($author),200);
    }
}
