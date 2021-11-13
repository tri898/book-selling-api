<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 
        'description',
        'slug',
        'image'
    ];
    public function books()
    {
        return $this->hasMany(Book::class,'author_id','id');
    }
    public function orderDetails()
    {
        return $this->hasManyThrough(OrderDetail::class, Book::class,'author_id', 'book_id', 'id');
    }
}
