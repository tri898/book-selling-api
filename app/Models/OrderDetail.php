<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id', 
        'book_id', 
        'quantity', 
        'unit_price',
        'sale_price',
        'review_status'
    ];
    public function book()
    {
        return $this->belongsTo(Book::class,'book_id','id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class,'order_id','id');
    }
    public function review()
    {
        return $this->hasOne(Review::class,'order_detail_id','id');
    }
    
}
