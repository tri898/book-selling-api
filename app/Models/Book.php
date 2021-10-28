<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
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
        return $this->belongsToMany(Category::class ,'book_categories', 'book_id', 'category_id')->withTimestamps();
    }
    public function bookCategory()
    {
        return $this->hasOne(BookCategory::class,'book_id','id');
    }
    public function discount()
    {
        return $this->hasOne(Discount::class,'book_id','id');
    }
    public function slider()
    {
        return $this->hasOne(Slider::class,'book_id','id');
    }
    public function image()
    {
        return $this->hasOne(Image::class,'book_id','id');
    }
    public function inventory()
    {
        return $this->hasOne(Inventory::class,'book_id','id');
    }
    public function author()
    {
        return $this->belongsTo(Author::class,'author_id', 'id');
    }
    public function publisher()
    {
        return $this->belongsTo(Publisher::class,'publisher_id', 'id');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'supplier_id', 'id');
    }
    public function goodsReceivedNotes()
    {
        return $this->belongsToMany(GoodsReceivedNote::class ,'goods_received_note_details', 'book_id', 'goods_received_note_id');
    }
    public function orders()
    {
        return $this->belongsToMany(Order::class ,'order_details', 'book_id', 'order_id');
    }
    public function reviews()
    {
        return $this->hasManyThrough(Review::class, OrderDetail::class,'book_id', 'order_detail_id', 'id');
    }
    
}
