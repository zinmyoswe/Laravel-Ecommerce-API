<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $colors = ['red', 'blue', 'green', 'black', 'white', 'yellow', 'orange', 'indigo', 'purple', 'gray'];
        $genders = ['Men', 'Women', 'Kids'];

        // Example Pexels image set (you can customize these)
        $pexelsImages = [
            "https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg",
            "https://images.pexels.com/photos/428340/pexels-photo-428340.jpeg",
            "https://images.pexels.com/photos/298863/pexels-photo-298863.jpeg",
            "https://images.pexels.com/photos/2983464/pexels-photo-2983464.jpeg",
            "https://images.pexels.com/photos/2983465/pexels-photo-2983465.jpeg",
            "https://images.pexels.com/photos/2983466/pexels-photo-2983466.jpeg",
            "https://images.pexels.com/photos/6311398/pexels-photo-6311398.jpeg",
            "https://images.pexels.com/photos/6311397/pexels-photo-6311397.jpeg",
        ];

        for ($i = 1; $i <= 20; $i++) {
            $product = Product::create([
                'productname' => "Product $i",
                'productimage' => $pexelsImages[array_rand($pexelsImages)],
                'productimages' => array_slice($pexelsImages, 0, rand(3, 5)), // ✅ Stored as array
                'productvideo' => null,
                'category_id' => rand(1, 6),
                'subcategory_id' => rand(1, 11),
                'color' => $colors[array_rand($colors)],
                'price' => rand(20, 150),
                'discount' => rand(0, 25),
                'stock' => rand(10, 100),
                'description' => Str::random(100),
                'gender' => $genders[array_rand($genders)],
                'adminid' => 1,
                'sameproductid' => null,
            ]);
        }
    }
}
