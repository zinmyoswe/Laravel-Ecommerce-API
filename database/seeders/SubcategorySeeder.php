<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subcategory;

class SubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subcategories = [
            'Longsleeve Tshirt',
            'Shortsleeve Tshirt',
            'Warm-up Jacket',
            'Football Jacket',
            'Lightweight Jacket',
            'Jogger & Sweatpants',
            'Tights & Leggings',
            'Trousers',
            'Crew Neck',
            'Sweatshirts',
            'Shorts',
        ];

        foreach ($subcategories as $name) {
            Subcategory::create(['subcategoryname' => $name]);
        }
    }
}
