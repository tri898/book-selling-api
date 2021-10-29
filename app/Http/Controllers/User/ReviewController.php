<?php

namespace App\Http\Controllers\User;

use App\Models\{OrderDetail, Review};
use App\Http\Requests\ReviewRequest;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Review as ReviewResource;

class ReviewController extends BaseController
{
    private $query = [
        'orderDetails:id,order_id',
        'orderDetails.order:id,user_id',
        'orderDetails.order.user:id,name'
    ];
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReviewRequest $request)
    {
        $fields = $request->validated();

        $itemOrder = OrderDetail::find($fields['order_detail_id']);
        $order = $itemOrder->order()->get();
        $statusOrder = $order[0]['status'];
        $userIdOrder= $order[0]['user_id'];
        if($statusOrder != 4 || $userIdOrder != auth()->user()->id) {
            return $this->sendError('Có lỗi.Không thể thực hiện thao tác.', [], 409);  
        }

        $review = $itemOrder->review()->create($fields);
        $itemOrder->update(['review_status' => 1]);
        return $this->sendResponse('Tạo đánh giá & xếp hạng thành công.',
                                    new ReviewResource($review->load($this->query)),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $review = Review::where('order_detail_id',$id)
                          ->with($this->query)
                          ->first();
        if (is_null($review)) {
            return $this->sendError('Không tìm thấy đánh giá.',[], 404); 
        }
       
        return $this->sendResponse('Truy xuất đánh giá & xếp hạng thành công.',
                                    new ReviewResource($review),200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ReviewRequest $request, $id)
    {
        $fields = $request->validated();

        $review = Review::where('order_detail_id',$id)->first();
        if (is_null($review)) {
            return $this->sendError('Không tìm thấy đánh giá.',[], 404); 
        }
        $itemOrder = OrderDetail::find($id);
        $order = $itemOrder->order()->get();

        $userIdOrder= $order[0]['user_id'];
        if($userIdOrder != auth()->user()->id) {
            return $this->sendError('Có lỗi.Không thể thực hiện thao tác.', [], 409);  
        }
        $review->update($fields);
        return $this->sendResponse('Sửa đánh giá & xếp hạng thành công.',
                                    new ReviewResource($review->load($this->query)),200);
    }
}
