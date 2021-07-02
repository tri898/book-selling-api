<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id', 
        'book_id'
    ];
    public function category()
    {
        return $this->hasOne(Category::class,'id','category_id');
    }
    public function book()
    {
        return $this->hasOne(Book::class,'id','book_id');
    }
}
