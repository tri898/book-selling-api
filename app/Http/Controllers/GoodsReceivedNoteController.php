<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GoodsReceivedNote;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\GoodsReceivedNote as GoodsReceivedNoteResource;
use Validator;

class GoodsReceivedNoteController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records =  GoodsReceivedNote::all();         
        return $this->sendResponse('GRN list retrieved successfully.', GoodsReceivedNoteResource::collection($records),200); 
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
            'book_id' => 'required|integer',
            'quantity' => 'required|integer',
            'import_unit_price' => 'required|integer',
            'supplier_id' => 'required|integer',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
        $getCurrentAdmin = auth()->user()->name;
        $received_note = GoodsReceivedNote::create([
            'book_id' => $fields['book_id'],
            'quantity' =>$fields['quantity'],
            'import_unit_price' =>$fields['import_unit_price'],
            'supplier_id' =>$fields['supplier_id'],
            'created_by' => $getCurrentAdmin
        ]);
        return $this->sendResponse('GRN create successfully.', new GoodsReceivedNoteResource($received_note),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $received_note = GoodsReceivedNote::find($id);
  
        if (is_null($received_note)) {
            return $this->sendError('No GRN found',[], 404); 
        }
        return $this->sendResponse('GRN retrieved successfully.', new GoodsReceivedNoteResource($received_note),200);  
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
            'book_id' => 'required|integer',
            'quantity' => 'required|integer',
            'import_unit_price' => 'required|integer',
            'supplier_id' => 'required|integer',
        ]);
        $received_note = GoodsReceivedNote::find($id);
        if (is_null($received_note)) {
            return $this->sendError('No GRN found',[], 404); 
        }
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
        $received_note->update([
            'book_id' => $fields['book_id'],
            'quantity' =>$fields['quantity'],
            'import_unit_price' =>$fields['import_unit_price'],
            'supplier_id' =>$fields['supplier_id'],
        ]);
        return $this->sendResponse('GRN updated successfully.',  new GoodsReceivedNoteResource($received_note),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $received_note = GoodsReceivedNote::find($id);
        if (is_null($received_note)) {
            return $this->sendError('No GRN found',[], 404); 
        }
        $received_note->delete();
        return $this->sendResponse('GRN deleted successfully.', [],204);
    }
     /**
     * Search for a name
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        // $received_note=  GoodsReceivedNote::where('name', 'like', '%'.$name.'%')->get();

        // return $this->sendResponse('Found the results.', GoodsReceivedNoteResource::collection($received_note),200);
    }
}
