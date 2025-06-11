<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('productid');
            $table->string('productname');
            $table->string('productimage');
            $table->json('productimages')->nullable();
            $table->string('productvideo')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('subcategory_id');
            $table->string('color', 50);;
            $table->decimal('price', 8, 2);
            $table->decimal('discount', 5, 2)->default(0.00);
            $table->integer('stock')->default(0);
            $table->text('description')->nullable();
            $table->enum('gender', ['Men', 'Women', 'Kids']);
            $table->unsignedBigInteger('adminid')->default(1);
            $table->unsignedBigInteger('sameproductid')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
