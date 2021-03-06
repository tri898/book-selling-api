<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Models\{GoodsReceivedNote, Inventory};
use App\Http\Requests\GRNRequest;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\GoodsReceivedNote as GoodsReceivedNoteResource;

class GoodsReceivedNoteController extends BaseController
{
    private $query = [
        'supplier:id,name',
        'admin:id,name',
        'details.book:id,name'
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {    
        $type = $request->input('type');
        if($request->has('type')) {
            $records = GoodsReceivedNote::where('status',$type)
                                        ->with($this->query)
                                        ->orderByDesc('id')->get();
        } else {
            $records = GoodsReceivedNote::with($this->query)->orderByDesc('id')->get(); 
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
                                    new GoodsReceivedNoteResource(
                                    $goodsReceivedNote->load($this->query)),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $goodsReceivedNote = GoodsReceivedNote::with($this->query)
                                                ->find($id);
  
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
        $getQtyGRN = $goodsReceivedNote->details()->get(['book_id','quantity'])->toArray();
         // check quantity in stock
        foreach ($getQtyGRN as $item) {
            $getQtyInventory = Inventory::where('book_id', $item['book_id'])
                                        ->get('available_quantity');

            $quantity = $getQtyInventory[0]['available_quantity'];
            if($quantity < $item['quantity']) {
                return $this->sendError('Có lỗi. Sách trong kho không đủ.', [], 409);       
            }
        };
        // update quantity in stock
        foreach ($getQtyGRN as $item) {
            $decrease= Inventory::where('book_id', $item['book_id'])
                                  ->decrement('available_quantity', $item['quantity']);
        };
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
        $getQtyGRN = $goodsReceivedNote->details()->get(['book_id','quantity'])->toArray();
        foreach ($getQtyGRN as $item) {
            $increase= Inventory::where('book_id', $item['book_id'])
                                  ->increment('available_quantity', $item['quantity']);
        };
        $goodsReceivedNote->update(['status' => 1]);
        return $this->sendResponse('Hoàn tác phiếu nhập thành công', [],200);
    }
    
}
