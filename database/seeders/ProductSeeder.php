<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $pexelsImages = [
            'https://images.pexels.com/photos/428340/pexels-photo-428340.jpeg',
            'https://images.pexels.com/photos/298863/pexels-photo-298863.jpeg',
            'https://images.pexels.com/photos/2983464/pexels-photo-2983464.jpeg',
            'https://images.pexels.com/photos/2983465/pexels-photo-2983465.jpeg',
            'https://images.pexels.com/photos/2983466/pexels-photo-2983466.jpeg',
            'https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg',
            'https://images.pexels.com/photos/298864/pexels-photo-298864.jpeg',
            'https://images.pexels.com/photos/614020/pexels-photo-614020.jpeg',
            'https://images.pexels.com/photos/298866/pexels-photo-298866.jpeg',
            'https://images.pexels.com/photos/298868/pexels-photo-298868.jpeg',
        ];

        $colors = ['red', 'blue', 'black', 'navy', 'gray', 'darkgray', 'white', 'green', 'yellow', 'pink', 'purple', 'orange', 'maroon', 'beige', 'brown', 'olive', 'cyan', 'teal', 'indigo', 'lime'];
        $genders = ['Men', 'Women', 'Kids'];

        $categories = Category::all();
        $subcategories = Subcategory::all();

        for ($i = 1; $i <= 20; $i++) {
            $category = $categories->random();
            $subcategory = $subcategories->random();

            $mainImage = $pexelsImages[array_rand($pexelsImages)];
            $additionalImages = array_slice($pexelsImages, 0, rand(2, 5));

            Product::create([
                'productname' => 'Product ' . $i,
                'productimage' => $mainImage,
                'productimages' => json_encode($additionalImages),
                'productvideo' => null,
                'category_id' => $category->categoryid,
                'subcategory_id' => $subcategory->subcategoryid,
                'color' => $colors[array_rand($colors)],
                'price' => rand(10, 100),
                'discount' => rand(0, 20),
                'stock' => rand(10, 100),
                'description' => Str::random(100),
                'gender' => $genders[array_rand($genders)],
                'adminid' => 1,
                'sameproductid' => null,
            ]);
        }
    }
}
