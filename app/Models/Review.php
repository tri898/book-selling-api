<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable = [
        'rating',
        'comment',
        'order_detail_id'
    ];
    public function orderDetails()
    {
        return $this->belongsTo(OrderDetail::class,'order_detail_id', 'id');
    }
}
