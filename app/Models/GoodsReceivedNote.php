<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceivedNote extends Model
{
    use HasFactory;
    protected $fillable = [
        'supplier_id',
        'admin_id'
    ];
    
    public function supplier()
    {
        return $this->hasOne(Supplier::class,'id','supplier_id');
    }
    public function admin()
    {
        return $this->hasOne(Admin::class,'id','admin_id');
    }
    public function details()
    {
        return $this->hasMany(GoodsReceivedNoteDetail::class,'goods_received_note_id','id');
    }
}
