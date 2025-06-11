<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Subcategory;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categoryMap = [
            'Tshirts' => ['Longsleeve Tshirt', 'Shortsleeve Tshirt'],
            'Jacket' => ['Warm-up Jacket', 'Football Jacket', 'Lightweight Jacket'],
            'Pants' => ['Jogger & Sweatpants', 'Tights & Leggings', 'Trousers'],
            'Hoodies' => ['Crew Neck', 'Sweatshirts'],
            'Shorts' => ['Jogger & Sweatpants', 'Shorts', 'Tights & Leggings'],
        ];

        foreach ($categoryMap as $categoryName => $subNames) {
            foreach ($subNames as $subName) {
                $subcategory = Subcategory::where('subcategoryname', $subName)->first();
                if ($subcategory) {
                    Category::create([
                        'categoryname' => $categoryName,
                        'subcategoryid' => $subcategory->subcategoryid,
                    ]);
                }
            }
        }
    }
}
