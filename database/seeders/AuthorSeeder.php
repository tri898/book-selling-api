<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $authors = [
            ['Nguyên Phong','mô tả tác giả'],
            ['Nguyễn Nhật Ánh ','mô tả tác giả'],
            ['Tô Hoài','mô tả tác giả'],
            ['Dương Thụy','mô tả tác giả']
        ];
        foreach($authors as $item) {
            $author = Author::create([
                'name' => $item[0],
                'description' =>$item[1],
                'slug' => Str::slug($item[0])
            ]);
        }
      
    }
}
