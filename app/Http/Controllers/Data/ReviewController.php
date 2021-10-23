<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\BaseController as BaseController;
use App\Models\{Book, Review};
use App\Http\Resources\Review as ReviewResource;
use DB;

class ReviewController extends BaseController
{
   /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getBookReview($id)
    {      
        $book = Book::find($id);      
        if (is_null($book)) {
            return $this->sendError('Không tìm thấy cuốn sách nào',[], 404); 
        }
        $review = $book->reviews()->get();
        return $this->sendResponse('Truy xuất đánh giá & xếp hạng sách thành công.',
                                    ReviewResource::collection($review),200);
    }
    public function getBookRating($id)
    {
        $book = Book::find($id);      
        if (is_null($book)) {
            return $this->sendError('Không tìm thấy cuốn sách nào',[], 404); 
        }
        $avgRating = $book->reviews()->avg('rating') ?? 0;
        $totalRating = $book->reviews()->count('rating');
        $starRating = $book->reviews()->select('rating', DB::raw('count(reviews.id) as amount'))
                           ->groupBy('reviews.rating')
                           ->orderBy('rating', 'desc')
                           ->get();
 
        $record['total'] = $totalRating;
        $record['average'] = $avgRating;   
        $record['stars'] = $starRating;

        return $this->sendResponse('Thống kê xếp hạng sách thành công.',
                                    $record,200);
    }
}
