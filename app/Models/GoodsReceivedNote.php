<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceivedNote extends Model
{
    use HasFactory;
    protected $fillable = [
        'supplier_id',
        'admin_id',
        'total'
    ];
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'supplier_id','id');
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class,'admin_id','id');
    }
    public function books()
    {
        return $this->belongsToMany(Book::class ,'goods_received_note_details', 'goods_received_note_id', 'book_id')->withTimestamps();
    }
    public function details()
    {
        return $this->hasMany(GoodsReceivedNoteDetail::class,'goods_received_note_id','id');
    }
}
