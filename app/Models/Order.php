<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'address',
        'phone',
        'total',
        'note',
        'status'
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function books()
    {
        return $this->belongsToMany(Book::class ,'order_details', 'order_id', 'book_id')->withTimestamps();
    }
    public function details()
    {
        return $this->hasMany(OrderDetail::class,'order_id','id');
    }
}
