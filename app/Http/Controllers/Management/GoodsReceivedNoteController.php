<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
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
    public function index(Request $request)
    {    
        $type = $request->input('type');
        if($request->has('type')) {
            $records = GoodsReceivedNote::where('status',$type)->with('details')
                                          ->orderByDesc('id')->get();
        } else {
            $records = GoodsReceivedNote::with('details')->orderByDesc('id')->get(); 
        }
          
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
        $fields = $request->only(['formality','supplier_id', 'total','note']);
        $fields['admin_id'] = auth()->user()->id;
        $fields['status'] = 1;

        $goodsReceivedNote = GoodsReceivedNote::create($fields);
        // get order details
        $grnDetails = [];
        foreach ($request->items as $item) {   
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

        return new GoodsReceivedNoteResource($goodsReceivedNote);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $goodsReceivedNote = GoodsReceivedNote::where('status',1)->find($id);
        if (is_null($goodsReceivedNote)) {
            return $this->sendError('Không tìm thấy phiếu nhập',[], 404); 
        }
        // update quantity in stock
        $result = $goodsReceivedNote->details()->pluck('book_id','quantity');
        $result->each(function($key, $item) {
            $decrease= Inventory::where('book_id', $key)->decrement('available_quantity', $item);
        });
        $goodsReceivedNote->update(['status' => 0]);
        return $this->sendResponse('Hủy phiếu nhập thành công', [],204);
    }
    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $goodsReceivedNote = GoodsReceivedNote::where('status',0)->find($id);
        if (is_null($goodsReceivedNote)) {
            return $this->sendError('Không tìm thấy phiếu nhập',[], 404); 
        }
        // update quantity in stock
        $result = $goodsReceivedNote->details()->pluck('book_id','quantity');
        $result->each(function($key, $item) {
            $increase= Inventory::where('book_id', $key)->increment('available_quantity', $item);
        });
        $goodsReceivedNote->update(['status' => 1]);
        return $this->sendResponse('Hoàn tác phiếu nhập thành công', [],200);
    }
    
}
