<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteDetail;
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
            'supplier_id' => 'required|integer',
            'grnItems.*.book_id' => 'required|integer',
            'grnItems.*.quantity' => 'required|integer',
            'grnItems.*.import_unit_price' => 'required|integer'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
        $getCurrentAdmin = auth()->user()->id;

        $goodsReceivedNote = GoodsReceivedNote::create([
            'supplier_id' =>$fields['supplier_id'],
            'admin_id' => $getCurrentAdmin
        ]);
        foreach ($request->grnItems as $item) {
            $goodsReceivedNoteDetail = GoodsReceivedNoteDetail::create([
                'goods_received_note_id' => $goodsReceivedNote->id,
                'book_id' => $item['book_id'],
                'quantity' => $item['quantity'],
                'import_unit_price' => $item['import_unit_price']
            ]);
        }
        return $this->sendResponse('GRN create successfully.', new GoodsReceivedNoteResource($goodsReceivedNote),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $goodsReceivedNote = GoodsReceivedNote::find($id);
  
        if (is_null($goodsReceivedNote)) {
            return $this->sendError('No GRN found',[], 404); 
        }
        return $this->sendResponse('GRN retrieved successfully.', new GoodsReceivedNoteResource($goodsReceivedNote),200);  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $goodsReceivedNote = GoodsReceivedNote::find($id);
        if (is_null($goodsReceivedNote)) {
            return $this->sendError('No GRN found',[], 404); 
        }
        $goodsReceivedNote->delete();
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
        // $goodsReceivedNote=  GoodsReceivedNote::where('name', 'like', '%'.$name.'%')->get();

        // return $this->sendResponse('Found the results.', GoodsReceivedNoteResource::collection($goodsReceivedNote),200);
    }
}
