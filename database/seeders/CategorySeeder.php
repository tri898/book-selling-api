<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['Kinh tế','mô tả thể loại sách'],
            ['Văn học','mô tả thể loại sách'],
            ['Khoa học - Xã hội','mô tả thể loại sách'],
            ['Ngoại ngữ','mô tả thể loại sách'],
            ['Truyện ngắn','mô tả thể loại sách']

        ];
        foreach($categories as $item) {
            $categories = Category::create([
                'name' => $item[0],
                'description' =>$item[1],
                'slug' => Str::slug($item[0])
            ]);
        }
    }
}
