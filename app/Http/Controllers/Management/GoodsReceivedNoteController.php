<?php

namespace App\Http\Controllers\Management;

use App\Models\{GoodsReceivedNote, Inventory};
use App\Http\Requests\GRNRequest;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\GoodsReceivedNote as GoodsReceivedNoteResource;

class GoodsReceivedNoteController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = GoodsReceivedNote::with('details')->orderByDesc('id')->get();    
             
        return $this->sendResponse('Truy xuất danh sách phiếu nhập thành công.',
                                    GoodsReceivedNoteResource::collection($records),200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GRNRequest $request)
    {
        $fields = $request->only(['supplier_id', 'total']);
        //information received note
        $currentIdAdmin = auth()->user()->id;
        $customValues = $fields + ['admin_id' => $currentIdAdmin];

        $goodsReceivedNote = GoodsReceivedNote::create($customValues);
        // get order details
        $grnDetails = [];
        foreach ($request->grnItems as $item) {   
            $grnDetails[$item['book_id']] = ['quantity' => $item['quantity'],
                                            'import_unit_price' => $item['import_unit_price']];       
            // update quantity in stock
            $increase= Inventory::where('book_id', $item['book_id'])
                                ->increment('available_quantity',  $item['quantity']);         
        }
        $goodsReceivedNote->books()->attach($grnDetails);

        return $this->sendResponse('Phiếu nhập tạo thành công.',
                                    new GoodsReceivedNoteResource($goodsReceivedNote->load('details')),201);
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
            $decrease= Inventory::where('book_id', $key)->decrement('available_quantity', $item);
        });
        $goodsReceivedNote->delete();
        return $this->sendResponse('Xóa phiếu nhập thành công', [],204);
    }
    
}
