<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 
        'address', 
        'phone', 
        'email', 
        'description',
        'slug', 

    ];
    public function books()
    {
        return $this->hasMany(Book::class,'supplier_id','id');
    }
    public function goodsReceivedNotes()
    {
        return $this->hasMany(GoodsReceivedNote::class,'supplier_id','id');
    }
}
