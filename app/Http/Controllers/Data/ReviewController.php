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
        $review = Book::find($id)->reviews()->get();

        return $this->sendResponse('Truy xuất đánh giá & xếp hạng sách thành công.',
                                    ReviewResource::collection($review),200);
    }
    public function getBookRating($id)
    {
        $avgRating = Book::find($id)->reviews()->avg('rating') ?? 0;
        $totalRating = Book::find($id)->reviews()->count('rating');
        $starRating = Book::find($id)->reviews()->select('rating', DB::raw('count(reviews.id) as amount'))
        ->groupBy('reviews.rating')
        ->orderBy('rating', 'desc')
        ->get();

        $review = Book::find($id)->reviews()->get();
        $record['total'] = $totalRating;
        $record['average'] = $avgRating;   
        $record['stars'] = $starRating;

        return $this->sendResponse('Truy xuất thống kê xếp hạng sách thành công.',
                                    $record,200);
    }
}
