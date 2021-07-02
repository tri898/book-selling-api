<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;
    protected $fillable = [
        'book_id', 
        'percent'
    ];
    public function book()
    {
        return $this->hasOne(Book::class,'id','book_id');
    }
}
