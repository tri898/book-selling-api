<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $suppliers = [
            ['NXB Trẻ','57 Bùi Thị Xuân Q TB TPHCM','0351247521','contact@nxbtre.com','Mô tả nhà cung cấp'],
            ['Nhã Nam','124 Nguyễn Thị Lựu Q 12 TPHCM','0851324587','info@nhanambook.vn','Mô tả nhà cung cấp'],
            ['Sky Books','60a Lê Đại Lộ Q8 TPHCM','0751245102','contact@skybooks.com','Mô tả nhà cung cấp'],      
        ];
        foreach($suppliers as $item) {
            $suppliers = Supplier::create([
                'name' => $item[0],
                'address' => $item[1],
                'phone' => $item[2],
                'email' => $item[3],
                'description' =>$item[4],
                'slug' => Str::slug($item[0])
            ]);
        }
    }
}
