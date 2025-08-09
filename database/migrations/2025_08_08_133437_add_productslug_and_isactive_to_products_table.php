<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Product;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('productslug')->nullable()->after('productname');
            $table->boolean('isactive')->default(1)->after('productslug');
        });

        // Fill slugs for existing products
        $products = Product::all();
        foreach ($products as $product) {
            $slug = Str::slug($product->productname);
            // Ensure uniqueness by appending ID if needed
            if (Product::where('productslug', $slug)->exists()) {
                $slug .= '-' . $product->productid;
            }
            $product->update(['productslug' => $slug]);
        }

        // Now add unique index
        Schema::table('products', function (Blueprint $table) {
            $table->unique('productslug');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['productslug']);
            $table->dropColumn(['productslug', 'isactive']);
        });
    }
};
