<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    protected $fillable = [
        'available_quantity',
        'book_id'
    ];
    public function book()
    {
        return $this->hasOne(Book::class,'id','book_id');
    }
}
