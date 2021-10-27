<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'book_id', 
        'image'
    ];
    public function book()
    {
        return $this->belongsTo(Book::class,'book_id','id');
    }
}
