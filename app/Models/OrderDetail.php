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
        'price',
        'discount'
    ];
    public function book()
    {
        return $this->belongsTo(Book::class,'book_id','id');
    }
}
