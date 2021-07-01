<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceivedNote extends Model
{
    use HasFactory;
    protected $fillable = [
        'book_id', 
        'quantity', 
        'import_unit_price',
        'supplier_id',
        'created_by'
    ];
}
