<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceivedNoteDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'goods_received_note_id', 
        'book_id', 
        'quantity', 
        'import_unit_price'
    ];
   
    public function book()
    {
        return $this->hasOne(Book::class,'id','book_id');
    }
}
