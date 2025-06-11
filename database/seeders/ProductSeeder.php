<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'];
        $colors = ['red', 'blue', 'black', 'navy', 'gray', 'darkgray', 'white', 'green', 'yellow', 'pink',
                   'purple', 'orange', 'maroon', 'beige', 'brown', 'olive', 'cyan', 'teal', 'indigo', 'lime'];

        $categories = Category::all();

        for ($i = 1; $i <= 20; $i++) {
            $category = $categories->random();

            Product::create([
                'productname' => "Product $i",
                'productimage' => "https://example.com/product_$i.jpg",
                'productimages' => [ // ✅ FIXED: no json_encode()
                    "https://example.com/product_{$i}_1.jpg",
                    "https://example.com/product_{$i}_2.jpg",
                ],
                'productvideo' => rand(0, 1) ? "https://example.com/video_$i.mp4" : null,
                'category_id' => $category->categoryid,
                'subcategory_id' => $category->subcategoryid,
                'size' => $sizes[array_rand($sizes)],
                'color' => $colors[array_rand($colors)],
                'price' => rand(1000, 5000),
                'discount' => rand(5, 30),
                'stock' => rand(10, 100),
                'description' => "Sample description for Product $i",
                'gender' => ['Men', 'Women', 'Kids'][array_rand(['Men', 'Women', 'Kids'])],
                'adminid' => 1,
                'sameproductid' => null,
            ]);
        }
    }
}
