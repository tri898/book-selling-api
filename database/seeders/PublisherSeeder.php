<?php

namespace Database\Seeders;

use App\Models\Publisher;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class PublisherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $publishers = [
            ['NXB Trẻ','mô tả nhà xuất bản'],
            ['NXB Hội Nhà Văn','mô tả nhà xuất bản'],
            ['NXB Văn học','mô tả nhà xuất bản'],
            ['NXB Kim Đồng','mô tả nhà xuất bản']
        ];
        foreach($publishers as $item) {
            $publishers = Publisher::create([
                'name' => $item[0],
                'description' =>$item[1]
            ]);
        }
    }
}
