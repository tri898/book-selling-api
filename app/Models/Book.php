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
    protected $hidden = ['pivot'];
    public function category()
    {
        return $this->belongsToMany(Category::class ,'book_categories', 'book_id', 'category_id')->select('name','slug');
    }
    public function discount()
    {
        return $this->hasOne(Discount::class,'book_id','id')->select('percent');
    }
    public function author()
    {
        return $this->hasOne(Author::class,'id', 'author_id');
    }
    public function publisher()
    {
        return $this->hasOne(Publisher::class,'id', 'publisher_id');
    }
    public function supplier()
    {
        return $this->hasOne(Supplier::class,'id', 'supplier_id');
    }
}
