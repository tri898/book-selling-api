<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 
        'description',
        'unit_price',
        'weight',
        'format',
        'release_date',
        'language',
        'size',
        'num_pages',
        'slug',
        'description',
        'translator',
        'author_id',
        'publisher_id',
        'supplier_id',

    ];
    public function category()
    {
        return $this->belongsToMany(Category::class ,'book_categories', 'book_id', 'category_id');
    }
    public function discount()
    {
        return $this->hasOne(Discount::class,'book_id');
    }
}
