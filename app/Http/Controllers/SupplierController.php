<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Supplier as SupplierResource;
use Validator;

class SupplierController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records =  Supplier::all();         
                return $this->sendResponse('Suppliers list retrieved successfully.', SupplierResource::collection($records),200);
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
            'name' => 'required|string|max:50|unique:suppliers',
            'address' => 'required|string|min:10|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'description' => 'required|string|max:1000'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
        $supplier = Supplier::create([
            'name' => $fields['name'],
            'address' => $fields['address'],
            'phone' => $fields['phone'],
            'email' => $fields['email'],
            'description' =>$fields['description'],
            'slug' => Str::slug($fields['name'])
        ]);
        return $this->sendResponse('Supplier create successfully.', new SupplierResource($supplier),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $supplier = Supplier::find($id);
  
        if (is_null($supplier)) {
            return $this->sendError('No supplier found',[], 404); 
        }
        return $this->sendResponse('Supplier retrieved successfully.', new SupplierResource($supplier),200);  
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
            'name' => 'required|string|max:50|unique:suppliers,name,' . $id,
            'address' => 'required|string|min:10|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'description' => 'required|string|max:1000'
        ]);
        $supplier = Supplier::find($id);
  
        if (is_null($supplier)) {
            return $this->sendError('No supplier found',[], 404); 
        }
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
        $supplier->update([
            'name' => $fields['name'],
            'address' => $fields['address'],
            'phone' => $fields['phone'],
            'email' => $fields['email'],
            'description' =>$fields['description'],
            'slug' => Str::slug($fields['name'])
        ]);
        return $this->sendResponse('Supplier updated successfully.',  new SupplierResource($supplier),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $supplier = Supplier::find($id);
        if (is_null($supplier)) {
            return $this->sendError('No supplier found',[], 404); 
        }
        $supplier->delete();
        return $this->sendResponse('Supplier deleted successfully.', [],204);
    }
     /**
     * Search for a name
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        $supplier=  Supplier::where('name', 'like', '%'.$name.'%')->get();

        return $this->sendResponse('Found the results.', SupplierResource::collection($supplier),200);
    }
}
