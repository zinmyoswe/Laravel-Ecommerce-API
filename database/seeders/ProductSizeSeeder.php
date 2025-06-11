<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Size;

class ProductSizeSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        $sizeIds = Size::pluck('id')->toArray(); // Assuming 'id' is the PK for sizes

        foreach ($products as $product) {
            $assignedSizes = collect($sizeIds)->random(rand(2, 5))->unique();

            foreach ($assignedSizes as $sizeId) {
                DB::table('product_size')->insert([
                    'product_id' => $product->productid,
                    'size_id' => $sizeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
