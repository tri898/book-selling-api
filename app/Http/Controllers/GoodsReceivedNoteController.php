<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $records =  GoodsReceivedNote::with('details')->get();         
        return GoodsReceivedNoteResource::collection($records);
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
            'total' => 'required|integer',
            'grnItems' => 'required|array',
            'grnItems.*.book_id' => 'required|integer',
            'grnItems.*.quantity' => 'required|integer',
            'grnItems.*.import_unit_price' => 'required|integer'
        ]);
        if($validator->fails()){
            return $this->sendError('Dữ liệu nhập lỗi.', $validator->errors(), 422);       
        }
        // get id admin current
        $getCurrentAdmin = auth()->user()->id;

        $goodsReceivedNote = GoodsReceivedNote::create([
            'supplier_id' =>$fields['supplier_id'],
            'admin_id' => $getCurrentAdmin,
            'total' =>$fields['total']
        ]);
        // add list item to table detail
        foreach ($request->grnItems as $item) {
            $goodsReceivedNote->details()->create([
                'book_id' => $item['book_id'],
                'quantity' => $item['quantity'],
                'import_unit_price' => $item['import_unit_price']
            ]);
            // update quantity in stock
            DB::table('inventories')->where('book_id', $item['book_id'])->increment('available_quantity',  $item['quantity']);
            
        }
        return $this->sendResponse('Phiếu nhập tạo thành công.', new GoodsReceivedNoteResource($goodsReceivedNote->load('details')),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $goodsReceivedNote = GoodsReceivedNote::with('details')->find($id);
  
        if (is_null($goodsReceivedNote)) {
            return $this->sendError('Không tìm thấy phiếu nhập',[], 404); 
        }

        return new  GoodsReceivedNoteResource($goodsReceivedNote);
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
            return $this->sendError('Không tìm thấy phiếu nhập',[], 404); 
        }
        // update quantity in stock
        $result = $goodsReceivedNote->details()->pluck('book_id','quantity');
        $result->each(function($key, $item) {
             DB::table('inventories')->where('book_id', $key)->decrement('available_quantity', $item);
        });
        $goodsReceivedNote->delete();
        return $this->sendResponse('Xóa phiếu nhập thành công', [],204);
    }
    
}
