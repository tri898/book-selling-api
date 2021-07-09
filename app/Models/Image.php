<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    protected $fillable = [
        'book_id', 
        'front_cover',
        'back_cover'
    ];
    public function book()
    {
        return $this->hasOne(Book::class,'id','book_id');
    }
}
