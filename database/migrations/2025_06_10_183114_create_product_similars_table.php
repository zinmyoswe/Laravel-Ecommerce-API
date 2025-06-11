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
        Schema::create('product_similars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('similar_product_id');
            $table->timestamps();

            $table->foreign('product_id')->references('productid')->on('products')->onDelete('cascade');
            $table->foreign('similar_product_id')->references('productid')->on('products')->onDelete('cascade');
            $table->unique(['product_id', 'similar_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_similars');
    }
};
