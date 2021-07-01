<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publisher;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Publisher as PublisherResource;
use Validator;

class PublisherController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records =  Publisher::all();         
                return $this->sendResponse('Publishers list retrieved successfully.', PublisherResource::collection($records),200);  
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
            'description' => 'required|string|max:1000'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
        $publisher = Publisher::create([
            'name' => $fields['name'],
            'description' =>$fields['description']
        ]);
        return $this->sendResponse('Publisher create successfully.', new PublisherResource($publisher),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $publisher = Publisher::find($id);
  
        if (is_null($publisher)) {
            return $this->sendError('No publisher found',[], 404); 
        }
        return $this->sendResponse('Publisher retrieved successfully.', new PublisherResource($publisher),200);  
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
            'description' => 'required|string|max:1000'
        ]);
        $publisher = Publisher::find($id);
        if (is_null($publisher)) {
            return $this->sendError('No publisher found',[], 404); 
        }
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
        $publisher->update([
            'name' => $fields['name'],
            'description' =>$fields['description']
        ]);
        return $this->sendResponse('Publisher updated successfully.',  new PublisherResource($publisher),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $publisher = Publisher::find($id);
        if (is_null($publisher)) {
            return $this->sendError('No publisher found',[], 404); 
        }
        $publisher->delete();
        return $this->sendResponse('Publisher deleted successfully.', [],204);
    }
     /**
     * Search for a name
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        $publisher=  Publisher::where('name', 'like', '%'.$name.'%')->get();

        return $this->sendResponse('Found the results.', PublisherResource::collection($publisher),200);
    }
}
